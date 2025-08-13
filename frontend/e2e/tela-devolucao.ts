import { expect, Page } from "@playwright/test";

export class TelaDevolucao {
    constructor(private page: Page) {}

    async acessarTelaPelaListaDeLocacoes(locacaoId: number) {
        await this.page.goto("/listarlocacao");

        const linhaLocacao = this.page.locator("tr").filter({
            has: this.page.locator(`td:first-child:text-is("${locacaoId}")`),
        });

        await linhaLocacao.locator('button:has-text("Devolver")').click();
        await expect(
            this.page.locator('h2:has-text("Registrar Devolução")'),
        ).toBeVisible();
    }

    async clicarEmRegistrarDevolucao() {
        await this.page
            .locator('button:has-text("Registrar Devolução")')
            .click();
    }

    async marcarLimpeza(itemId: number) {
        await this.page.locator(`#limpeza-${itemId}`).check();
    }

    async adicionarAvaria(
        descricao: string,
        valor: string,
        caminhoFoto: string,
    ) {
        await this.page
            .locator('tbody#itens-locacao button:has-text("+ Avaria")')
            .click();

        await this.page.locator("#descricaoAvaria").fill(descricao);
        await this.page.locator("#valorAvaria").fill(valor);
        await this.page.locator("#fotoAvaria").setInputFiles(caminhoFoto);
        await this.page.locator("#avaliadorAvaria").selectOption({ index: 1 });
        await this.page.locator("#formAvaria button[type='submit']").click();

        const containerAvarias = this.page.locator(".avarias-container");
        await expect(containerAvarias).toContainText(descricao);
        await expect(containerAvarias).toContainText(
            `R$ ${parseFloat(valor).toFixed(2)}`,
        );
    }

    async verificarCustoAdicional(
        tipo: "avarias" | "limpeza",
        valorEsperado: string,
    ) {
        const elementoCusto = this.page.locator(`#custo-${tipo}`);
        await expect(elementoCusto).toContainText(valorEsperado);
    }

    async verificaSucessoAoRealizarDevolucao() {
        const dialogPromise = this.page.waitForEvent("dialog");
        await this.clicarEmRegistrarDevolucao();
        const dialog = await dialogPromise;
        expect(dialog.message()).toBe("Devolução registrada com sucesso!");
        await dialog.accept();
    }
}
