<?php

interface IFuncionarioRepositorio
{
    /**
     * Busca um funcionário pelo ID.
     *
     * @param int $id ID do funcionário.
     * @return Funcionario|null O funcionário encontrado ou null se não existir.
     */
    public function buscarPorId(int $id): ?Funcionario;

    /**
     * Busca todos os funcionários.
     *
     * @return Funcionario[] Array de funcionários.
     */
    public function buscarTodos(): array;
    /**
     * Busca um funcionário pelo CPF.
     *
     * @return Funcionario|null O funcionário encontrado ou null se não existir.
     */
    public function buscaPorCPF(string $cpf): ?Funcionario;
    /**
     * Busca um funcionário pelo código.
     *
     * @param string $codigo Código do funcionário.
     * @return Funcionario|null O funcionário encontrado ou null se não existir.
     */
    public function buscarPorCodigo(string $codigo): ?Funcionario;

    /**
     * Busca um funcionário pelo ID do usuário.
     *
     * @param int $usuarioId ID do usuário associado ao funcionário.
     * @return Funcionario|null O funcionário encontrado ou null se não existir.
     */
    public function buscarPorUsuarioId(int $usuarioId): ?Funcionario;

    /**
     * Salva um funcionário.
     *
     * @param Funcionario $funcionario O funcionário a ser salvo.
     * @return void
     */
    public function salvar(Funcionario $funcionario): void;

    /**
     * Atualiza um funcionário.
     *
     * @param Funcionario $funcionario O funcionário a ser atualizado.
     * @return void
     */
    public function atualizar(Funcionario $funcionario): void;

    /**
     * Remove um funcionário pelo ID.
     *
     * @param int $id ID do funcionário a ser removido.
     * @return void
     */
    public function remover(int $id): void;
}
