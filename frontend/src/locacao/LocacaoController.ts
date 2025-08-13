import { Cliente } from "../cliente/Cliente.js";
import { Funcionario } from "../funcionario/Funcionario.js";
import { Item } from "../item/Item.js";
import { ErroCliente } from "../infra/ErroCliente.js";
import { ErroFuncionario } from "../infra/ErroFuncionario.js";
import { ErroItem } from "../infra/ErroItem.js";
import { ErroLocacao } from "../infra/ErroLocacao.js";
import { LocacaoManager } from "./LocacaoManager.js";
import { ILocacaoView } from "./interface/IlocacaoView.js";

export class LocacaoController {
    private cliente: Cliente | null = null;
    private itens: Item[] = [];
    private funcionario: Funcionario | null = null;
    private clientesCache: Cliente[] = [];
    private itensCache: Item[] = [];

    constructor(
        private view: ILocacaoView,
        private manager: LocacaoManager,
    ) {}

    async getClientes(): Promise<Cliente[]> {
        try {
            this.clientesCache = await this.manager.getClientes();
            return this.clientesCache;
        } catch {
            throw new ErroCliente("Erro ao buscar clientes");
        }
    }

    async getItens(): Promise<Item[]> {
        try {
            this.itensCache = await this.manager.getItens();
            return this.itensCache;
        } catch {
            throw new ErroItem("Erro ao buscar itens");
        }
    }

    sugerirClientes(entrada: string): Cliente[] {
        if (!entrada) return [];
        if (
            entrada.match(/^\d{3}\.\d{3}\.\d{3}-\d{2}$/) &&
            entrada.length == 11
        ) {
            return this.clientesCache.filter(
                (c) =>
                    c.cpf &&
                    c.cpf.replace(/\D/g, "") === entrada.replace(/\D/g, ""),
            );
        } else {
            return this.clientesCache.filter(
                (c) => Number(c.id) === Number(entrada),
            );
        }
    }

    confirmarCliente(entrada: string): Cliente {
        if (!entrada) throw new ErroCliente("Entrada de cliente vazia");
        let clienteSelecionado: any = null;
        if (
            entrada.match(/^\d{3}\.\d{3}\.\d{3}-\d{2}$/) ||
            entrada.length >= 11
        ) {
            clienteSelecionado = this.clientesCache.find(
                (c: any) =>
                    c.cpf &&
                    c.cpf.replace(/\D/g, "") === entrada.replace(/\D/g, ""),
            );
        } else {
            clienteSelecionado = this.clientesCache.find(
                (c: any) => Number(c.id) === Number(entrada),
            );
        }
        if (!clienteSelecionado)
            throw new ErroCliente("Cliente não encontrado");
        this.cliente = new Cliente(
            Number(clienteSelecionado.id),
            clienteSelecionado.nome,
            clienteSelecionado.cpf,
            clienteSelecionado.telefone,
            clienteSelecionado.email || "",
            clienteSelecionado.endereco || "",
            clienteSelecionado.dataNascimento ||
                clienteSelecionado.data_nascimento ||
                "",
            clienteSelecionado.foto || "",
            clienteSelecionado.doc_path || "",
        );
        this.view.exibirCliente(this.cliente);
        return this.cliente;
    }

    sugerirItens(codigo: string): Item[] {
        if (!codigo) return [];
        return this.itensCache.filter(
            (i) =>
                i.codigo &&
                i.codigo.toLowerCase().includes(codigo.toLowerCase()),
        );
    }

    confirmarItem(codigo: string): Item {
        if (!codigo) throw new ErroItem("Codigo do item vazio");

        const itemSelecionado = this.itensCache.find(
            (i: any) =>
                i.codigo && i.codigo.toLowerCase() === codigo.toLowerCase(),
        );

        if (!itemSelecionado) throw new ErroItem("Item nao encontrado");

        if (!itemSelecionado.disponivel)
            throw new ErroItem("Item nao disponivel");

        const baseUrl = import.meta.env.VITE_URL.replace("/api", "");

        const avariasComUrl = (itemSelecionado.avarias || []).map(
            (avaria: any) => {
                return {
                    ...avaria,
                    foto: `${baseUrl}/${avaria.foto}`,
                };
            },
        );

        const itemObj = new Item(
            Number(itemSelecionado.id),
            itemSelecionado.codigo,
            itemSelecionado.modelo,
            Number(itemSelecionado.valor_hora),
            Boolean(itemSelecionado.disponivel),
            avariasComUrl,
        );

        if (!this.itens.find((i) => i.id === itemObj.id)) {
            this.itens.push(itemObj);
        }
        return itemObj;
    }

    removerItem(id: number): Item[] {
        this.itens = this.itens.filter((item) => item.id !== id);
        return this.itens;
    }

    getItensSelecionados(): Item[] {
        return this.itens;
    }

    setFuncionario(
        id: number,
        nome: string,
        codigo?: string,
        telefone?: string,
        email?: string,
        cpf?: string,
        cargo?: string,
    ): void {
        this.funcionario = new Funcionario(
            id,
            nome,
            codigo || "",
            telefone || "",
            email || "",
            cpf || "",
            cargo || "",
        );
    }

    calcularSubtotal(horas: number): number {
        return this.itens.reduce((acc, i) => acc + i.valor_hora * horas, 0);
    }

    calcularDesconto(horas: number): number {
        const subtotal = this.calcularSubtotal(horas);
        return horas > 2 ? subtotal * 0.1 : 0;
    }

    calcularEntrega(horas: number): string {
        const entregaDate = new Date();
        entregaDate.setHours(entregaDate.getHours() + horas);
        return entregaDate.toLocaleString("pt-BR");
    }

    async registrar(horas: number): Promise<void> {
        if (!this.cliente || !this.cliente.id) 
            throw new ErroCliente("Cliente não selecionado");
        if (!this.funcionario || !this.funcionario.id) 
            throw new ErroFuncionario("Funcionário não selecionado");
        if (!horas || horas <= 0) 
            throw new ErroLocacao("Horas contratadas inválidas");
        if (!Array.isArray(this.itens) || !this.itens.length) 
            throw new ErroItem("Nenhum item selecionado");

        await this.manager.registrarLocacao({
            cliente_id: this.cliente.id,
            funcionario_id: this.funcionario.id,
            horas_contratadas: horas,
            itens: this.itens.map((i) => ({ id: i.id })),
        });

        this.cliente = null;
        this.itens = [];
        this.funcionario = null;
    }
}
