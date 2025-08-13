<?php

class TransacaoEmBDR implements ITransacao
{
    /**
     * Construtor da classe TransacaoEmBDR.
     *
     * @param PDO $pdo A instância do PDO para controle de transação.
     */
    function __construct(private PDO $pdo) {}


    /**
     * Inicia uma transação no banco de dados.
     *
     * @return void
     */
    public function iniciarTransacao()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * Salva (commit) a transação atual no banco de dados.
     *
     * @return void
     */
    public function salvarTransacao()
    {
        $this->pdo->commit();
    }

    /**
     * Desfaz (rollback) a transação atual no banco de dados.
     *
     * @return void
     */
    public function desfazerTransacao()
    {
        $this->pdo->rollBack();
    }
}
