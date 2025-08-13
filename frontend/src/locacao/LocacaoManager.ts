import { Cliente } from "../cliente/Cliente.js";
import { Item } from "../item/Item.js";
import { ErroCliente } from "../infra/ErroCliente.js";
import { ErroItem } from "../infra/ErroItem.js";
import { ErroLocacao } from "../infra/ErroLocacao.js";
import { ApiClient } from "../infra/ApiClient.js";

export class LocacaoManager {


    async getClientes(): Promise<Cliente[]> {
        try {
            return await ApiClient.get("/clientes");
        } catch (error) {
            throw new ErroCliente("Erro ao buscar clientes.");
        }
    }

    async getItens(): Promise<Item[]> {
        try {
            return await ApiClient.get("/itens");
        } catch (error) {
            throw new ErroItem("Erro ao buscar itens.");
        }
    }

    async registrarLocacao(body: {
        cliente_id: number;
        funcionario_id: number;
        horas_contratadas: number;
        itens: Array<{ id: number }>;
    }): Promise<void> {
        try {
            await ApiClient.post("/locacoes", body);
        } catch (error) {
            if (error instanceof Error) {
                throw new ErroLocacao(error.message);
            }
            throw new ErroLocacao(
                "Ocorreu um erro desconhecido ao registrar a locação.",
            );
        }
    }
}
