import { ListarDevolucaoController } from "./ListarDevolucaoController.js";
import { ListarDevolucaoModel } from "./ListarDevolucaoModel.js";
export class ListarDevolucaoView {
    private controller: ListarDevolucaoController;
    constructor() {
        this.controller = new ListarDevolucaoController(this);
    }
    exibirDevolucoes(devolucoes: ListarDevolucaoModel[]): void {
        const tabela = document.getElementById(
            "tabela-devolucoes",
        ) as HTMLTableElement;
        if (!tabela) {
            return;
        }
        tabela.innerHTML = "";

        devolucoes.forEach((devolucao) => {
            const linha = document.createElement("tr");
            linha.innerHTML = `
                <td>${devolucao.id}</td>
                <td>${devolucao.dataHoraDevolucao}</td>
                <td>${devolucao.numeroLocacao}</td>
                <td>${devolucao.nomeCliente}</td>
                <td> R$ ${devolucao.valorPago.toFixed(2)}</td>
            `;
            tabela.appendChild(linha);
        });
    }
}
