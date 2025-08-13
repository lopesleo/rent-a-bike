<?php
class RelatorioRepositorioEmBDR implements IRelatorioRepositorio
{
    public function __construct(private PDO $conexao)
    {
    }

    public function buscarLocacoesDevolvidas(DateTimeImmutable $dataInicial, DateTimeImmutable $dataFinal): array
    {
        $sql = "
            SELECT 
                l.id as id_locacao,
                DATE(l.data_hora_locacao) as data_locacao,
                d.data_hora as data_devolucao,
                d.valor_pago
            FROM locacao l
            INNER JOIN devolucao d ON l.id = d.locacao_id
            WHERE l.data_hora_locacao BETWEEN :data_inicial AND :data_final
            ORDER BY l.data_hora_locacao ASC
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':data_inicial', $dataInicial->format('Y-m-d 00:00:00'));
        $stmt->bindValue(':data_final', $dataFinal->format('Y-m-d 23:59:59'));
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buscarTopItens(DateTimeImmutable $dataInicial, DateTimeImmutable $dataFinal): array
    {
         $sql = "
            SELECT 
                i.id as id_item,
                i.modelo,
                i.codigo,
                i.descricao,
                COUNT(li.item_id) as quantidade
            FROM locacao l
            INNER JOIN locacao_item li ON l.id = li.locacao_id
            INNER JOIN item i ON li.item_id = i.id
            WHERE l.data_hora_locacao BETWEEN :data_inicial AND :data_final
            GROUP BY i.id, i.modelo, i.codigo, i.descricao
            ORDER BY quantidade DESC, i.modelo ASC
        ";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':data_inicial', $dataInicial->format('Y-m-d 00:00:00'));
        $stmt->bindValue(':data_final', $dataFinal->format('Y-m-d 23:59:59'));
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
