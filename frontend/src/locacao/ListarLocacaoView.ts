import { AutenticadorUtils } from "../Utils/AutenticadorUtils";
import { IListarLocacaoView } from "./interface/IListarLocacaoView";
import { ListarLocacaoController } from "./ListarLocacaoController";
import { ListarLocacaoManager } from "./ListarLocacaoManager";
import { Locacao } from "./LocacaoModel";

export class ListarLocacaoView implements IListarLocacaoView {
    private autenticador: AutenticadorUtils;
    private controller: ListarLocacaoController;
    constructor() {
        this.autenticador = new AutenticadorUtils();
        this.controller = new ListarLocacaoController(
            this,
            new ListarLocacaoManager(),
        );
    }

    exibirLocacoes(locacoes: Locacao[]): void {
        const corpo = document.getElementById("tabela-locacoes")!;
        corpo.innerHTML = "";

        for (const l of locacoes) {
            const dataLoc = new Date(
                l.dataHoraLocacao.replace(" ", "T"),
            ).toLocaleString("pt-BR", {});
            const dataEnt = new Date(
                l.dataHoraEntregaPrevista.replace(" ", "T"),
            ).toLocaleString("pt-BR", {});

            const linha = document.createElement("tr");
            linha.innerHTML = `
            <td>${l.id}</td>
            <td>${dataLoc}</td>
            <td>${l.horasContratadas}</td>
            <td>${dataEnt}</td>
            <td>${l.cliente.nome}</td>
            <td>${l.cliente.telefone || "-"}</td>
            <td>${l.cliente.cpf || "-"}</td>
        `;
            const tdBotao = document.createElement("td");
            if (this.autenticador.validarCargo(["ATENDENTE", "GERENTE"])) {
                if (l.status === "EM_ANDAMENTO") {
                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = "btn btn-warning btn-sm";
                    btn.textContent = "Devolver";
                    btn.addEventListener("click", () => {
                        localStorage.setItem(
                            "devolucaoEmCurso",
                            JSON.stringify({
                                locacaoId: l.id,
                                locacaoHoras: l.horasContratadas,
                                dataHoraEntregaPrevista:
                                    l.dataHoraEntregaPrevista,
                                clienteNome: l.cliente.nome,
                                clienteCpf: l.cliente.cpf,
                                itens: l.itens,
                            }),
                        );
                        window.location.href = "/registrardevolucao";
                    });
                    tdBotao.appendChild(btn);
                }
            }
            linha.appendChild(tdBotao);
            corpo.appendChild(linha);
        }
    }
}
