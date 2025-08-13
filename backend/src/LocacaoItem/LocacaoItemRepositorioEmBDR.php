<?php

class LocacaoItemRepositorioEmBDR implements ILocacaoItemRepositorio
{
    /**
     * Construtor do repositório de itens de locação.
     *
     * @param PDO $pdo Instância do PDO para acesso ao banco de dados.
     */
    public function __construct(
        private PDO $pdo,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function salvar(LocacaoItem $locacaoItem): bool
    {
        $sql = "INSERT INTO locacao_item (locacao_id, item_id, valor_hora)
                VALUES (:locacao_id, :item_id, :valor_hora)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(
            [
                'locacao_id' => $locacaoItem->getLocacao()->getId(),
                'item_id' => $locacaoItem->getItem()->getId(),
                'valor_hora' => $locacaoItem->getValorHora(),
            ]
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * {@inheritdoc}
     * @return LocacaoItemResumo[]
     */
    public function buscarPorLocacaoId(int $id): array
    {
        $sql = "SELECT * FROM locacao_item WHERE locacao_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $rows =  $stmt->fetchAll();
        return array_map(function ($row) {
            return LocacaoItemResumo::fromArray($row);
        }, $rows);
    }

    public function marcarLimpeza(int $locacaoId, int $itemId, float $valor): bool
    {
        $sql = "
        UPDATE locacao_item
           SET limpeza_aplicada    = 1,
               valor_taxa_limpeza = :valor
         WHERE locacao_id = :locacaoId
           AND item_id    = :itemId
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':valor', $valor);
        $stmt->bindValue(':locacaoId', $locacaoId, PDO::PARAM_INT);
        $stmt->bindValue(':itemId', $itemId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function remover(int $locacaoId, int $itemId): bool
    {
        $sql = "DELETE FROM locacao_item WHERE locacao_id = :locacaoId AND item_id = :itemId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['locacaoId' => $locacaoId, 'itemId' => $itemId]);
    }
}
