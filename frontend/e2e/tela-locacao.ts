import { Page, expect } from "@playwright/test";

export class TelaLocacao {
    constructor(private page: Page) {}
    async acessarTelaRegistrarLocacao() {
        await this.page.goto("/registrarlocacao");
        await this.page.waitForSelector(
            'xpath=//h2[contains(text(),"Registrar Locação")]',
        );
    }
    async acessarTelaListarLocacao() {
        await this.page.goto("/listarlocacao");
        await expect(
            this.page.locator('h2:has-text("Lista de Locações")'),
        ).toBeVisible();

        await this.page.locator("tbody tr").first().waitFor({ timeout: 5000 });
    }

    async clicarEmRegistrar() {
        await this.page.waitForSelector('button:has-text("Registrar")');
        await this.page.click('button:has-text("Registrar")');
    }
    async verificarIndicadorDeAvaria(itemId: number, contagemEsperada: number) {
        const botaoAvarias = this.page.locator(
            `button[data-bs-target="#avarias-collapse-${itemId}"]`,
        );
        await botaoAvarias.waitFor();
        await expect(botaoAvarias).toBeVisible();
        await expect(botaoAvarias).toContainText(
            `Avarias (${contagemEsperada})`,
        );
    }
    async verificarAlerta(msg: string) {
        this.page.once("dialog", async (dialog) => {
            console.log("Dialog message:", dialog.message());
            expect(dialog.type()).toBe("alert");
            expect(dialog.message()).toBe(msg);
            await dialog.accept();
        });
    }
    async aguardaTempo(ms: number) {
        await this.page.waitForTimeout(ms);
    }
    async insereClienteString(cliente: string) {
        await this.page.locator('xpath=//input[@id="cliente"]').fill(cliente);
        await this.page.locator('xpath=//input[@id="cliente"]').press("Tab");
    }

    async insereQuantidadeHorasString(qtdHoras: string) {
        await this.page.locator('xpath=//input[@id="horas"]').fill(qtdHoras);
    }

    async insereCodigoItem(codigoItem: string) {
        await this.page.locator('xpath=//input[@id="item"]').fill(codigoItem);
        await this.page.locator('xpath=//input[@id="item"]').press("Tab");
    }

    async removerItemDaTabela(codigoItem: string) {
        const tabela = this.page.locator(
            'xpath=//table[@id="itens-selecionados"]',
        );
        await tabela.waitFor();
        const linhas = tabela.locator("tbody tr");
        const count = await linhas.count();
        expect(count).toBeGreaterThan(0);
        const linha = linhas.filter({ hasText: codigoItem });
        expect(await linha.count()).toBeGreaterThan(0);
        const botaoRemover = linha.locator('button:has-text("Remover")');
        await botaoRemover.click();
        const conteudo = await linhas.filter({ hasText: codigoItem }).count();
        expect(conteudo).toBe(0);
    }

    async verificaCodigoItemEmTabela(codigoItem: string) {
        const tabela = this.page.locator(
            'xpath=//table[@id="itens-selecionados"]',
        );
        await tabela.waitFor();
        const linhas = tabela.locator("tbody tr");
        const count = await linhas.count();
        expect(count).toBeGreaterThan(0);
        const conteudo = await linhas.filter({ hasText: codigoItem }).count();
        expect(conteudo).toBeGreaterThan(0);
    }

    async insereFuncionario(funcionario: string) {
        await this.page
            .locator('xpath=//select[@id="funcionario"]')
            .selectOption(funcionario);
    }
}
