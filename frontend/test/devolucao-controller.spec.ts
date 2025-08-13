import { describe, it, expect, vi, beforeEach } from "vitest";
import { DevolucaoController } from "../src/devolucao/DevolucaoController";

class MockView {
    exibirDevolucao = vi.fn();
    exibirResumo = vi.fn();
    atualizarItensEResumo = vi.fn();
    getForm = vi.fn(() => ({
        addEventListener: vi.fn(),
    }));
    getFuncionarioId = vi.fn(() => 1);
    getItensComAvarias = vi.fn(() => []);
    isLimpezaAplicada = vi.fn(() => false);
}
class MockManager {
    carregaValorHoraItemLocacao = vi.fn(async () => [{ id: 1, valorHora: 10 }]);
    enviarDevolucao = vi.fn(async () => {});
    carregarFuncionarios = vi.fn(async () => [{ id: 1, nome: "Funcionario" }]);
}

describe("DevolucaoController -> Funcionalidades", () => {
    let view: any;
    let manager: any;

    beforeEach(() => {
        view = new MockView();
        manager = new MockManager();
        localStorage.clear();
        vi.clearAllMocks();
    });

    it("carregarFuncionarios retorna lista de funcionários", async () => {
        const controller = new DevolucaoController(view, manager);
        const funcionarios = await controller.carregarFuncionarios();
        expect(funcionarios).toEqual([{ id: 1, nome: "Funcionario" }]);
    });

    it("carregarFuncionarios retorna [] e alerta em erro", async () => {
        manager.carregarFuncionarios = vi.fn(async () => {
            throw new Error("Falha");
        });
        globalThis.alert = vi.fn();
        const controller = new DevolucaoController(view, manager);
        const funcionarios = await controller.carregarFuncionarios();
        expect(funcionarios).toEqual([]);
        expect(globalThis.alert).toHaveBeenCalled();
    });

    it("inicializaDevolucao alerta e redireciona se não houver devolucaoEmCurso", async () => {
        globalThis.alert = vi.fn();
        globalThis.window = Object.assign(globalThis.window || {}, {
            location: { assign: vi.fn() },
        });
        new DevolucaoController(view, manager);
        await new Promise((r) => setTimeout(r, 60));
        expect(globalThis.alert).toHaveBeenCalledWith(
            "Nenhuma devolução em andamento.",
        );
        expect(globalThis.window.location.assign).toHaveBeenCalledWith(
            "/listarlocacao",
        );
    });

    it("enviarDevolucao alerta se funcionário não autenticado", async () => {
        localStorage.setItem(
            "devolucaoEmCurso",
            JSON.stringify({
                locacaoId: 1,
                clienteNome: "Cliente",
                clienteCpf: "123",
                dataHoraEntregaPrevista: "2023-10-01 14:00",
                locacaoHoras: 2,
                itens: [{ id: 1, valor_hora: 10 }],
            }),
        );
        view.getFuncionarioId = vi.fn(() => 0);
        globalThis.alert = vi.fn();
        const controller = new DevolucaoController(view, manager);
        await controller["enviarDevolucao"]();
        expect(globalThis.alert).toHaveBeenCalledWith(
            "Funcionário não autenticado.",
        );
    });

    it("enviarDevolucao alerta erro de backend", async () => {
        localStorage.setItem(
            "devolucaoEmCurso",
            JSON.stringify({
                locacaoId: 1,
                clienteNome: "Cliente",
                clienteCpf: "123",
                dataHoraEntregaPrevista: "2023-10-01 14:00",
                locacaoHoras: 2,
                itens: [{ id: 1, valor_hora: 10 }],
            }),
        );
        manager.enviarDevolucao = vi.fn(async () => {
            throw new Error("Falha backend");
        });
        view.getFuncionarioId = vi.fn(() => 1);
        globalThis.alert = vi.fn();
        const controller = new DevolucaoController(view, manager);
        await controller["enviarDevolucao"]();
        expect(globalThis.alert).toHaveBeenCalledWith(
            expect.stringContaining("Erro ao registrar devolução"),
        );
    });
});

describe("DevolucaoController -> Atrasos", () => {
    it("deve retornar 0 se a diferença for menor ou igual a 15 minutos", () => {
        const previsto = "2023-10-01 14:00";
        const devolucao = "2023-10-01 14:10";
        expect(
            DevolucaoController.calcularHorasExtras(previsto, devolucao),
        ).toBe(0);
    });

    it("deve retornar horas extras corretas para diferença maior que 15 minutos", () => {
        const previsto = "2023-10-01 14:00";
        const devolucao = "2023-10-01 15:30";
        expect(
            DevolucaoController.calcularHorasExtras(previsto, devolucao),
        ).toBe(2);
    });

    it("deve arredondar para cima frações de hora", () => {
        const previsto = "2023-10-01 14:00";
        const devolucao = "2023-10-01 15:01";
        expect(
            DevolucaoController.calcularHorasExtras(previsto, devolucao),
        ).toBe(2);
    });

    it("deve retornar 0 se a devolução for antes do previsto", () => {
        const previsto = "2023-10-01 14:00";
        const devolucao = "2023-10-01 13:45";
        expect(
            DevolucaoController.calcularHorasExtras(previsto, devolucao),
        ).toBe(0);
    });

    it("deve retornar 0 se a devolução for exatamente no horário previsto", () => {
        const previsto = "2023-10-01 14:00";
        const devolucao = "2023-10-01 14:00";
        expect(
            DevolucaoController.calcularHorasExtras(previsto, devolucao),
        ).toBe(0);
    });

    it("deve retornar 0 se a devolução for exatamente 15 minutos após o previsto", () => {
        const previsto = "2023-10-01 14:00";
        const devolucao = "2023-10-01 14:15";
        expect(
            DevolucaoController.calcularHorasExtras(previsto, devolucao),
        ).toBe(0);
    });
});

(globalThis as any).localStorage = {
    store: {} as Record<string, string>,
    getItem(key: string) {
        return this.store[key] ?? null;
    },
    setItem(key: string, value: string) {
        this.store[key] = value;
    },
    removeItem(key: string) {
        delete this.store[key];
    },
    clear() {
        this.store = {};
    },
};
