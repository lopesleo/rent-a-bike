export class Cliente {
    constructor(
        public id: number,
        public nome: string,
        public cpf: string,
        public telefone: string,
        public email: string,
        public endereco: string,
        public dataNascimento: string,
        public foto_path: string,
        public doc_path: string,
    ) {}
}
