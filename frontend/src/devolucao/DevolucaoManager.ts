import { ErroDevolucao } from "../infra/ErroDevolucao";
import { ErroFuncionario } from "../infra/ErroFuncionario";
import { ApiClient } from "../infra/ApiClient";

export interface PrecoItem {
    id: number;
    valorHora: number;
}

export class DevolucaoManager {
    async carregaValorHoraItemLocacao(id: number): Promise<Array<PrecoItem>> {
        try {
            const itensJson: Array<{ item_id: number; valor_hora: number }> =
                await ApiClient.get(`/locacoes/${id}/itens`);

            return itensJson.map(({ item_id, valor_hora }) => ({
                id: item_id,
                valorHora: valor_hora,
            }));
        } catch (error) {
            throw new ErroDevolucao("Erro ao carregar itens da locação.");
        }
    }

    async enviarDevolucao(payload: {
        locacao_id: number;
        funcionario_id: number;
    }): Promise<void> {
        return await ApiClient.post(`/devolucoes`, payload);
    }

    async carregarFuncionarios(): Promise<Array<{ id: number; nome: string }>> {
        try {
            return await ApiClient.get("/funcionarios");
        } catch (error) {
            throw new ErroFuncionario("Erro ao buscar funcionários.");
        }
    }
}
