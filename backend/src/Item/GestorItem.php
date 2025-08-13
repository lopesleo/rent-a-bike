<?php

class GestorItem
{

    /**
     * Construtor do GestorItem.
     *
     * @param IItemRepositorio $itemRepositorio O repositório de itens.
     */
    public function __construct(
        private IItemRepositorio $itemRepositorio,
    ) {}

    /**
     * Busca um item pelo ID.
     *
     * @param int $id ID do item.
     * @return Item|null O item encontrado ou null se não existir.
     */
    public function buscarPorId(int $id): ?Item
    {
        return $this->itemRepositorio->buscarPorId($id);
    }

    /**
     * Busca um item pelo código.
     *
     * @param string $codigo Código do item.
     * @return Item|null O item encontrado ou null se não existir.
     */
    public function buscarPorCodigo(string $codigo): ?Item
    {
        return $this->itemRepositorio->buscarPorCodigo($codigo);
    }

    /**
     * Busca itens por uma lista de IDs.
     *
     * @param int[] $ids Array de IDs dos itens.
     * @return Item[] Array de itens encontrados.
     */
    public function buscarPorIds(array $ids): array
    {
        return $this->itemRepositorio->buscarPorIds($ids);
    }

    /**
     * Busca todos os itens disponíveis.
     *
     * @return Item[] Array de itens disponíveis.
     */
    public function buscarDisponiveis(): array
    {
        return $this->itemRepositorio->buscarDisponiveis();
    }

    /**
     * Busca itens por tipo.
     *
     * @param string $tipo Tipo do item.
     * @return Item[] Array de itens encontrados.
     */
    public function buscarPorTipo(string $tipo): array
    {
        return $this->itemRepositorio->buscarPorTipo($tipo);
    }

    /**
     * Busca todos os itens.
     *
     * @return Item[] Array de todos os itens.
     */
    public function buscarTodos(): array
    {
        return $this->itemRepositorio->buscarTodos();
    }

    /**
     * Define um item como indisponível.
     *
     * @param int $item_id ID do item.
     * @return bool True se o status foi atualizado com sucesso, false caso contrário.
     */
    public function setIndisponivel(int $item_id): bool
    {
        return $this->itemRepositorio->setIndisponivel($item_id);
    }

    /**
     * Define um item como disponível.
     *
     * @param int $item_id ID do item.
     * @return bool True se o status foi atualizado com sucesso, false caso contrário.
     */
    public function setDisponivel(int $item_id): bool
    {
        return $this->itemRepositorio->setDisponivel($item_id);
    }
}
