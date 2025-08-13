import { Cliente } from "../cliente/Cliente.js";
import { Item } from "../item/Item.js";
import { AutenticadorUtils } from "../Utils/AutenticadorUtils.js";
import { ILocacaoView } from "./interface/IlocacaoView.js";
import { LocacaoController } from "./LocacaoController.js";
import { LocacaoManager } from "./LocacaoManager.js";
import { Modal } from "bootstrap";
export class LocacaoView implements ILocacaoView {
    private controller: LocacaoController;
    private autenticador = new AutenticadorUtils();
    constructor() {
        this.controller = new LocacaoController(this, new LocacaoManager());
        this.inicializarHtml();
    }

    async inicializarHtml() {
        await this.atribuirFuncionario();
        await this.preencherClientes();
        await this.preencherItens();
        this.configurarEventos();
        this.configurarModalDeImagem();
    }

    private configurarEventos() {
        const clienteInput = document.getElementById(
            "cliente",
        ) as HTMLInputElement;
        clienteInput?.addEventListener("input", () => this.onClienteInput());
        clienteInput?.addEventListener("blur", () => this.onClienteBlur());

        const itemInput = document.getElementById("item") as HTMLInputElement;
        itemInput?.addEventListener("input", () => this.onItemInput());
        itemInput?.addEventListener("blur", () => this.onItemBlur());

        const horasInput = document.getElementById("horas") as HTMLInputElement;
        horasInput?.addEventListener("input", () => this.atualizarResumo());

        const form = document.getElementById("form-locacao") as HTMLFormElement;
        form?.addEventListener("submit", (e) => this.enviarLocacao(e));
        this.atribuirFuncionario();

        this.removerItemHTML((e) => this.onRemoverItem(e));
    }

    private async atribuirFuncionario() {
        let funcionario = this.autenticador.getFuncionario();
        if (funcionario) {
            this.controller.setFuncionario(funcionario.id, funcionario.nome);
        }
    }

    private async preencherClientes() {
        const clientes = await this.controller.getClientes();
        const datalist = document.getElementById(
            "lista-clientes",
        ) as HTMLDataListElement;
        if (datalist) {
            datalist.innerHTML = "";
            clientes.forEach((c) => {
                const opt = document.createElement("option");
                opt.value = c.id.toString();
                opt.label = `${c.nome} (${c.cpf})`;
                datalist.appendChild(opt);
            });
        }
    }

    private async preencherItens() {
        const itens = await this.controller.getItens();
        const datalist = document.getElementById(
            "lista-itens",
        ) as HTMLDataListElement;
        if (datalist) {
            datalist.innerHTML = "";
            itens.forEach((i) => {
                const opt = document.createElement("option");
                opt.value = i.codigo;
                opt.label = `${i.modelo} - ${i.disponivel ? "Disp" : "Indisp"}`;
                datalist.appendChild(opt);
            });
        }
    }

    private onClienteInput() {
        const entrada = this.getClienteInput();
        const sugestoes = this.controller.sugerirClientes(entrada);
        this.exibirSugestoesClientes(sugestoes);
    }

    private onClienteBlur() {
        const entrada = this.getClienteInput();
        const cliente = this.controller.confirmarCliente(entrada);
        if (cliente) {
            this.exibirCliente(cliente);
            this.atualizarResumo();
        }
    }
    private onItemBlur() {
        try {
            const codigo = this.getItemInput();

            if (!codigo) return;

            const item = this.controller.confirmarItem(codigo);

            this.exibirItem(item);
            this.atualizarResumo();

            (document.getElementById("item") as HTMLInputElement).value = "";
        } catch (erro: any) {
            alert(erro.message);
            (document.getElementById("item") as HTMLInputElement).value = ""; // Limpa o campo
        }
    }

    private onItemInput() {
        const codigo = this.getItemInput();
        const sugestoes = this.controller.sugerirItens(codigo);
        this.exibirSugestoesItens(sugestoes);
    }

    onRemoverItem(e: Event) {
        const btn = e.currentTarget as HTMLButtonElement;
        const id = Number(btn.getAttribute("data-id"));
        this.controller.removerItem(id);

        const tabela = document.getElementById("itens-tabela")!;
        const linhaItem = tabela.querySelector(`tr[data-item-id="${id}"]`);

        const collapseId = `avarias-collapse-${id}`;
        const linhaAvarias = tabela.querySelector(`tr:has(#${collapseId})`);

        linhaItem?.remove();
        linhaAvarias?.remove();

        this.atualizarResumo();
    }
    private async enviarLocacao(e: Event) {
        e.preventDefault();
        try {
            const cliente = this.controller.confirmarCliente(
                this.getClienteInput(),
            );
            const horas = this.getHoras();
            if (!cliente) throw new Error("Cliente não selecionado");
            if (!horas || horas <= 0) throw new Error("Horas inválidas");
            if (!this.controller.getItensSelecionados().length)
                throw new Error("Nenhum item selecionado");

            await this.controller.registrar(horas);
            alert("Locação registrada!");
            window.location.reload();
        } catch (erro: any) {
            alert(erro.message || "Erro ao registrar locação.");
        }
    }

    private atualizarResumo() {
        const cliente = this.controller.confirmarCliente(
            this.getClienteInput(),
        );
        const itens = this.controller.getItensSelecionados();
        const horas = this.getHoras();
        if (!cliente || itens.length === 0 || isNaN(horas) || horas <= 0)
            return;
        const subtotal = this.controller.calcularSubtotal(horas);
        const desconto = this.controller.calcularDesconto(horas);
        const entrega = this.controller.calcularEntrega(horas);
        this.TelaDeRegistro(cliente, itens, subtotal, desconto, entrega);
    }

    exibirCliente(cliente: Cliente): void {
        const lista = document.getElementById(
            "lista-clientes",
        ) as HTMLDataListElement;
        if (lista) {
            lista.innerHTML = `<option value="${cliente.id}">${cliente.nome} - ${cliente.cpf}</option>`;
        }

        const nome = document.getElementById("cliente-nome");
        const cpf = document.getElementById("cliente-cpf");
        const telefone = document.getElementById("cliente-telefone");
        const endereco = document.getElementById("cliente-endereco");
        const email = document.getElementById("cliente-email");
        const cliente_foto = document.getElementById("cliente-foto");
        if (nome) nome.innerText = `Nome: ${cliente.nome}`;
        if (cpf) cpf.innerText = `CPF: ${cliente.cpf}`;
        if (telefone)
            telefone.innerText = `Telefone: ${cliente.telefone || "-"}`;
        if (endereco)
            endereco.innerText = `Endereço: ${cliente.endereco || "-"}`;
        if (email) email.innerText = `Email: ${cliente.email || "-"}`;
        if (cliente_foto) {
            cliente_foto.setAttribute("src", cliente.foto_path || " ");
            cliente_foto.setAttribute(
                "style",
                "display: block ; max-width: 230px; max-height: 230px",
            );
        }
    }
    private configurarModalDeImagem(): void {
        const modalImagemElement = document.getElementById("modal-imagem");
        if (!modalImagemElement) return;

        modalImagemElement.addEventListener("show.bs.modal", (event) => {
            const trigger = (event as any).relatedTarget;

            const imageUrl = trigger.getAttribute("data-imagem-url");
            const descricao = trigger.getAttribute("data-descricao");

            const modalImage = modalImagemElement.querySelector(
                "#modal-imagem-tag",
            ) as HTMLImageElement;
            const lightboxCaption = modalImagemElement.querySelector(
                "#lightbox-caption",
            ) as HTMLElement;

            modalImage.src = imageUrl;
            lightboxCaption.textContent = descricao;
        });
    }

    exibirItem(item: Item): void {
        const itensTab = document.getElementById("itens-tabela");
        if (!itensTab) return;

        const trItem = document.createElement("tr");
        trItem.setAttribute("data-item-id", item.id.toString());
        trItem.className = "linha-item";

        trItem.innerHTML = `
        <td>${item.modelo}</td>
        <td>${item.codigo}</td>
        <td>R$${item.valor_hora.toFixed(2)}/h</td>
        <td class="text-end" id="acoes-item-${item.id}">
            <button class="btn btn-danger btn-sm remover-item" data-id="${item.id}">Remover</button>
        </td>
    `;
        itensTab.appendChild(trItem);

        if (item.avarias && item.avarias.length > 0) {
            const collapseId = `avarias-collapse-${item.id}`;

            const celulaAcoes = document.getElementById(
                `acoes-item-${item.id}`,
            );
            if (celulaAcoes) {
                celulaAcoes.insertAdjacentHTML(
                    "afterbegin",
                    `
                <button 
                    class="btn btn-outline-secondary btn-sm me-2 btn-ver-avarias collapsed" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#${collapseId}" 
                    aria-expanded="false" 
                    aria-controls="${collapseId}">
                    Avarias (${item.avarias.length})
                </button>
            `,
                );
            }

            const trAvarias = document.createElement("tr");
            trAvarias.className = "linha-avarias-recolhida";

            const tdAvarias = document.createElement("td");
            tdAvarias.colSpan = 4;

            tdAvarias.innerHTML = `
            <div class="collapse" id="${collapseId}">
                <div class="avarias-container p-2">
                    <h6 class="avarias-titulo">Avarias registradas:</h6>
                    ${item.avarias
                        .map((avaria) => {
                            const fotoUrl = avaria.foto;
                            return `
                            <div class="avaria-item">
                                <img src="${fotoUrl}" 
                                     alt="${avaria.descricao}" 
                                     class="avaria-img img-thumbnail"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#modal-imagem" 
                                     data-imagem-url="${fotoUrl}" 
                                     data-descricao="${avaria.descricao}">
                                <div class="avaria-descricao">${avaria.descricao}</div>
                            </div>
                        `;
                        })
                        .join("")}
                </div>
            </div>
        `;

            trAvarias.appendChild(tdAvarias);
            itensTab.appendChild(trAvarias);
        }

        this.configurarRemoverItem();
    }

    configurarRemoverItem(): void {
        document.querySelectorAll(".remover-item").forEach((btn) => {
            btn.removeEventListener("click", this.removerItemEvento as any);
            btn.addEventListener("click", this.removerItemEvento as any);
        });
    }

    private removerItemEvento: (e: Event) => void = () => {};
    removerItemHTML(eventoRemover: (e: Event) => void) {
        this.removerItemEvento = eventoRemover;
    }

    TelaDeRegistro(
        cliente: Cliente,
        itens: Item[],
        total: number,
        desconto: number,
        entrega: string,
    ): void {
        this.exibirCliente(cliente);

        const itensTab = document.getElementById("itens-tabela");
        if (itensTab) {
            itensTab.innerHTML = "";

            itens.forEach((item) => {
                this.exibirItem(item);
            });
        }

        const entregaSpan = document.getElementById(
            "entrega-esperada",
        ) as HTMLSpanElement;

        const subtotalSpan = document.getElementById(
            "subtotal",
        ) as HTMLSpanElement;
        const descontoSpan = document.getElementById(
            "desconto",
        ) as HTMLSpanElement;
        const totalSpan = document.getElementById("total") as HTMLSpanElement;

        entregaSpan.innerText = entrega;
        subtotalSpan.innerText = `R$ ${total.toFixed(2)}`;
        descontoSpan.innerText = `R$ ${desconto.toFixed(2)}`;
        totalSpan.innerText = `R$ ${(total - desconto).toFixed(2)}`;
    }

    getClienteInput(): string {
        return (
            document.getElementById("cliente") as HTMLInputElement
        ).value.trim();
    }

    getItemInput(): string {
        return (
            document.getElementById("item") as HTMLInputElement
        ).value.trim();
    }

    getHoras(): number {
        const horasInput = document.getElementById("horas") as HTMLInputElement;
        const value = horasInput?.value?.trim();
        const parsed = parseInt(value);
        return isNaN(parsed) ? 0 : parsed;
    }

    exibirSugestoesClientes(clientes: Cliente[]): void {
        const datalist = document.getElementById(
            "lista-clientes",
        ) as HTMLDataListElement;
        if (datalist) {
            datalist.innerHTML = "";
            clientes.forEach((c: any) => {
                const opt = document.createElement("option");
                opt.value = c.id.toString();
                opt.label = `${c.nome} (${c.cpf})`;
                datalist.appendChild(opt);
            });
        }
    }

    exibirSugestoesItens(itens: Item[]): void {
        const datalist = document.getElementById(
            "lista-itens",
        ) as HTMLDataListElement;
        if (datalist) {
            datalist.innerHTML = "";
            itens.forEach((i: Item) => {
                const opt = document.createElement("option");
                opt.value = i.codigo;
                opt.label = `${i.modelo} - ${i.disponivel ? "Disp" : "Indisp"}`;
                datalist.appendChild(opt);
            });
        }
    }
}
