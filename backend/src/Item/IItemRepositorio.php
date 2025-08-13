<?php


interface IItemRepositorio
{
    /**
     * Busca um item pelo ID.
     *
     * @param int $id ID do item.
     * @return Item|null Item encontrado ou null se não encontrado.
     */
    public function buscarPorId(int $id): ?Item;

    /**
     * Busca um item pelo código.
     *
     * @param string $codigo Código do item.
     * @return Item|null Item encontrado ou null se não encontrado.
     */
    public function buscarPorCodigo(string $codigo): ?Item;

    /**
     * Busca itens por uma lista de IDs.
     *
     * @param int[] $ids Array de IDs dos itens.
     * @return Item[] Array de itens encontrados.
     */
    public function buscarPorIds(array $ids): array;

    /**
     * Busca todos os itens disponíveis.
     *
     * @return Item[] Array de itens disponíveis.
     */
    public function buscarDisponiveis(): array;

    /**
     * Busca itens por tipo.
     *
     * @param string $tipo Tipo do item.
     * @return Item[] Array de itens encontrados.
     */
    public function buscarPorTipo(string $tipo): array;

    /**
     * Busca todos os itens.
     *
     * @return Item[] Array de todos os itens.
     */
    public function buscarTodos(): array;

    /**
     * Salva um item.
     *
     * @param Item $item Item a ser salvo.
     * @return bool True se o item foi salvo com sucesso, false caso contrário.
     */
    public function salvar(Item $item): bool;

    /**
     * Remove um item pelo ID.
     *
     * @param int $id ID do item a ser removido.
     * @return bool True se o item foi removido com sucesso, false caso contrário.
     */
    public function remover(int $id): bool;

    /**
     * Atualiza um item.
     *
     * @param Item $item Item a ser atualizado.
     * @return bool True se o item foi atualizado com sucesso, false caso contrário.
     */
    public function atualizar(Item $item): bool;


    /**
     * Define um item como disponível.
     *
     * @param int $item_id ID do item.
     * @return bool True se o status foi atualizado com sucesso, false caso contrário.
     */
    public function setDisponivel(int $item_id): bool;

    /**
     * Define um item como indisponível.
     *
     * @param int $item_id ID do item.
     * @return bool True se o status foi atualizado com sucesso, false caso contrário.
     */
    public function setIndisponivel(int $item_id): bool;
}
