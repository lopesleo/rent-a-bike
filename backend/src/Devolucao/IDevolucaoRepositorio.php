<?php

interface IDevolucaoRepositorio
{

    /**
     * @throws RepositorioException
     */

    /**
     * @param Devolucao $devolucao
     * @return int ID da devolução inserida
     */
    function salvar(Devolucao $devolucao): int;

    /**
     * @param Devolucao $devolucao
     * @return bool Retorna true se a atualização foi bem-sucedida, false caso contrário
     */
    function atualizar(Devolucao $devolucao): bool;
    /**
     * @param int $id ID da locação a ser deletada
     * @return bool Retorna true se a deleção foi bem-sucedida, false caso contrário
     */
    function deletarPorId($id): bool;
    /**
     * @param int $id ID da devolução a ser buscada
     * @return DevolucaoResumo Retorna a devolução correspondente ao ID fornecido
     */
    function buscarPorId(int $id): DevolucaoResumo;
    /**
     * @return DevolucaoResumo[] Retorna todas as devoluções
     */
    function buscarTodos(): array;

}
