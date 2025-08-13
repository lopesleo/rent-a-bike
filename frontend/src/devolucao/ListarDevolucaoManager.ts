import { ApiClient } from "../infra/ApiClient";
import { ErroDominio } from "../infra/ErroDominio";
import { ListarDevolucaoModel } from "./ListarDevolucaoModel";

export class ListarDevolucaoManager {
    public async getDevolucoes(): Promise<ListarDevolucaoModel[]> {
        const devolucoes = await ApiClient.get("/devolucoes");

        return devolucoes.map(
            (d: any) =>
                new ListarDevolucaoModel(
                    Number(d.id),
                    d.data_hora_devolucao,
                    Number(d.numero_locacao),
                    d.nome_cliente,
                    Number(d.valor_pago),
                ),
        );
    }
}
