export class ListarDevolucaoModel {
    id!: number;
    dataHoraDevolucao!: string;
    numeroLocacao!: number;
    nomeCliente!: string;
    valorPago!: number;

    constructor(
        id: number,
        dataHoraDevolucao: string,
        numeroLocacao: number,
        nomeCliente: string,
        valorPago: number,
    ) {
        this.id = id;
        this.dataHoraDevolucao = dataHoraDevolucao;
        this.numeroLocacao = numeroLocacao;
        this.nomeCliente = nomeCliente;
        this.valorPago = valorPago;
    }
}
