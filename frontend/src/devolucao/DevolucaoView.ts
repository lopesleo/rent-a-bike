import { Item } from "../item/Item";
import { IDevolucaoView } from "./interface/IDevolucaoView";
import { DevolveCPFMascarado } from "../Utils/CPFUtils";
import { AutenticadorUtils } from "../Utils/AutenticadorUtils";
import { DevolucaoController } from "./DevolucaoController";
import { DevolucaoManager } from "./DevolucaoManager";
import { ErroFuncionario } from "../infra/ErroFuncionario";
import { IAvaria } from "./interface/IAvaria";
import Modal from "bootstrap/js/dist/modal";

export class DevolucaoView implements IDevolucaoView {
    private controller: DevolucaoController;
    private autenticador = new AutenticadorUtils();
    private avariasMap = new Map<number, IAvaria[]>();
    private itemCorrente!: Item;
    private itensDaLocacao: Item[] = [];
    private indiceAvariaEmEdicao: number | null = null;
    private resumoAtual = { totalHoras: 0, desconto: 0 };
    private funcionarios: { id: number; nome: string }[] = [];
    constructor() {
        this.controller = new DevolucaoController(this, new DevolucaoManager());
        this.configurarEventListeners();
    }

    exibirDevolucao(
        locacaoId: number,
        locacaoHoras: number,
        nomeCliente: string,
        cpfCliente: string,
        dataHoraEntregaPrevista: string,
        dataHoraDevolucao: string,
        itens: Item[],
    ): void {
        //tentar logar tudo
        this.itensDaLocacao = itens;
        const tbody = document.getElementById(
            "itens-locacao",
        ) as HTMLTableSectionElement;
        if (!tbody) return;
        tbody.innerHTML = "";
        let valorTotal = 0;
        itens.forEach((item) => {
            const tr = document.createElement("tr");
            const totalItem = item.valor_hora * (locacaoHoras ?? 1);
            valorTotal += totalItem;
            tr.innerHTML = `
            <td>${item.modelo} (${item.codigo})</td>
            <td>R$ ${item.valor_hora.toFixed(2)}</td>
            <td>${locacaoHoras ?? 1}</td>
            <td>R$ ${totalItem.toFixed(2)}</td>
        `;
            tbody.appendChild(tr);
        });

        const trResumo = document.createElement("tr");
        trResumo.innerHTML = `
            <td colspan="3" class="text-end"><strong>Total:</strong></td>
            <td><strong>R$ ${valorTotal.toFixed(2)}</strong></td>`;
        tbody.appendChild(trResumo);

        const infoDiv = document.getElementById("info-devolucao");
        if (infoDiv) {
            infoDiv.innerHTML = `
                        <p><strong>ID Locação:</strong> ${locacaoId}</p>
                        <p><strong>Cliente:</strong> ${nomeCliente} (${DevolveCPFMascarado(
                            cpfCliente,
                        )})</p>
                    `;
        }
        const previstoDate = new Date(
            dataHoraEntregaPrevista.replace(" ", "T"),
        );

        const formatoBR = {
            day: "2-digit" as const,
            month: "2-digit" as const,
            year: "numeric" as const,
            hour: "2-digit" as const,
            minute: "2-digit" as const,
            second: "2-digit" as const,
            hour12: false as const,
        };
        const previstoFormatado = previstoDate.toLocaleString(
            "pt-BR",
            formatoBR,
        );

        const elPrevisto = document.getElementById(
            "horario-previsto-devolucao",
        );
        if (elPrevisto) {
            elPrevisto.textContent = previstoFormatado;
        }

        const elDevolucao = document.getElementById("horario-devolucao");
        if (elDevolucao) {
            elDevolucao.textContent = dataHoraDevolucao;
        }

        const elHorasLocacao = document.getElementById("horas-locacao");
        if (elHorasLocacao) {
            elHorasLocacao.textContent = (locacaoHoras ?? 1).toString();
        }
    }
    exibirResumo(
        totalHoras: number,
        horasExtras: number,
        desconto: number,
        valorTotal: number,
    ): void {
        this.resumoAtual = { totalHoras, desconto };
        (document.getElementById("horas-atraso") as HTMLElement).textContent =
            totalHoras.toString();
        (document.getElementById("atraso") as HTMLElement).textContent =
            horasExtras > 0 ? `${horasExtras}` : "Sem atraso";
        (document.getElementById("desconto") as HTMLElement).textContent =
            `R$ ${desconto.toFixed(2)}`;
        (document.getElementById("valor-pagar") as HTMLElement).textContent =
            `R$ ${valorTotal.toFixed(2)}`;
        this.atualizarResumoFinanceiro();
    }
    private atualizarResumoFinanceiro(): void {
        const subtotalItens = this.itensDaLocacao.reduce(
            (acc, i) => acc + i.valor_hora * this.resumoAtual.totalHoras,
            0,
        );

        let custoAvarias = 0;
        this.avariasMap.forEach((lista) => {
            custoAvarias += lista.reduce(
                (acc, avaria) => acc + avaria.valor,
                0,
            );
        });
        let custoLimpeza = 0;
        this.itensDaLocacao.forEach((item) => {
            if (this.isLimpezaAplicada(item.id)) {
                custoLimpeza +=
                    item.valor_hora * this.resumoAtual.totalHoras * 0.1;
            }
        });
        const valorTotal =
            subtotalItens -
            this.resumoAtual.desconto +
            custoAvarias +
            custoLimpeza;

        (document.getElementById("desconto") as HTMLElement).textContent =
            `R$ ${this.resumoAtual.desconto.toFixed(2)}`;
        (document.getElementById("custo-avarias") as HTMLElement).textContent =
            `R$ ${custoAvarias.toFixed(2)}`;
        (document.getElementById("custo-limpeza") as HTMLElement).textContent =
            `R$ ${custoLimpeza.toFixed(2)}`;
        (document.getElementById("valor-pagar") as HTMLElement).textContent =
            `R$ ${valorTotal.toFixed(2)}`;
    }

    public async carregarAvaliador() {
        try {
            await this.preencherAvaliador();
        } catch (e) {
            if (e instanceof ErroFuncionario) {
                alert(e.message);
            } else {
                alert("Erro ao carregar avaliadores.");
            }
        }
    }

    private configurarEventListeners(): void {
        const tabelaItens = document.getElementById("itens-locacao");
        if (!tabelaItens) return;

        tabelaItens.addEventListener("click", (event) => {
            const target = event.target as HTMLElement;

            const botaoExcluir = target.closest<HTMLButtonElement>(
                ".btn-excluir-avaria",
            );
            if (botaoExcluir) {
                const itemId = parseInt(botaoExcluir.dataset.itemId!);
                const avariaIndex = parseInt(botaoExcluir.dataset.avariaIndex!);
                this.excluirAvaria(itemId, avariaIndex);
                return;
            }

            const botaoEditar =
                target.closest<HTMLButtonElement>(".btn-editar-avaria");
            if (botaoEditar) {
                const itemId = parseInt(botaoEditar.dataset.itemId!);
                const avariaIndex = parseInt(botaoEditar.dataset.avariaIndex!);
                this.editarAvaria(itemId, avariaIndex);
            }
        });

        tabelaItens.addEventListener("change", (event) => {
            const target = event.target as HTMLElement;
            if (target.matches('input[type="checkbox"][id^="limpeza-"]')) {
                this.atualizarResumoFinanceiro();
            }
        });
    }

    public atualizarItensEResumo(
        itens: Item[],
        totalHoras: number,
        desconto: number,
        valorTotal: number,
    ): void {
        this.itensDaLocacao = itens;
        this.resumoAtual = { totalHoras, desconto };

        const tbody = document.getElementById(
            "itens-locacao",
        ) as HTMLTableSectionElement;
        tbody.innerHTML = "";

        const isGerente =
            this.autenticador.getFuncionario()?.cargo.toUpperCase() ===
            "GERENTE";

        let subtotal = 0;

        itens.forEach((item) => {
            const totalItem = item.valor_hora * totalHoras;
            subtotal += totalItem;

            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${item.modelo} (${item.codigo})</td>
                <td>R$ ${item.valor_hora.toFixed(2)}</td>
                <td>${totalHoras}</td>
                <td>R$ ${totalItem.toFixed(2)}</td>
                <td><input type="checkbox" id="limpeza-${item.id}" class="form-check-input"></td>
                <td></td> 
            `;
            tbody.appendChild(tr);

            if (isGerente) {
                const actionCell = tr.cells[tr.cells.length - 1];
                const btnAvaria = document.createElement("button");
                btnAvaria.type = "button";
                btnAvaria.textContent = "+ Avaria";
                btnAvaria.classList.add("btn", "btn-sm", "btn-warning");
                btnAvaria.onclick = () => this.abrirModalAvaria(item.id);
                actionCell.appendChild(btnAvaria);
            }

            const trAvarias = document.createElement("tr");
            const tdAvarias = document.createElement("td");

            tdAvarias.colSpan = tr.cells.length;
            tdAvarias.innerHTML = `<div class="avarias-container" id="avarias-list-${item.id}"></div>`;
            trAvarias.appendChild(tdAvarias);
            tbody.appendChild(trAvarias);

            this.renderizarAvariasParaItem(item.id);
        });

        const trResumo = document.createElement("tr");
        const colspan = tbody.querySelector("tr")!.cells.length - 1;
        trResumo.innerHTML = `
            <td colspan="${colspan}" class="text-end"><strong>Subtotal:</strong></td>
            <td><strong>R$ ${subtotal.toFixed(2)}</strong></td>
        `;
        tbody.appendChild(trResumo);

        this.atualizarResumoFinanceiro();
    }

    private renderizarAvariasParaItem(itemId: number): void {
        const container = document.getElementById(`avarias-list-${itemId}`);
        if (!container) return;

        const avarias = this.avariasMap.get(itemId) || [];
        container.innerHTML = "";

        if (avarias.length > 0) {
            console.log(this.funcionarios);
            const listaHtml = avarias
                .map((avaria, index) => {
                    const nomeFuncionario =
                        this.funcionarios.find(
                            (f) => f.id == avaria.funcionario_id,
                        )?.nome || "Desconhecido";
                    return `
                    <div class="d-flex justify-content-between align-items-center p-2 border-top bg-light">
                        <span class="text-muted small">
                            ↳ ${avaria.descricao} (R$ ${avaria.valor.toFixed(2)}) 
                            <br>
                            <small><i>Registrado por: ${nomeFuncionario} </i></small>
                        </span>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-editar-avaria" data-item-id="${itemId}" data-avaria-index="${index}">Editar</button>
                            <button type="button" class="btn btn-sm btn-outline-danger ms-2 btn-excluir-avaria" data-item-id="${itemId}" data-avaria-index="${index}">Excluir</button>
                        </div>
                    </div>
                `;
                })
                .join("");
            container.innerHTML = `<div class="mt-0 mb-2">${listaHtml}</div>`;
        }
    }

    /** retorna true se o usuário marcou limpeza para este item */
    public isLimpezaAplicada(itemId: number): boolean {
        const cb = document.getElementById(
            `limpeza-${itemId}`,
        ) as HTMLInputElement | null;
        return cb ? cb.checked : false;
    }

    public getItensComAvarias(): Array<{ id: number; avarias: IAvaria[] }> {
        return Array.from(this.avariasMap.entries()).map(([id, avarias]) => ({
            id,
            avarias,
        }));
    }

    private criarModalAvaria(): void {
        document.body.insertAdjacentHTML(
            "beforeend",
            `
      <div class="modal fade" id="modalAvaria" tabindex="-1" aria-labelledby="modalAvariaLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form id="formAvaria">
              <div class="modal-header">
                <h5 class="modal-title" id="modalAvariaLabel">Registrar Avaria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="descricaoAvaria" class="form-label">Descrição</label>
                  <textarea id="descricaoAvaria" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                  <label for="valorAvaria" class="form-label">Valor (R$)</label>
                  <input id="valorAvaria" type="number" step="0.01" class="form-control" required  min="0.01" ">
                </div>
                <div class="mb-3">
                  <label for="fotoAvaria" class="form-label">Foto (JPG)</label>
                  <input id="fotoAvaria" type="file" accept=".jpg" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="avaliadorAvaria" class="form-label">Avaliador</label>
                  <select id="avaliadorAvaria" class="form-select" required></select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    `,
        );

        const form = document.getElementById("formAvaria") as HTMLFormElement;
        form.addEventListener("submit", (e) => {
            e.stopPropagation();
            e.preventDefault();
            this.salvarAvaria();
        });
    }

    private async salvarAvaria(): Promise<void> {
        const desc = (
            document.getElementById("descricaoAvaria") as HTMLTextAreaElement
        ).value;
        const valor = parseFloat(
            (document.getElementById("valorAvaria") as HTMLInputElement).value,
        );
        const fotoInput = document.getElementById(
            "fotoAvaria",
        ) as HTMLInputElement;
        const arquivo = fotoInput.files?.[0];
        const funcionario_id = parseInt(
            (document.getElementById("avaliadorAvaria") as HTMLSelectElement)
                .value,
        );

        let fotoBase64: string;

        if (arquivo) {
            fotoBase64 = await new Promise<string>((res, rej) => {
                const reader = new FileReader();
                reader.onload = () =>
                    res((reader.result as string).split(",")[1]);
                reader.onerror = (err) => rej(err);
                reader.readAsDataURL(arquivo);
            });
        } else if (this.indiceAvariaEmEdicao !== null) {
            const avarias = this.avariasMap.get(this.itemCorrente.id) || [];
            fotoBase64 = avarias[this.indiceAvariaEmEdicao].foto;
        } else {
            alert("É obrigatório selecionar uma foto (JPG) para a avaria.");
            return;
        }

        const avaria: IAvaria = {
            funcionario_id,
            descricao: desc,
            valor,
            foto: fotoBase64,
        };
        const lista = this.avariasMap.get(this.itemCorrente.id) || [];

        if (this.indiceAvariaEmEdicao !== null) {
            lista[this.indiceAvariaEmEdicao] = avaria;
        } else {
            lista.push(avaria);
        }

        this.avariasMap.set(this.itemCorrente.id, lista);

        this.renderizarAvariasParaItem(this.itemCorrente.id);
        this.atualizarResumoFinanceiro();

        const modalEl = document.getElementById("modalAvaria");
        if (modalEl) {
            Modal.getInstance(modalEl)?.hide();
        }
    }

    private editarAvaria(itemId: number, avariaIndex: number): void {
        this.indiceAvariaEmEdicao = avariaIndex;
        this.abrirModalAvaria(itemId);
    }

    private excluirAvaria(itemId: number, avariaIndex: number): void {
        if (!confirm("Tem certeza que deseja excluir esta avaria?")) return;

        const lista = this.avariasMap.get(itemId);
        if (lista) {
            lista.splice(avariaIndex, 1);
            this.avariasMap.set(itemId, lista);

            this.renderizarAvariasParaItem(itemId);
            this.atualizarResumoFinanceiro();
        }
    }
    private async abrirModalAvaria(itemId: number): Promise<void> {
        const itemEncontrado = this.itensDaLocacao.find((i) => i.id === itemId);
        if (!itemEncontrado) {
            alert("Erro: Não foi possível encontrar o item selecionado.");
            return;
        }
        this.itemCorrente = itemEncontrado;

        this.criarModalAvaria();
        await this.preencherAvaliador();

        const modalTitle = document.querySelector("#modalAvariaLabel")!;
        const saveButton = document.querySelector(
            "#formAvaria button[type='submit']",
        )!;
        const fotoInput = document.getElementById(
            "fotoAvaria",
        ) as HTMLInputElement;

        if (this.indiceAvariaEmEdicao !== null) {
            modalTitle.textContent = `Editar Avaria - ${this.itemCorrente.modelo}`;
            saveButton.textContent = "Atualizar";
            fotoInput.required = false;

            const avaria =
                this.avariasMap.get(itemId)?.[this.indiceAvariaEmEdicao];
            if (avaria) {
                (
                    document.getElementById(
                        "descricaoAvaria",
                    ) as HTMLInputElement
                ).value = avaria.descricao;
                (
                    document.getElementById("valorAvaria") as HTMLInputElement
                ).value = avaria.valor.toString();
                (
                    document.getElementById(
                        "avaliadorAvaria",
                    ) as HTMLSelectElement
                ).value = avaria.funcionario_id.toString();
            }
        } else {
            modalTitle.textContent = `Registrar Avaria - ${this.itemCorrente.modelo}`;
            saveButton.textContent = "Salvar";
            fotoInput.required = true;
        }

        const modalEl = document.getElementById("modalAvaria")!;
        const bsModal = new Modal(modalEl);
        bsModal.show();

        modalEl.addEventListener(
            "hidden.bs.modal",
            () => {
                bsModal.dispose();
                modalEl.remove();
                this.indiceAvariaEmEdicao = null;
            },
            { once: true },
        );
    }

    public async preencherFuncionarios() {
        let funcionario = this.autenticador.getFuncionario();
        const select = document.getElementById(
            "funcionario",
        ) as HTMLSelectElement;
        select.innerHTML = "";
        if (funcionario) {
            const opt = document.createElement("option");
            opt.value = funcionario.id.toString();
            opt.textContent = funcionario.nome;
            opt.selected = true;
            select.appendChild(opt);
        }
    }

    public async preencherAvaliador(): Promise<void> {
        const select = document.getElementById(
            "avaliadorAvaria",
        ) as HTMLSelectElement;
        select.innerHTML = "";

        try {
            this.funcionarios = await this.controller.carregarFuncionarios();
            console.log(
                "Preenchendo avaliador com funcionários:",
                this.funcionarios,
            );

            for (const func of this.funcionarios) {
                const option = document.createElement("option");
                option.value = String(func.id);
                option.textContent = func.nome;
                select.appendChild(option);
            }
        } catch (err) {
            console.error("Erro ao carregar avaliadores:", err);
            alert("Não foi possível carregar a lista de avaliadores.");
        }
    }

    public getForm(): HTMLFormElement {
        const form = document.getElementById(
            "form-devolucao",
        ) as HTMLFormElement;
        if (!form) throw new Error("Formulário não encontrado");
        return form;
    }
    public getFuncionarioId(): number {
        const funcionario = this.autenticador.getFuncionario();
        if (!funcionario) {
            throw new ErroFuncionario("Funcionario não autenticado");
        }
        return funcionario.id;
    }

    private async preencherFuncionario() {
        let funcionario = this.autenticador.getFuncionario();
        const div = document.getElementById("funcionario") as HTMLDivElement;
        div.innerHTML = "";
        if (funcionario) {
            const span = document.createElement("span");
            span.textContent = funcionario.nome;
            div.appendChild(span);
        }
    }
}
