<?php

interface ILocacaoRepositorio
{

    /**
     * @throws RepositorioException
     */

    /**
     * @param Locacao $locacao
     * @return int ID da locação inserida
     */
    function salvar(Locacao $locacao): int;

    /**
     * @param Locacao $locacao
     * @return bool Retorna true se a atualização foi bem-sucedida, false caso contrário
     */
    function atualizar(Locacao $locacao): bool;
    /**
     * @param int $id ID da locação a ser deletada
     * @return bool Retorna true se a deleção foi bem-sucedida, false caso contrário
     */
    function deletarPorId($id): bool;
    /**
     * @param int $id ID da locação a ser buscada
     * @return LocacaoResumo Retorna a locação correspondente ao ID fornecido
     */
    function buscarPorId(int $id): LocacaoResumo;
    /**
     * @param int $id ID da locação a ser buscada
     * @return Locacao Retorna a locação correspondente ao ID fornecido
     */
    function buscarPorIdModel(int $id): ?Locacao;
    /**
     * @return LocacaoResumo[] Retorna todas as locações
     */
    function buscarTodos(): array;
    /**
     * @param int $cliente_id ID do cliente para buscar locações
     * @return LocacaoResumo[] Retorna todas as locações associadas ao cliente
     */
    function buscarPorClienteId(int $cliente_id): array;

    /**
     * @param int $id ID da locacao para atualizar o status
     * @param string $status muda o status para o desejado
     * @return bool Retornar sucesso ou falha (true e false)
     */
    public function atualizarStatus(int $id, string $status): bool;
}
