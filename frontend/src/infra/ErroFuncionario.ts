export class ErroFuncionario extends Error {
    constructor(mensagem: string) {
        super(mensagem);
        this.name = "ErroFuncionario";
    }
}
