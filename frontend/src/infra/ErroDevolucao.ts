export class ErroDevolucao extends Error {
    constructor(mensagem: string) {
        super(mensagem);
        this.name = "ErroDevolucao";
    }
}
