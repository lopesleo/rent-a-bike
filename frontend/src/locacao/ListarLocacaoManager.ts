import { Locacao } from "./LocacaoModel.js";
import { Item } from "../item/Item.js";
import { ErroLocacao } from "../infra/ErroLocacao.js";
import { ErroItem } from "../infra/ErroItem.js";
import { ApiClient } from "../infra/ApiClient.js";

export class ListarLocacaoManager {
    public async getLocacoes(): Promise<Locacao[]> {
        try {
            return await ApiClient.get("/locacoes");
        } catch (error) {
            throw new ErroLocacao(
                `Erro ao buscar locações: ${(error as Error).message}`,
            );
        }
    }
    public async getItens(): Promise<Item[]> {
        try {
            return await ApiClient.get("/itens");
        } catch (error) {
            throw new ErroItem(
                `Erro ao buscar itens: ${(error as Error).message}`,
            );
        }
    }
}
