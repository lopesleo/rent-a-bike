import { ErroLogin } from "../infra/ErroLogin.js";
import { ILoginView } from "./interface/ILoginView.js";
import { LoginController } from "./LoginController.js";

export class LoginView implements ILoginView {
    private usuarioInput!: HTMLInputElement;
    private senhaInput!: HTMLInputElement;
    private form!: HTMLFormElement;
    private controller: LoginController;
    private togglePasswordButton!: HTMLButtonElement;
    private eyeIcon!: HTMLElement;
    constructor() {
        this.controller = new LoginController(this);
        this.inicializarComponentes();
    }

    async inicializarComponentes() {
        this.usuarioInput = document.getElementById(
            "usuario",
        ) as HTMLInputElement;
        this.senhaInput = document.getElementById("senha") as HTMLInputElement;
        this.form = document.getElementById("form-login") as HTMLFormElement;
        this.togglePasswordButton = document.getElementById(
            "toggle-password",
        ) as HTMLButtonElement;
        this.eyeIcon = this.togglePasswordButton.querySelector(
            "i",
        ) as HTMLElement;

        this.form.addEventListener("submit", (event: Event) => {
            event.preventDefault();
            this.realizarLogin();
        });
        this.configurarToggleSenha();
    }
    private configurarToggleSenha(): void {
        this.togglePasswordButton.addEventListener("click", () => {
            const type =
                this.senhaInput.getAttribute("type") === "password"
                    ? "text"
                    : "password";
            this.senhaInput.setAttribute("type", type);

            this.eyeIcon.classList.toggle("bi-eye-fill");
            this.eyeIcon.classList.toggle("bi-eye-slash-fill");
        });
    }
    async realizarLogin(): Promise<boolean> {
        const usuario = this.getUsuarioInput();
        const senha = this.getSenhaInput();
        if (!usuario || !senha) {
            this.exibirErro("Usuário e senha são obrigatórios.");
            return false;
        }
        try {
            const resultado = await this.controller.realizarLogin(
                usuario,
                senha,
            );
            if (resultado) {
                this.limparCampos();
                this.redirecionarParaMenu();
                return true;
            } else {
                this.exibirErro("Usuário ou senha inválidos.");
                this.limparCampos();
                return false;
            }
        } catch (erro: any) {
            this.exibirErro(`Senha ou usuário incorretos: ${erro.message}`);
            this.limparCampos();
            return false;
        }
    }

    limparCampos(): void {
        this.usuarioInput.value = "";
        this.senhaInput.value = "";
    }

    getUsuarioInput(): string {
        return this.usuarioInput.value;
    }

    getSenhaInput(): string {
        return this.senhaInput.value;
    }

    exibirErro(mensagem: string): void {
        const msgElement = document.createElement("span");
        msgElement.id = "msg-erro";
        if (document.getElementById("msg-erro")) {
            msgElement.innerText = mensagem;
        } else {
            msgElement.innerText = mensagem;
            msgElement.style.color = "red";
            const formElement = document.getElementById("form-login");
            formElement!.appendChild(msgElement);
        }

        throw new ErroLogin(mensagem);
    }
    exibirMensagemSucesso(mensagem: string): void {
        console.log(`Sucesso: ${mensagem}`);
    }

    redirecionarParaListarLocacao(): void {
        window.location.href = "/listarlocacao";
    }

    redirecionarParaListarDevolucao(): void {
        window.location.href = "/listardevolucao";
    }

    redirecionarParaRegistrarLocacao(): void {
        window.location.href = "/registrarlocacao";
    }

    redirecionarParaRegistrarDevolucao(): void {
        window.location.href = "/registrardevolucao";
    }
    redirecionarParaMenu(): void {
        window.location.href = "/";
    }
    redirecionarParaLogin(): void {
        window.location.href = "/login";
    }
}
