export class ErroCliente extends Error {
    constructor(mensagem: string) {
        super(mensagem);
        this.name = "ErroCliente";
    }
}
