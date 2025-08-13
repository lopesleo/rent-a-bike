import { Funcionario } from "../funcionario/Funcionario";
import { ApiClient } from "../infra/ApiClient";

export class AutenticadorUtils {
    private cargoParaControle: Array<string> = [
        "ATENDENTE",
        "GERENTE",
        "MECANICO",
    ];

    constructor() {
        this.configurarLogoutDinamico();
        if (!this.isAutenticado()) {
            window.location.href = "/login";
        }
        this.verificaUrlAtual();
    }

    getFuncionario(): Funcionario | null {
        const funcionarioLocal = sessionStorage.getItem("funcionario");
        if (!funcionarioLocal) {
            return null;
        }

        const dadosFuncionario = JSON.parse(funcionarioLocal);

        if (dadosFuncionario && dadosFuncionario.id) {
            return new Funcionario(
                dadosFuncionario.id,
                dadosFuncionario.codigo,
                dadosFuncionario.nome,
                dadosFuncionario.telefone,
                dadosFuncionario.email,
                dadosFuncionario.cpf,
                dadosFuncionario.cargo,
            );
        }

        return null;
    }

    isAutenticado(): boolean {
        if (document.cookie.includes("session_expired=true")) {
            sessionStorage.removeItem("funcionario");
            window.location.href = "/login";
            return false;
        }
        return !!this.getFuncionario();
    }

    getCargo(): string | null {
        const funcionario = this.getFuncionario();
        return funcionario ? funcionario.cargo : null;
    }

    validarCargo(cargo: Array<string>): boolean {
        const funcionario = this.getFuncionario();
        let cargoValido = false;
        if (!funcionario) return false;
        cargo.forEach((c: string) => {
            c = c.toLocaleUpperCase();
            if (funcionario.cargo === c) {
                cargoValido = true;
            }
        });
        return cargoValido;
    }
    configurarUrlsPorCargo() {
        return [
            {
                cargo: "GERENTE",
                urls: [
                    "/listardevolucao",
                    "/listarlocacao",
                    "/registrarlocacao",
                    "/registrardevolucao",
                    "/relatorio",
                    "/",
                ],
            },
            {
                cargo: "ATENDENTE",
                urls: [
                    "/listardevolucao",
                    "/listarlocacao",
                    "/registrarlocacao",
                    "/registrardevolucao",
                    "/relatorio",
                    "/",
                ],
            },
            {
                cargo: "MECANICO",
                urls: ["/listardevolucao", "/listarlocacao", "/"],
            },
        ];
    }

    validarAcesso(urlAtual: string): void {
        this.isAutenticado();
        const urlsPorCargo = this.configurarUrlsPorCargo();
        const cargosPermitidos = urlsPorCargo
            .filter((caminho) =>
                caminho.urls.some((url) => url.replace("/", "") === urlAtual),
            )
            .map((item) => item.cargo);
        this.cargoParaControle = cargosPermitidos;
        this.configuraCaminhosPorCargo();

        if (!this.validarCargo(this.cargoParaControle)) {
            if (this.getFinalUrl() == "") window.location.href = "/login";
            document.body.innerHTML =
                "Acesso negado. Você não tem permissão para acessar esta página.";
            setTimeout(() => {
                if (this.getFinalUrl() == "") window.location.href = "/login";
                else window.location.href = "/";
            }, 3000);
        }
    }

    configurarLogoutDinamico() {
        const btnLogout = document.getElementById("logout");
        if (btnLogout) {
            btnLogout.addEventListener("click", (e) => {
                e.preventDefault();
                this.logout();
            });
        }
    }
    getFinalUrl(): string {
        const path = window.location.pathname;
        return path.startsWith("/")
            ? path.substring(1).split("/")[0]
            : path.split("/")[0];
    }

    verificaUrlAtual(): void {
        const urlAtual = this.getFinalUrl();
        this.validarAcesso(urlAtual);
    }
    configuraCaminhosPorCargo(): void {
        if (
            this.cargoParaControle.includes("MECANICO") &&
            this.getCargo() === "MECANICO"
        ) {
            document
                .getElementById("menu-registrarlocacao")
                ?.setAttribute("hidden", "true");
            document
                .getElementById("menu-registrardevolucao")
                ?.setAttribute("hidden", "true");
            document
                .getElementById("menu-relatorio")
                ?.setAttribute("hidden", "true");
            if (this.getFinalUrl() === "") {
                document
                    .getElementById("main-registrarlocacao")
                    ?.setAttribute("hidden", "true");
                document
                    .getElementById("main-registrardevolucao")
                    ?.setAttribute("hidden", "true");
                document
                    .getElementById("main-relatorio")
                    ?.setAttribute("hidden", "true");
                const mainListarLocacao =
                    document.getElementById("main-listarlocacao");
                const mainListarDevolucao = document.getElementById(
                    "main-listardevolucao",
                );
                mainListarLocacao?.classList.remove("col-md-2");
                mainListarDevolucao?.classList.remove("col-md-2");
                mainListarLocacao?.classList.add("col-md-5");
                mainListarDevolucao?.classList.add("col-md-5");
            }
        }
        if (
            this.getFinalUrl() == "relatorio" &&
            this.getCargo() == "ATENDENTE"
        ) {
            document
                .getElementById("relatorio-locacoes-devolvidas")
                ?.setAttribute("hidden", "true");
        }
    }
    async logout(): Promise<void> {
        try {
            await ApiClient.post("/logout", {});
            sessionStorage.removeItem("funcionario");
            window.location.href = "/login";
        } catch (error) {
            console.error("Erro ao fazer logout:", error);
            sessionStorage.removeItem("funcionario");
            window.location.href = "/login";
        }
    }
}
