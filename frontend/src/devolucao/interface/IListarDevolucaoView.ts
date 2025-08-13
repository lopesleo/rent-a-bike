import { ListarDevolucaoModel } from "../ListarDevolucaoModel";

export interface IListarDevolucaoView {
    exibirDevolucoes(devolucoes: ListarDevolucaoModel[]): void;
}
