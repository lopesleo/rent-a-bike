<?php

class GestorCliente
{

    function __construct(private IClienteRepositorio $repositorio) {}

    public function buscarPorId(int $id): ?Cliente
    {
        return $this->repositorio->buscarPorId($id);
    }

    /**
     * Busca todos os clientes.
     *
     * @return array<Cliente> Lista de clientes.
     */
    public function buscarTodos(): array
    {
        return $this->repositorio->buscarTodos();
    }

    public function buscarPorCPF(string $cpf): ?Cliente
    {
        return $this->repositorio->buscarPorCPF($cpf);
    }

    public function salvar(Cliente $cliente): void {}

    public function remover(int $id): void
    {
        $this->repositorio->remover($id);
    }
}
