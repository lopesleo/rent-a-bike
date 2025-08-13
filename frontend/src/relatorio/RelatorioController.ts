import { ErroRelatorio } from "../infra/ErroRelatorio.js";
import { IRelatorioView } from "./interface/IRelatorioView.js";
import { RelatorioManager } from "./RelatorioManager.js";

export class RelatorioController {
    private manager: RelatorioManager;
    constructor(private view: IRelatorioView) {
        this.manager = new RelatorioManager();
    }

    async obterTopItensNoPeriodo(
        dataInicial: string,
        dataFinal: string,
    ): Promise<any[]> {
        try {
            return await this.manager.obterTopItensNoPeriodo(
                dataInicial,
                dataFinal,
            );
        } catch (error) {
            throw new ErroRelatorio("Erro ao obter top itens");
        }
    }

    async obterLocacoesNoPeriodo(
        dataInicial: string,
        dataFinal: string,
    ): Promise<any[]> {
        try {
            return await this.manager.obterLocacoesNoPeriodo(
                dataInicial,
                dataFinal,
            );
        } catch (error) {
            throw new ErroRelatorio("Erro ao obter locações");
        }
    }
}
