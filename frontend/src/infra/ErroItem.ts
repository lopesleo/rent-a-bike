export class ErroItem extends Error {
    constructor(mensagem: string) {
        super(mensagem);
        this.name = "ErroItem";
    }
}
