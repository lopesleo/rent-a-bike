import { test, expect, Page } from "@playwright/test";
import { TelaLogin } from "./tela-login";
import { TelaLocacao } from "./tela-locacao";
import { TelaListarLocacao } from "./tela-listar-locacao";
import { TelaDevolucao } from "./tela-devolucao";

const CREDENCIAIS = {
    GERENTE: { user: "12345678910", pass: "teste" },
    ATENDENTE: { user: "12312312312", pass: "batata" },
    MECANICO: { user: "98765432101", pass: "senha" },
};
const CLIENTE_ID = "1";
const ITEM_BIKE_ID = 1;
const ITEM_BIKE_COD = "IT001";
const ITEM_CAPACETE_ID = 2;
const ITEM_CAPACETE_COD = "IT002";

test.describe("Fluxo de Devolução (Gerente)", () => {
    test("deve realizar uma devolução com item sujo", async ({ page }) => {
        const telaLogin = new TelaLogin(page);
        await telaLogin.acessar();
        await telaLogin.realizarLogin(
            CREDENCIAIS.GERENTE.user,
            CREDENCIAIS.GERENTE.pass,
        );

        const telaDevolucao = new TelaDevolucao(page);
        await telaDevolucao.acessarTelaPelaListaDeLocacoes(2);
        await telaDevolucao.marcarLimpeza(ITEM_CAPACETE_ID);
        await telaDevolucao.verificarCustoAdicional("limpeza", "R$ 0.25");
        await telaDevolucao.verificaSucessoAoRealizarDevolucao();
    });

    test("deve realizar uma devolução com avaria", async ({ page }) => {
        const telaLogin = new TelaLogin(page);
        await telaLogin.acessar();
        await telaLogin.realizarLogin(
            CREDENCIAIS.GERENTE.user,
            CREDENCIAIS.GERENTE.pass,
        );

        const telaDevolucao = new TelaDevolucao(page);
        await telaDevolucao.acessarTelaPelaListaDeLocacoes(3);
        await telaDevolucao.adicionarAvaria(
            "Risco profundo",
            "25.00",
            "/home/lopesleo/projeto-integrador/rent-a-bike/frontend/e2e/imgTeste.jpg",
        );
        await telaDevolucao.verificarCustoAdicional("avarias", "R$ 25.00");
        await telaDevolucao.verificaSucessoAoRealizarDevolucao();
    });
});

test.describe("Permissões (Atendente)", () => {
    test("atendente não deve conseguir adicionar avaria", async ({ page }) => {
        const telaLogin = new TelaLogin(page);
        await telaLogin.acessar();
        await telaLogin.realizarLogin(
            CREDENCIAIS.ATENDENTE.user,
            CREDENCIAIS.ATENDENTE.pass,
        );

        const telaDevolucao = new TelaDevolucao(page);
        await telaDevolucao.acessarTelaPelaListaDeLocacoes(4);
        const botaoAvaria = page.locator('button:has-text("+ Avaria")');
        await expect(botaoAvaria).toHaveCount(0);
    });
});

test.describe("Permissões (Mecânico)", () => {
    test("mecanico não deve ver o botão 'Devolver' na lista de locações", async ({
        page,
    }) => {
        const telaLogin = new TelaLogin(page);

        await telaLogin.acessar();
        await telaLogin.realizarLogin(
            CREDENCIAIS.GERENTE.user,
            CREDENCIAIS.GERENTE.pass,
        );
        await telaLogin.realizarLogout(); // Logout do Gerente

        await telaLogin.realizarLogin(
            CREDENCIAIS.MECANICO.user,
            CREDENCIAIS.MECANICO.pass,
        );

        const telaListarLocacao = new TelaListarLocacao(page);
        await telaListarLocacao.acessarTelaListarLocacao();
        await telaListarLocacao.verificarBotaoDevolverNaoEstaPresente(4);
    });
});

test.describe("Ciclo de Vida da Avaria", () => {
    const ITEM_ID_AVARIA_TEST = 3;
    const ITEM_COD_AVARIA_TEST = "IT003";
    const CLIENTE_ID_NOVA_LOCACAO = "2";

    test("deve exibir avaria em nova locação após devolução", async ({
        page,
    }) => {
        const telaLogin = new TelaLogin(page);
        const telaDevolucao = new TelaDevolucao(page);
        const telaLocacao = new TelaLocacao(page);

        await telaLogin.acessar();
        await telaLogin.realizarLogin(
            CREDENCIAIS.GERENTE.user,
            CREDENCIAIS.GERENTE.pass,
        );

        await telaLocacao.acessarTelaRegistrarLocacao();
        await telaLocacao.insereClienteString(CLIENTE_ID_NOVA_LOCACAO);
        await telaLocacao.insereQuantidadeHorasString("1");

        await telaLocacao.insereCodigoItem(ITEM_COD_AVARIA_TEST);

        await telaLocacao.verificarIndicadorDeAvaria(ITEM_ID_AVARIA_TEST, 1);
    });
});
