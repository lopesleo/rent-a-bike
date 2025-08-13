export interface IRelatorioView {
    preencherDataInicial(): Promise<void>;
    preencherDataFinal(): Promise<void>;
    exibirGraficoRelatorioTopItens(dados: string): Promise<void>;
    exibirGraficoRelatorioLocacaoesNoPeriodo(dados: string): Promise<void>;
}
