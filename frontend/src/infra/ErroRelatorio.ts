export class ErroRelatorio extends Error {
    constructor(mensagem: string) {
        super(mensagem);
        this.name = "ErroRelatorio";
    }
}
