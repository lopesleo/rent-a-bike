<?php

class ClienteRepositorioEmBDR implements IClienteRepositorio
{
    function __construct(private PDO $pdo) {}

    public function buscarPorId(int $id): ?Cliente
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cliente WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch();
        if (!$data) {
            return null;
        }

        $cliente = Cliente::fromArray($data);

        return $cliente;
    }

    public function buscarPorCPF(string $cpf): ?Cliente
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cliente WHERE cpf = :cpf");
        $stmt->bindValue(':cpf', $cpf);
        $stmt->execute();
        $data = $stmt->fetch();
        return $data ? Cliente::fromArray($data) : null;
    }
    /**
     * Busca todos os clientes.
     *
     * @return array<Cliente> Lista de clientes.
     */
    public function buscarTodos(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM cliente");
        $clientes = $stmt->fetchAll();
        return array_map(fn($data) => Cliente::fromArray($data), $clientes);
    }

    public function salvar(Cliente $cliente): void {}

    public function atualizar(Cliente $cliente): void {}

    public function remover(int $id): void {}
}
