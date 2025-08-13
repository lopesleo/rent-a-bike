<?php

class ItemRepositorioEmBDR implements IItemRepositorio
{
    public function __construct(private PDO $pdo) {}

    public function buscarPorId(int $id): ?Item
    {
        try {
            $sql  = "SELECT * FROM item WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $row  = $stmt->fetch();

            if (!$row) {
                return null;
            }
            return Item::fromArray($row);
        } catch (PDOException $e) {
            throw RepositorioException::com([$e->getMessage()]);
        }
    }

    public function buscarPorIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM item WHERE id IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ids);
        $rows = $stmt->fetchAll();
        return array_map(fn($r) => Item::fromArray($r), $rows);
    }

    public function buscarPorCodigo(string $codigo): ?Item
    {
        $sql = "SELECT * FROM item WHERE codigo = :codigo";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        return Item::fromArray($row);
    }
    public function buscarDisponiveis(): array
    {
        $sql = "SELECT * FROM item WHERE disponivel = 1";
        $stmt = $this->pdo->query($sql);
        $itens = $stmt->fetchAll();
        return array_map(fn($row) => Item::fromArray($row), $itens);
    }
    public function buscarPorTipo(string $tipo): array
    {
        $sql = "SELECT * FROM item WHERE tipo = :tipo";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tipo' => $tipo]);
        $itens = $stmt->fetchAll();
        return array_map(fn($row) => Item::fromArray($row), $itens);
    }
    public function buscarTodos(): array
    {
        $sql = "SELECT * FROM item";
        $stmt = $this->pdo->query($sql);
        $itens = $stmt->fetchAll();
        return array_map(fn($row) => Item::fromArray($row), $itens);
    }
    public function salvar(Item $item): bool
    {
        $sql = "INSERT INTO item (codigo, modelo, fabricante, descricao, valor_hora, avarias, numero_seguro, disponivel, tipo)
                VALUES (:codigo, :modelo, :fabricante, :descricao, :valor_hora, :avarias, :numero_seguro, :disponivel, :tipo)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $item->getCodigo(),
            'modelo' => $item->getModelo(),
            'fabricante' => $item->getFabricante(),
            'descricao' => $item->getDescricao(),
            'valor_hora' => $item->getValorHora(),
            'avarias' => $item->getAvarias(),
            'numero_seguro' => $item->getNumeroSeguro(),
            'disponivel' => $item->isDisponivel(),
            'tipo' => $item->getTipo(),
        ]);
        if ($stmt->rowCount() > 0) {
            $item->setId((int)$this->pdo->lastInsertId());
            return true;
        }
        return false;
    }
    public function remover(int $id): bool
    {
        $sql = "DELETE FROM item WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    public function atualizar(Item $item): bool
    {
        $sql = "UPDATE item SET codigo = :codigo, modelo = :modelo, fabricante = :fabricante, descricao = :descricao, valor_hora = :valor_hora, avarias = :avarias, numero_seguro = :numero_seguro, disponivel = :disponivel, tipo = :tipo WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'codigo' => $item->getCodigo(),
            'modelo' => $item->getModelo(),
            'fabricante' => $item->getFabricante(),
            'descricao' => $item->getDescricao(),
            'valor_hora' => $item->getValorHora(),
            'avarias' => $item->getAvarias(),
            'numero_seguro' => $item->getNumeroSeguro(),
            'disponivel' => $item->isDisponivel(),
            'tipo' => $item->getTipo(),
            'id' => $item->getId(),
        ]);
        return $stmt->rowCount() > 0;
    }

    function setIndisponivel(int $item_id): bool
    {
        try {
            $sql = "UPDATE item SET disponivel = :disponivel
            WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                "disponivel" => (int) false,
                "id" => $item_id
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RepositorioException($e->getMessage());
        }
    }
    function setDisponivel(int $item_id): bool
    {
        try {
            $sql = "UPDATE item SET disponivel = :disponivel
            WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                "disponivel" => true,
                "id" => $item_id
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RepositorioException($e->getMessage());
        }
    }
}
