import { ErroRelatorio } from "../infra/ErroRelatorio.js";
import { ApiClient } from "../infra/ApiClient.js";

export class RelatorioManager {
    async obterTopItensNoPeriodo(
        dataInicial: string,
        dataFinal: string,
    ): Promise<any[]> {
        try {
            return await ApiClient.get(
                `/relatorio/top-itens?dataInicial=${dataInicial}&dataFinal=${dataFinal}`,
            );
        } catch (error) {
            throw new ErroRelatorio((error as Error).message);
        }
    }

    async obterLocacoesNoPeriodo(
        dataInicial: string,
        dataFinal: string,
    ): Promise<any[]> {
        try {
            return await ApiClient.get(
                `/relatorio/locacoes?dataInicial=${dataInicial}&dataFinal=${dataFinal}`,
            );
        } catch (error) {
            throw new ErroRelatorio((error as Error).message);
        }
    }
}
