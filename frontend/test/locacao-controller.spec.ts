(globalThis as any).alert = () => {};

import { describe, it, expect, beforeAll } from "vitest";
import { LocacaoController } from "../src/locacao/LocacaoController.js";

class MockView {}
class MockManager {
    async registrarLocacao(body: any) {
        return;
    }
}

let controller: LocacaoController;

beforeAll(() => {
    controller = new LocacaoController(
        new MockView() as any,
        new MockManager() as any,
    );
    (controller as any).clientesCache = [
        {
            id: 1,
            nome: "João Silva",
            cpf: "12345678901",
            telefone: "11999999999",
            email: "joao@teste.com",
            endereco: "Rua A, 123",
            data_nascimento: "1990-01-01",
            foto_path: "",
            doc_path: "",
        },
    ];
    (controller as any).funcionariosCache = [
        {
            id: 2,
            nome: "Maria Santos",
            codigo: "F001",
            telefone: "11888888888",
            email: "maria@empresa.com",
            cpf: "98765432100",
            cargo: "ATENDENTE",
        },
    ];
    (controller as any).itensCache = [
        {
            id: 1,
            codigo: "BK001",
            modelo: "Bicicleta MTB",
            valor_hora: 15,
            disponivel: true,
        },
    ];
});

describe("LocacaoController - Validações", () => {
    it("não registra sem cliente", async () => {
        (controller as any).cliente = null;
        (controller as any).funcionario = { id: 2 };
        (controller as any).itens = [{ id: 1 }];

        await expect(controller.registrar(2)).rejects.toThrow(
            "Cliente não selecionado",
        );
    });
    it("falha quando funcionário está ausente", async () => {
        (controller as any).cliente = { id: 1 };
        (controller as any).funcionario = null;
        (controller as any).itens = [{ id: 1 }];

        await expect(controller.registrar(2)).rejects.toThrow(
            "Funcionário não selecionado",
        );
    });

    it("rejeita horas negativas", async () => {
        (controller as any).cliente = { id: 1 };
        (controller as any).funcionario = { id: 2 };
        (controller as any).itens = [{ id: 1 }];

        await expect(controller.registrar(-1)).rejects.toThrow(
            "Horas contratadas inválidas",
        );
    });
    it("não aceita zero horas", async () => {
        (controller as any).cliente = { id: 1 };
        (controller as any).funcionario = { id: 2 };
        (controller as any).itens = [{ id: 1 }];

        await expect(controller.registrar(0)).rejects.toThrow(
            "Horas contratadas inválidas",
        );
    });
    it("lista de itens vazia causa erro", async () => {
        (controller as any).cliente = { id: 1 };
        (controller as any).funcionario = { id: 2 };
        (controller as any).itens = [];

        await expect(controller.registrar(2)).rejects.toThrow(
            "Nenhum item selecionado",
        );
    });
    it("cenário de sucesso completo", async () => {
        (controller as any).cliente = { id: 1 };
        (controller as any).funcionario = { id: 2 };
        (controller as any).itens = [{ id: 1 }];

        await expect(controller.registrar(3)).resolves.not.toThrow();
    });
});

describe("LocacaoController - Cálculos financeiros", () => {
    let controllerCalc: LocacaoController;

    beforeAll(() => {
        controllerCalc = new LocacaoController(
            new MockView() as any,
            new MockManager() as any,
        );

        (controllerCalc as any).itens = [
            { id: 1, valor_hora: 12.5 },
            { id: 2, valor_hora: 8.0 },
            { id: 3, valor_hora: 25.0 },
        ];
    });

    it("soma valores por hora corretamente", () => {
        expect(controllerCalc.calcularSubtotal(2)).toBe(91.0);
    });

    it("sem desconto para locações curtas", () => {
        expect(controllerCalc.calcularDesconto(1)).toBe(0);
        expect(controllerCalc.calcularDesconto(2)).toBe(0);
    });

    it("aplica desconto em locações longas", () => {
        const subtotal = 45.5 * 4; // 182.00
        const desconto = controllerCalc.calcularDesconto(4);
        expect(desconto).toBeGreaterThan(0);
    });

    it("gera horário de entrega futuro", () => {
        const agora = new Date();
        const entrega = controllerCalc.calcularEntrega(3);
        expect(entrega).toContain(agora.getFullYear().toString());
    });
});
