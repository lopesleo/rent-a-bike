import { Item } from "../../item/Item";
import { IAvaria } from "./IAvaria";

export interface IDevolucaoView {
    exibirDevolucao(
        locacaoId: number,
        locacaoHoras: number,
        nomeCliente: string,
        cpfCliente: string,
        dataHoraEntregaPrevista: string,
        dataHoraDevolucao: string,
        itens: Item[],
    ): void;
    exibirResumo(
        totalHoras: number,
        horasExtras: number,
        desconto: number,
        valorTotal: number,
    ): void;
    atualizarItensEResumo(
        itens: Item[],
        totalHoras: number,
        desconto: number,
        valorTotal: number,
    ): void;
    getForm(): HTMLFormElement;
    getFuncionarioId(): number;
    preencherFuncionarios(): Promise<void>;
    isLimpezaAplicada(itemId: number): boolean;

    getItensComAvarias(): Array<{ id: number; avarias: IAvaria[] }>;
}
