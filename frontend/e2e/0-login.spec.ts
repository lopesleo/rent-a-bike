import { test, expect } from "@playwright/test";
import { TelaLogin } from "./tela-login.ts";

test.describe("Login e Logout", () => {
    let telaLogin: TelaLogin;

    test.beforeEach(async ({ page }) => {
        telaLogin = new TelaLogin(page);
    });

    test("deve exibir erro com credenciais inválidas", async () => {
        await telaLogin.acessar();
        await telaLogin.preencherUsuario("usuarioinvalido");
        await telaLogin.preencherSenha("senhainvalida");
        await telaLogin.clicarEntrar();
        await telaLogin.verificarMensagemErro(`Usuário ou senha inválidos.`);
    });

    test("deve realizar login com sucesso", async ({ page }) => {
        await telaLogin.acessar();
        await telaLogin.realizarLogin("12345678910", "teste");

        await expect(page).toHaveURL("/");
        await expect(
            page.locator('h1:has-text("Bem-vindo ao Rent-A-Bike")'),
        ).toBeVisible();
    });

    test("deve realizar logout com sucesso", async ({ page }) => {
        await telaLogin.acessar();
        await telaLogin.realizarLogin("12345678910", "teste");
        await expect(page).toHaveURL("/");

        await telaLogin.realizarLogout();

        await expect(page).toHaveURL("/login");
        await expect(
            page.locator('h2:has-text("Acesse sua Conta")'),
        ).toBeVisible();
    });
});
