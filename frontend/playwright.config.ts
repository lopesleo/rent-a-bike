import { defineConfig } from "@playwright/test";

export default defineConfig({
    testDir: "e2e",
    testMatch: ["locacao.spec.ts", "devolucao.spec.ts", "**/*.spec.ts"],
    use: {
        baseURL: "http://localhost:5173",
        headless: true,
        actionTimeout: 5000,
    },
    workers: 1,
});
