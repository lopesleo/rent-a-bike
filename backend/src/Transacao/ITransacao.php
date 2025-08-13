<?php

interface ITransacao
{
    /**
     * Inicia uma transação.
     *
     * @throws RepositorioException Se ocorrer um erro ao iniciar a transação.
     * @return void
     */
    function iniciarTransacao();

    /**
     * Salva (commit) a transação atual.
     *
     * @throws RepositorioException Se ocorrer um erro ao salvar a transação.
     * @return void
     */
    function salvarTransacao();

    /**
     * Desfaz (rollback) a transação atual.
     *
     * @throws RepositorioException Se ocorrer um erro ao desfazer a transação.
     * @return void
     */
    function desfazerTransacao();
}
