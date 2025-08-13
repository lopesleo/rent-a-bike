export interface ILoginView {
    realizarLogin(): void;
    inicializarComponentes(): Promise<void>;
    limparCampos(): void;
    getUsuarioInput(): string;
    getSenhaInput(): string;
    exibirErro(mensagem: string): void;
    exibirMensagemSucesso(mensagem: string): void;
    redirecionarParaListarLocacao(): void;
    redirecionarParaListarDevolucao(): void;
    redirecionarParaRegistrarLocacao(): void;
    redirecionarParaRegistrarDevolucao(): void;
    redirecionarParaMenu(): void;
}
