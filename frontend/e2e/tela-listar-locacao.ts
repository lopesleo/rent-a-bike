import { Page, expect } from "@playwright/test";

export class TelaListarLocacao {
    constructor(private page: Page) {}

    async acessarTelaListarLocacao() {
        await this.page.goto("/listarlocacao");
        await expect(
            this.page.locator('h2:has-text("Lista de Locações")'),
        ).toBeVisible();
    }

    async clicarEmDevolver() {
        await this.page.waitForSelector('button:has-text("Devolver")');
        await this.page.click('button:has-text("Devolver")');
    }

    async verificarTabelaTemConteudo() {
        const tabela = this.page.locator("xpath=//table");
        await tabela.waitFor();
        const linhas = tabela.locator("tbody tr");
        await linhas.first().waitFor();
        const count = await linhas.count();
        expect(count).toBeGreaterThan(0);
    }
    async verificarBotaoDevolverNaoEstaPresente(locacaoId: number) {
        const linhaLocacao = this.page.locator(`tr:has-text("${locacaoId}")`);
        const botaoDevolver = linhaLocacao.locator(
            'button:has-text("Devolver")',
        );
        await expect(botaoDevolver).toHaveCount(0);
    }
    async verificarTabelaVazia() {
        const tabela = this.page.locator("xpath=//table");
        await tabela.waitFor();
        const linhas = tabela.locator("tbody tr");
        const count = await linhas.count();
        expect(count).toBe(0);
    }

    async VerificaSeBotaoDevolverNaoEstaPresente() {
        await expect(
            this.page.locator('button:has-text("Devolver")'),
        ).toHaveCount(0);
    }
}
