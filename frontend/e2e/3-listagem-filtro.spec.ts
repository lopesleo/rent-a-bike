import { test, expect, Page } from "@playwright/test";
import { TelaLogin } from "./tela-login";
import { TelaListarLocacao } from "./tela-listar-locacao";

const CREDENCIAIS = {
    GERENTE: { user: "12345678910", pass: "teste" },
};

const DADOS_LOCACAO_1 = {
    id: "1",
    cliente: "Ana Silva",
};

const DADOS_LOCACAO_2 = {
    id: "2",
    cliente: "Bruno Souza",
};

test.describe("Filtros na Lista de Locações", () => {
    let page: Page;

    test.beforeEach(async ({ browser }) => {
        page = await browser.newPage();
        const telaLogin = new TelaLogin(page);
        const telaListarLocacao = new TelaListarLocacao(page);

        await telaLogin.acessar();
        await telaLogin.realizarLogin(
            CREDENCIAIS.GERENTE.user,
            CREDENCIAIS.GERENTE.pass,
        );
        await telaListarLocacao.acessarTelaListarLocacao();

        await expect(page.locator("tbody tr")).not.toHaveCount(0, {
            timeout: 10000,
        });
    });

    test("deve filtrar locações pelo nome do cliente", async () => {
        await page.locator("#campo-filtro").selectOption("nome");
        await page.locator("#filtro-locacoes").fill("Ana");

        await expect(
            page.locator("tr").filter({ hasText: DADOS_LOCACAO_2.cliente }),
        ).not.toBeVisible();
        await expect(
            page.locator("tr").filter({ hasText: DADOS_LOCACAO_1.cliente }),
        ).toBeVisible();
    });

    test("deve filtrar locações pelo CPF do cliente", async () => {
        await page.locator("#campo-filtro").selectOption("cpf");
        await page.locator("#filtro-locacoes").fill("987654");

        await expect(
            page.locator("tr").filter({ hasText: DADOS_LOCACAO_1.cliente }),
        ).not.toBeVisible();
        await expect(
            page.locator("tr").filter({ hasText: DADOS_LOCACAO_2.cliente }),
        ).toBeVisible();
    });

    test("deve limpar o filtro e exibir todas as locações novamente", async () => {
        const totalLinhasAntes = await page.locator("tbody tr").count();
        expect(totalLinhasAntes).toBeGreaterThan(1);

        await page.locator("#campo-filtro").selectOption("id");
        await page.locator("#filtro-locacoes").fill(DADOS_LOCACAO_1.id);

        await expect(page.locator("tbody tr")).toHaveCount(1);

        await page.locator("#clear-filtro").click();

        await expect(page.locator("tbody tr")).toHaveCount(totalLinhasAntes);
        await expect(page.locator("#filtro-locacoes")).toBeEmpty();
    });
});
