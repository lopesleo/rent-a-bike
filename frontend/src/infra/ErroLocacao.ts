export class ErroLocacao extends Error {
    constructor(mensagem: string) {
        super(mensagem);
        this.name = "ErroLocacao";
    }
}
