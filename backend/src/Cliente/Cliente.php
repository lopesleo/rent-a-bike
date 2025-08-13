<?php

/**
 * Representa um cliente.
 */
class Cliente
{
    /**
     * Construtor da classe Cliente.
     *
     * @param int $id O ID do cliente.
     * @param string|null $codigo O código do cliente.
     * @param string $nome O nome do cliente.
     * @param string|null $foto A URL da foto do cliente.
     * @param DateTimeImmutable|null $dataNascimento A data de nascimento do cliente.
     * @param string|null $cpf O CPF do cliente.
     * @param string|null $telefone O número de telefone do cliente.
     * @param string|null $email O endereço de e-mail do cliente.
     * @param string|null $endereco O endereço do cliente.
     */
    function __construct(
        private int $id,
        private ?string $codigo,
        private string $nome,
        private ?string $foto,
        private ?DateTimeImmutable $dataNascimento,
        private ?string $cpf,
        private ?string $telefone,
        private ?string $email,
        private ?string $endereco,
    ) {}
    /**
     * Converte o objeto cliente para um array.
     *
     * @return array<string, mixed> Os dados do cliente como um array.
     */
    function toArray(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nome' => $this->nome,
            'foto' => $this->foto,
            'data_nascimento' => $this->dataNascimento->format('Y-m-d'),
            'cpf' => $this->cpf,
            'telefone' => $this->telefone,
            'email' => $this->email,
            'endereco' => $this->endereco,
        ];
    }
    /**
     * Cria um objeto Cliente a partir de um array.
     *
     * @param array<string, mixed> $data Os dados para criar o cliente.
     * @return Cliente O objeto Cliente criado.
     */
    public static function fromArray(array $data): Cliente
    {
        return new self(
            (int) $data['id'],
            $data['codigo'] ?? '',
            $data['nome'] ?? '',
            $data['foto'] ?? null,
            new DateTimeImmutable($data['data_nascimento']),
            $data['cpf'] ?? '',
            $data['telefone'] ?? null,
            $data['email'] ?? null,
            $data['endereco'] ?? null
        );
    }

    //getters and setters

    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function getCodigo(): string
    {
        return $this->codigo;
    }
    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }
    public function getNome(): string
    {
        return $this->nome;
    }
    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }
    public function getFoto(): ?string
    {
        return $this->foto;
    }
    public function setFoto(?string $foto): void
    {
        $this->foto = $foto;
    }
    public function getDataNascimento(): DateTimeImmutable
    {
        return $this->dataNascimento;
    }
    public function setDataNascimento(DateTimeImmutable $dataNascimento): void
    {
        $this->dataNascimento = $dataNascimento;
    }
    public function getCpf(): string
    {
        return $this->cpf;
    }
    public function setCpf(string $cpf): void
    {
        $this->cpf = $cpf;
    }
    public function getTelefone(): ?string
    {
        return $this->telefone;
    }
    public function setTelefone(?string $telefone): void
    {
        $this->telefone = $telefone;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
    public function getEndereco(): ?string
    {
        return $this->endereco;
    }
    public function setEndereco(?string $endereco): void
    {
        $this->endereco = $endereco;
    }
    public function __toString(): string
    {
        return $this->nome;
    }
}
