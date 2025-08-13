import { Cliente } from "../../cliente/Cliente";
import { Item } from "../../item/Item";

export interface ILocacaoView {
    TelaDeRegistro(
        cliente: Cliente,
        itens: Item[],
        total: number,
        desconto: number,
        entrega: string,
    ): void;
    getClienteInput(): string;
    getItemInput(): string;
    getHoras(): number;
    exibirCliente(cliente: Cliente): void;
    exibirItem(item: Item): void;
    exibirSugestoesClientes(clientes: Cliente[]): void;
    exibirSugestoesItens(itens: Item[]): void;
    removerItemHTML(handler: (e: Event) => void): void;
}
