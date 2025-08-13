import { ErroDevolucao } from "../infra/ErroDevolucao.js";
import { ErroDominio } from "../infra/ErroDominio.js";
import { IListarDevolucaoView } from "./interface/IListarDevolucaoView.js";
import { ListarDevolucaoManager } from "./ListarDevolucaoManager.js";

export class ListarDevolucaoController {
    private manager: ListarDevolucaoManager;
    constructor(private view: IListarDevolucaoView) {
        this.manager = new ListarDevolucaoManager();
        this.carregar();
    }

    async carregar(): Promise<void> {
        try {
            const devolucoes = await this.manager.getDevolucoes();

            devolucoes.forEach((devolucao) => {
                devolucao.dataHoraDevolucao = new Date(
                    devolucao.dataHoraDevolucao,
                ).toLocaleString("pt-BR", {
                    timeZone: "America/Sao_Paulo",
                });
            });

            this.view.exibirDevolucoes(devolucoes);
        } catch (e) {
            if (e instanceof ErroDominio || e instanceof ErroDevolucao) {
                alert(e.message);
            } else {
                alert("Erro inesperado ao carregar as devoluções.");
            }
        }
    }
}
