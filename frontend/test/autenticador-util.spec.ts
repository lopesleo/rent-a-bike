/**
 * @vitest-environment jsdom
 */

import { describe, it, expect, vi, beforeEach } from "vitest";
import { AutenticadorUtils } from "../src/Utils/AutenticadorUtils";
import { Funcionario } from "../src/funcionario/Funcionario";

vi.spyOn(
    AutenticadorUtils.prototype,
    "configurarLogoutDinamico",
).mockImplementation(() => {});
vi.spyOn(AutenticadorUtils.prototype, "verificaUrlAtual").mockImplementation(
    () => {},
);

describe("AutenticadorUtils", () => {
    beforeEach(() => {
        vi.clearAllMocks();
        vi.stubGlobal("location", { href: "" });
    });

    it('deve atribuir "/login" a window.location.href se não estiver autenticado', () => {
        vi.spyOn(Storage.prototype, "getItem").mockReturnValue(null);

        new AutenticadorUtils();

        expect(window.location.href).toBe("/login");
    });

    it("NÃO deve alterar window.location.href se estiver autenticado", () => {
        const dadosFuncionario = { id: 1, nome: "Teste", cargo: "GERENTE" };
        vi.spyOn(Storage.prototype, "getItem").mockReturnValue(
            JSON.stringify(dadosFuncionario),
        );

        new AutenticadorUtils();

        expect(window.location.href).toBe("");
    });

    it("getFuncionario deve retornar null se não houver funcionário na sessão", () => {
        vi.spyOn(Storage.prototype, "getItem").mockReturnValue(null);

        const autenticador = new AutenticadorUtils();

        expect(autenticador.getFuncionario()).toBeNull();
    });

    it("getFuncionario deve retornar um objeto Funcionario se houver dados na sessão", () => {
        const dadosFuncionario = {
            id: 1,
            nome: "Teste Gerente",
            cargo: "GERENTE",
            cpf: "123",
        };
        vi.spyOn(Storage.prototype, "getItem").mockReturnValue(
            JSON.stringify(dadosFuncionario),
        );

        const autenticador = new AutenticadorUtils();
        const funcionario = autenticador.getFuncionario();

        expect(funcionario).toBeInstanceOf(Funcionario);
        expect(funcionario?.id).toBe(1);
    });

    it("getCargo deve retornar o cargo correto do funcionário logado", () => {
        const dadosFuncionario = {
            id: 2,
            nome: "Teste Mecanico",
            cargo: "MECANICO",
        };
        vi.spyOn(Storage.prototype, "getItem").mockReturnValue(
            JSON.stringify(dadosFuncionario),
        );

        const autenticador = new AutenticadorUtils();

        expect(autenticador.getCargo()).toBe("MECANICO");
    });

    it("validarCargo deve retornar true para cargo permitido", () => {
        const dadosFuncionario = {
            id: 3,
            nome: "Teste Atendente",
            cargo: "ATENDENTE",
        };
        vi.spyOn(Storage.prototype, "getItem").mockReturnValue(
            JSON.stringify(dadosFuncionario),
        );

        const autenticador = new AutenticadorUtils();

        expect(autenticador.validarCargo(["ATENDENTE"])).toBe(true);
    });

    it("validarCargo deve retornar false para cargo não permitido", () => {
        const dadosFuncionario = {
            id: 4,
            nome: "Teste Mecanico",
            cargo: "MECANICO",
        };
        vi.spyOn(Storage.prototype, "getItem").mockReturnValue(
            JSON.stringify(dadosFuncionario),
        );

        const autenticador = new AutenticadorUtils();

        expect(autenticador.validarCargo(["GERENTE", "ATENDENTE"])).toBe(false);
    });
});
