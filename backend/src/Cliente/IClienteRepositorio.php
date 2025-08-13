<?php

interface IClienteRepositorio
{
    /**
     * Busca um cliente pelo ID.
     *
     * @param int $id ID do cliente.
     * @return Cliente|null Cliente encontrado ou null se não encontrado.
     */
    public function buscarPorId(int $id): ?Cliente;

    /**
     * Busca todos os clientes.
     *
     * @return Cliente[] Array de clientes.
     */
    public function buscarTodos(): array;
    /**
     * Busca um cliente pelo CPF.
     *
     * @param string $cpf CPF do cliente.
     * @return Cliente|null Cliente encontrado ou null se não encontrado.
     */
    public function buscarPorCPF(string $cpf): ?Cliente;

    /**
     * Salva um cliente.
     *
     * @param Cliente $cliente Cliente a ser salvo.
     * @return void
     */
    public function salvar(Cliente $cliente): void;

    /**
     * Atualiza um cliente.
     *
     * @param Cliente $cliente Cliente a ser atualizado.
     * @return void
     */
    public function atualizar(Cliente $cliente): void;

    /**
     * Remove um cliente pelo ID.
     *
     * @param int $id ID do cliente a ser removido.
     * @return void
     */
    public function remover(int $id): void;
}
