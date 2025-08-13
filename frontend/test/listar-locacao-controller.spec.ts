(globalThis as any).alert = () => {};
/**
 * @vitest-environment jsdom
 */

import { describe, it, expect, vi } from "vitest";
import { ListarLocacaoController } from "../src/locacao/ListarLocacaoController.js";
import { Locacao } from "../src/locacao/LocacaoModel";
import { Cliente } from "../src/cliente/Cliente";
import { Funcionario } from "../src/funcionario/Funcionario";

const mockView = {
    exibirLocacoes: vi.fn(),
};
const mockManager = {
    getLocacoes: vi.fn(),
    getItens: vi.fn(),
};

const clienteFalso = new Cliente(
    1,
    "Ana Silva",
    "12345678901",
    "99999",
    "ana@email.com",
    "Rua X",
    "1990-01-01",
    "",
    "",
);
const funcionarioFalso = new Funcionario(
    1,
    "F01",
    "Carlos Lima",
    "88888",
    "carlos@email.com",
    "11122233344",
    "GERENTE",
);
const LOCACOES_FAKE = [
    new Locacao(
        1,
        clienteFalso,
        funcionarioFalso,
        "",
        2,
        "",
        0,
        50,
        "EM_ANDAMENTO",
        [],
    ),
    new Locacao(
        2,
        clienteFalso,
        funcionarioFalso,
        "",
        3,
        "",
        0,
        75,
        "FINALIZADA",
        [],
    ),
];

describe("ListarLocacaoController", () => {
    it("deve chamar o manager para carregar locações e a view para exibi-las", async () => {
        mockManager.getLocacoes.mockResolvedValue(LOCACOES_FAKE);
        mockManager.getItens.mockResolvedValue([]);

        const controller = new ListarLocacaoController(
            mockView as any,
            mockManager as any,
        );

        await vi.dynamicImportSettled();

        expect(mockManager.getLocacoes).toHaveBeenCalledOnce();
        expect(mockView.exibirLocacoes).toHaveBeenCalledOnce();
        expect(mockView.exibirLocacoes).toHaveBeenCalledWith(expect.any(Array));
    });
});
