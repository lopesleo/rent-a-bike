import { Cliente } from "../cliente/Cliente.js";
import { Funcionario } from "../funcionario/Funcionario.js";
import { Item } from "../item/Item.js";

export class Locacao {
    constructor(
        public id: number,
        public cliente: Cliente,
        public funcionario: Funcionario,
        public dataHoraLocacao: string,
        public horasContratadas: number,
        public dataHoraEntregaPrevista: string,
        public descontoAplicado: number,
        public valorTotalPrevisto: number,
        public status: string,
        public itens: Item[],
    ) {}
}
