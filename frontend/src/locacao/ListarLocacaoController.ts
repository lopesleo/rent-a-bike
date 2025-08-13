import { IListarLocacaoView } from "./interface/IListarLocacaoView.js";
import { ErroLocacao } from "../infra/ErroLocacao.js";
import { ErroCliente } from "../infra/ErroCliente.js";
import { ErroItem } from "../infra/ErroItem.js";
import { ErroFuncionario } from "../infra/ErroFuncionario.js";
import { Locacao } from "./LocacaoModel.js";
import { Cliente } from "../cliente/Cliente.js";
import { Item } from "../item/Item.js";
import { Funcionario } from "../funcionario/Funcionario.js";
import { ListarLocacaoManager } from "./ListarLocacaoManager.js";

export class ListarLocacaoController {
    private todasLocacoes: Locacao[] = [];
    constructor(
        private view: IListarLocacaoView,
        private manager: ListarLocacaoManager,
    ) {
        this.carregar();
    }

    public async carregar(): Promise<void> {
        try {
            const locacoesJson = await this.manager.getLocacoes();
            const itensJson = await this.manager.getItens();
            function getItemById(id: number) {
                const ItemProcurado = itensJson.find(
                    (i: any) => Number(i.id) === Number(id),
                );
                if (!ItemProcurado)
                    throw new ErroItem(`Item com id ${id} não encontrado`);
                return new Item(
                    ItemProcurado.id,
                    ItemProcurado.codigo,
                    ItemProcurado.modelo,
                    ItemProcurado.valor_hora,
                    ItemProcurado.disponivel,
                );
            }
            const locacoes = locacoesJson.map(
                (l: any) =>
                    new Locacao(
                        Number(l.id),
                        new Cliente(
                            Number(l.cliente.id),
                            l.cliente.nome,
                            l.cliente.cpf ?? "",
                            l.cliente.telefone ?? "",
                            l.cliente.email ?? "",
                            l.cliente.endereco ?? "",
                            l.cliente.data_nascimento ??
                                l.cliente.dataNascimento ??
                                "",
                            l.cliente.foto_path ?? "",
                            l.cliente.doc_path ?? "",
                        ),
                        new Funcionario(
                            Number(l.funcionario.id),
                            l.funcionario.codigo ?? "",
                            l.funcionario.nome,
                            l.funcionario.telefone ?? "",
                            l.funcionario.email ?? "",
                            l.funcionario.cpf ?? "",
                            l.funcionario.cargo ?? "",
                        ),
                        l.data_hora_locacao ?? l.dataHoraLocacao ?? "",
                        l.horas_contratadas ?? l.horasContratadas ?? 0,
                        l.data_hora_entrega_prevista ??
                            l.dataHoraEntregaPrevista ??
                            "",
                        l.desconto_aplicado ?? l.descontoAplicado ?? 0,
                        l.valor_total_previsto ?? l.valorTotalPrevisto ?? 0,
                        l.status ?? "",
                        (l.itens ?? []).map((item: any) => {
                            if (item.codigo) {
                                return new Item(
                                    Number(item.id),
                                    item.codigo,
                                    item.modelo,
                                    item.valorHora,
                                    item.disponivel,
                                );
                            } else {
                                return getItemById(Number(item.id));
                            }
                        }),
                    ),
            );
            this.todasLocacoes = locacoes;
            this.view.exibirLocacoes(locacoes);
            this.configurarFiltro();
        } catch (erro) {
            if (erro instanceof ErroLocacao) {
                alert(`Falha ao listar locações: ${erro.message}`);
            } else if (erro instanceof ErroCliente) {
                alert(`Falha ao processar cliente: ${erro.message}`);
            } else if (erro instanceof ErroItem) {
                alert(`Falha ao processar item: ${erro.message}`);
            } else if (erro instanceof ErroFuncionario) {
                alert(`Falha ao processar funcionário: ${erro.message}`);
            } else {
                alert("Falha ao listar locações: erro desconhecido.");
            }
        }
    }

    private configurarFiltro(): void {
        const input = document.getElementById(
            "filtro-locacoes",
        ) as HTMLInputElement;
        const select = document.getElementById(
            "campo-filtro",
        ) as HTMLSelectElement;
        const clearBtn = document.getElementById(
            "clear-filtro",
        ) as HTMLButtonElement;
        const debounce = <F extends (...args: any[]) => void>(
            fn: F,
            ms = 200,
        ): F => {
            let timeoutId: number;
            return ((...args: any[]) => {
                clearTimeout(timeoutId);
                timeoutId = window.setTimeout(() => fn(...args), ms);
            }) as F;
        };
        const normalizeText = (s: string) =>
            s
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .toLowerCase();
        const filtrar = () => {
            const raw = input.value.trim();
            if (!raw) {
                this.view.exibirLocacoes(this.todasLocacoes);
                return;
            }
            const rawNum = raw.replace(/\D/g, "");
            let resultados: Locacao[] = [];
            switch (select.value) {
                case "id":
                    if (rawNum) {
                        resultados = this.todasLocacoes.filter((l) =>
                            l.id.toString().startsWith(rawNum),
                        );
                    }
                    break;
                case "cpf":
                    if (rawNum) {
                        resultados = this.todasLocacoes.filter((l) => {
                            const cpfNum = (l.cliente.cpf || "").replace(
                                /\D/g,
                                "",
                            );
                            return cpfNum.startsWith(rawNum);
                        });
                    }
                    break;
                case "nome":
                    const termNorm = normalizeText(raw);
                    resultados = this.todasLocacoes.filter((l) =>
                        normalizeText(l.cliente.nome).includes(termNorm),
                    );
                    break;
            }
            this.view.exibirLocacoes(resultados);
        };
        input.addEventListener("input", debounce(filtrar, 250));
        select.addEventListener("change", () => {
            input.value = "";
            filtrar();
            input.focus();
        });
        clearBtn.addEventListener("click", () => {
            input.value = "";
            filtrar();
            input.focus();
        });
    }
}
