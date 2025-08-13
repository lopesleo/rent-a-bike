export class ErroLogin extends Error {
    constructor(mensagem: string) {
        super(mensagem);
        this.name = "ErroLogin";
    }
}
