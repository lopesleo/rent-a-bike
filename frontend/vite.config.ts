import { defineConfig } from "vite";

export default defineConfig({
    build: {
        rollupOptions: {
            input: {
                index: "index.html",
                registrarlocacao: "src/view/registrarlocacao.html",
                listarlocacao: "src/view/listarlocacao.html",
                registrardevolucao: "src/view/registrardevolucao.html",
                listardevolucao: "src/view/listardevolucao.html",
            },
        },
    },
    server: {
        proxy: {
            "/registrarlocacao": {
                target: "http://localhost:5173",
                changeOrigin: true,
                rewrite: (path) =>
                    path.replace(
                        /^\/registrarlocacao/,
                        "/src/view/registrarlocacao.html",
                    ),
            },

            "/listarlocacao": {
                target: "http://localhost:5173",
                changeOrigin: true,
                rewrite: (path) =>
                    path.replace(
                        /^\/listarlocacao/,
                        "/src/view/listarlocacao.html",
                    ),
            },

            "/registrardevolucao": {
                target: "http://localhost:5173",
                changeOrigin: true,
                rewrite: (path) =>
                    path.replace(
                        /^\/registrardevolucao/,
                        "/src/view/registrardevolucao.html",
                    ),
            },

            "/listardevolucao": {
                target: "http://localhost:5173",
                changeOrigin: true,
                rewrite: (path) =>
                    path.replace(
                        /^\/listardevolucao/,
                        "/src/view/listardevolucao.html",
                    ),
            },
            "/login": {
                target: "http://localhost:5173",
                changeOrigin: true,
                rewrite: (path) =>
                    path.replace(/^\/login/, "/src/view/login.html"),
            },
            "/relatorio": {
                target: "http://localhost:5173",
                changeOrigin: true,
                rewrite: (path) =>
                    path.replace(/^\/relatorio/, "/src/view/relatorio.html"),
            },
            "/api": {
                target: "http://localhost:8001",
                changeOrigin: true,
                rewrite: (path) => path.replace(/^\/api/, ""),
            },
        },
    },
});
