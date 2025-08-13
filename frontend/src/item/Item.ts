export interface AvariaInfo {
    id: number;
    item_id: number;
    descricao: string;
    foto: string;
}

export class Item {
    constructor(
        public id: number,
        public codigo: string,
        public modelo: string,
        public valor_hora: number,
        public disponivel: boolean,
        public avarias: AvariaInfo[] = [],
    ) {}
}
