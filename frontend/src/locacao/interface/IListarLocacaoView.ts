import { Locacao } from "../LocacaoModel";

export interface IListarLocacaoView {
    exibirLocacoes(locacoes: Locacao[]): void;
}
