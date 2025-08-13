export class Funcionario {
    constructor(
        public id: number,
        public codigo: string,
        public nome: string,
        public telefone: string,
        public email: string,
        public cpf: string,
        public cargo: string,
    ) {
        this.id = id;
        this.codigo = codigo;
        this.nome = nome;
        this.telefone = telefone;
        this.email = email;
        this.cpf = cpf;
        this.cargo = cargo;
    }
    deSessionStorage(JSON: any): Funcionario {
        const data = JSON.parse(JSON.stringify(JSON));
        return new Funcionario(
            data.id,
            data.codigo,
            data.nome,
            data.telefone,
            data.email,
            data.cpf,
            data.cargo,
        );
    }
}
