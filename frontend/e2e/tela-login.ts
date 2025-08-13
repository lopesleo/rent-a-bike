import { Page, expect } from "@playwright/test";

export class TelaLogin {
    constructor(private page: Page) {}

    async acessar() {
        await this.page.goto("/login");
    }

    async preencherUsuario(usuario: string) {
        await this.page.locator("#usuario").fill(usuario);
    }

    async preencherSenha(senha: string) {
        await this.page.locator("#senha").fill(senha);
    }

    async clicarEntrar() {
        await this.page.locator('button:has-text("Entrar")').click();
    }

    async realizarLogin(usuario: string, senha: string) {
        await this.preencherUsuario(usuario);
        await this.preencherSenha(senha);
        await this.clicarEntrar();

        await expect(this.page).toHaveURL("/");
    }

    async realizarLogout() {
        await this.page.locator("#logout").click();

        await expect(this.page).toHaveURL("/login");
    }

    async verificarMensagemErro(mensagem: string) {
        const erroElement = this.page.locator("#msg-erro");
        await expect(erroElement).toBeVisible();
        await expect(erroElement).toHaveText(mensagem);
    }
}
