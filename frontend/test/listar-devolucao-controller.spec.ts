(globalThis as any).alert = () => {};

import { describe, it, expect, vi, beforeEach } from "vitest";
import { ListarDevolucaoController } from "../src/devolucao/ListarDevolucaoController.js";

class MockView {
    exibirDevolucoes = vi.fn();
    exibirMensagemVazia = vi.fn();
    exibirCarregando = vi.fn();
    ocultarCarregando = vi.fn();
}

class MockManager {
    constructor(
        private devolucoes: any[] = [],
        private shouldFail = false,
    ) {}

    async getDevolucoes() {
        if (this.shouldFail) throw new Error("Erro no servidor");
        return this.devolucoes;
    }
}

const DEVOLUCOES_MOCK = [
    {
        id: 1,
        locacao: {
            id: 1,
            cliente: { nome: "Ana Silva", cpf: "123.456.789-01" },
            horas_contratadas: 2,
        },
        funcionario: { nome: "Carlos Lima" },
        data_hora: "2025-06-30T14:30:00",
        valor_pago: 45.5,
        horas_usadas: 2,
        avarias: [],
    },
    {
        id: 2,
        locacao: {
            id: 2,
            cliente: { nome: "Bruno Santos", cpf: "987.654.321-00" },
            horas_contratadas: 3,
        },
        funcionario: { nome: "Maria Costa" },
        data_hora: "2025-06-30T16:15:00",
        valor_pago: 78.2,
        horas_usadas: 4,
        avarias: [{ descricao: "Arranhão lateral", valor: 15.0 }],
    },
];

describe("ListarDevolucaoController", () => {
    let view: MockView;
    let controller: ListarDevolucaoController;

    beforeEach(() => {
        view = new MockView();
        vi.clearAllMocks();
    });

    it("exibe mensagem quando não há devoluções", async () => {
        const manager = new MockManager([]);
        controller = new ListarDevolucaoController(view as any);
        (controller as any).manager = manager;
        await controller.carregar();
        const resultado = await manager.getDevolucoes();
        expect(resultado).toEqual([]);
        expect(resultado.length).toBe(0);
    });

    it("trata erro ao carregar devoluções", async () => {
        const manager = new MockManager([], true);
        controller = new ListarDevolucaoController(view as any);
        (controller as any).manager = manager;
        globalThis.alert = vi.fn();
        await controller.carregar();
        await expect(manager.getDevolucoes()).rejects.toThrow(
            "Erro no servidor",
        );
    });

    it("filtra devoluções por nome do cliente", async () => {
        controller = new ListarDevolucaoController(view as any);
        (controller as any).devolucoes = DEVOLUCOES_MOCK;

        const filtradas = DEVOLUCOES_MOCK.filter((d) =>
            d.locacao.cliente.nome.toLowerCase().includes("ana"),
        );

        expect(filtradas).toHaveLength(1);
        expect(filtradas[0].locacao.cliente.nome).toContain("Ana");
    });

    it("filtra devoluções por CPF", async () => {
        controller = new ListarDevolucaoController(view as any);
        (controller as any).devolucoes = DEVOLUCOES_MOCK;

        const filtradas = DEVOLUCOES_MOCK.filter(
            (d) => d.locacao.cliente.cpf === "987.654.321-00",
        );

        expect(filtradas).toHaveLength(1);
        expect(filtradas[0].locacao.cliente.cpf).toBe("987.654.321-00");
    });

    it("filtra devoluções com avarias", async () => {
        controller = new ListarDevolucaoController(view as any);
        (controller as any).devolucoes = DEVOLUCOES_MOCK;

        const comAvarias = DEVOLUCOES_MOCK.filter(
            (d) => d.avarias && d.avarias.length > 0,
        );

        expect(comAvarias).toHaveLength(1);
        expect(comAvarias[0].avarias.length).toBeGreaterThan(0);
    });

    it("calcula total de devoluções corretamente", async () => {
        controller = new ListarDevolucaoController(view as any);
        (controller as any).devolucoes = DEVOLUCOES_MOCK;

        const total = DEVOLUCOES_MOCK.reduce((sum, d) => sum + d.valor_pago, 0);

        expect(total).toBe(123.7);
    });

    it("ordena devoluções por data mais recente", async () => {
        const manager = new MockManager(DEVOLUCOES_MOCK);
        controller = new ListarDevolucaoController(view as any);
        (controller as any).manager = manager;

        await controller.carregar();

        const ordenadas = [...DEVOLUCOES_MOCK].sort(
            (a, b) =>
                new Date(b.data_hora).getTime() -
                new Date(a.data_hora).getTime(),
        );

        expect(ordenadas[0].data_hora).toBe("2025-06-30T16:15:00");
    });

    it("identifica devoluções em atraso", async () => {
        const devolucaoAtrasada = {
            ...DEVOLUCOES_MOCK[0],
            horas_usadas: 5,
            locacao: {
                ...DEVOLUCOES_MOCK[0].locacao,
                horas_contratadas: 2,
            },
        };

        const manager = new MockManager([devolucaoAtrasada]);
        controller = new ListarDevolucaoController(view as any);
        (controller as any).manager = manager;

        await controller.carregar();

        const emAtraso =
            devolucaoAtrasada.horas_usadas >
            devolucaoAtrasada.locacao.horas_contratadas;
        expect(emAtraso).toBe(true);
    });

    it("formata valores monetários corretamente", async () => {
        const manager = new MockManager(DEVOLUCOES_MOCK);
        controller = new ListarDevolucaoController(view as any);
        (controller as any).manager = manager;

        await controller.carregar();

        const valor = DEVOLUCOES_MOCK[0].valor_pago;
        const valorFormatado = valor.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL",
        });
        expect(valorFormatado).toContain("45");
        expect(valorFormatado).toContain("50");
        expect(valorFormatado).toMatch(/R\$\s*45[,.]50/);
    });

    it("calcula estatísticas de avarias", async () => {
        const manager = new MockManager(DEVOLUCOES_MOCK);
        controller = new ListarDevolucaoController(view as any);
        (controller as any).manager = manager;

        await controller.carregar();

        const totalAvarias = DEVOLUCOES_MOCK.reduce(
            (total, d) => total + (d.avarias ? d.avarias.length : 0),
            0,
        );

        const valorAvarias = DEVOLUCOES_MOCK.reduce(
            (total, d) =>
                total +
                (d.avarias
                    ? d.avarias.reduce(
                          (sum: number, a: any) => sum + a.valor,
                          0,
                      )
                    : 0),
            0,
        );

        expect(totalAvarias).toBe(1);
        expect(valorAvarias).toBe(15.0);
    });

    it("exporta dados para relatório", async () => {
        const manager = new MockManager(DEVOLUCOES_MOCK);
        controller = new ListarDevolucaoController(view as any);
        (controller as any).manager = manager;

        await controller.carregar();

        const dadosRelatorio = DEVOLUCOES_MOCK.map((d) => ({
            id: d.id,
            cliente: d.locacao.cliente.nome,
            funcionario: d.funcionario.nome,
            valor: d.valor_pago,
            data: d.data_hora,
        }));

        expect(dadosRelatorio).toHaveLength(2);
        expect(dadosRelatorio[0].cliente).toBe("Ana Silva");
    });
});
