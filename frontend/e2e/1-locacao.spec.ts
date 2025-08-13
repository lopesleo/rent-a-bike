import { test, expect } from "@playwright/test";
import { TelaListarLocacao } from "./tela-listar-locacao";
import { TelaLocacao } from "./tela-locacao";
import { TelaLogin } from "./tela-login";

test.describe("Cadastro de Locação", () => {
    let telaListarLocacao: TelaListarLocacao;
    let telaLocacao: TelaLocacao;
    let telaLogin: TelaLogin;

    test.beforeEach(async ({ page }) => {
        telaLogin = new TelaLogin(page);
        telaListarLocacao = new TelaListarLocacao(page);
        telaLocacao = new TelaLocacao(page);

        await telaLogin.acessar();
        await telaLogin.realizarLogin("12345678910", "teste");
        await expect(page).toHaveURL("/");
    });

    test("lanca erro ao tentar criar locacao com campo invalido ou vazio", async () => {
        await telaLocacao.acessarTelaRegistrarLocacao();
        await telaLocacao.clicarEmRegistrar();
        await telaLocacao.verificarAlerta("Cliente não selecionado");
    });

    test("realiza registro de locação com sucesso", async () => {
        await telaLocacao.acessarTelaRegistrarLocacao();
        await telaLocacao.insereClienteString("2");
        await telaLocacao.insereQuantidadeHorasString("2");
        await telaLocacao.insereCodigoItem("IT001");
        await telaLocacao.clicarEmRegistrar();
        await telaLocacao.verificarAlerta("Locação registrada!");
    });
    test("lanca erro ao tentar criar uma locacao com item indisponível", async () => {
        await telaLocacao.acessarTelaRegistrarLocacao();
        await telaLocacao.insereClienteString("6");
        await telaLocacao.insereQuantidadeHorasString("2");
        await telaLocacao.insereCodigoItem("IT001");
        await telaLocacao.verificarAlerta("Item nao disponivel");
    });

    test("verifica se tabela de listagem de locações tem conteúdo", async () => {
        await telaListarLocacao.acessarTelaListarLocacao();
        await telaListarLocacao.verificarTabelaTemConteudo();
    });
});
