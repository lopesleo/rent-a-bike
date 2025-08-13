<?php
//busca n:n relacionamentos

interface ILocacaoItemRepositorio
{
    /**
     * Salva um item de locação.
     *
     * @param LocacaoItem $locacaoItem O item de locação a ser salvo.
     * @return bool True se o item de locação foi salvo com sucesso, false caso contrário.
     */
    public function salvar(LocacaoItem $locacaoItem): bool;

    /**
     * Marca um item de locação para limpeza.
     *
     * @param int $locacaoId O ID da locação.
     * @param int $itemId O ID do item.
     * @param float $taxa A taxa de limpeza a ser aplicada.
     * @return bool True se o item foi marcado para limpeza com sucesso, false caso contrário.
     */
    public function marcarLimpeza(int $locacaoId, int $itemId, float $taxa): bool;

    /**
     * Busca itens de locação por ID da locação.
     *
     * @param int $id O ID da locação.
     * @return LocacaoItemResumo[]|null Um array de resumos de itens de locação ou null se não encontrado.
     */
    public function buscarPorLocacaoId(int $id): ?array;

    /**
     * Remove um item de locação.
     *
     * @param int $locacaoId O ID da locação.
     * @param int $itemId O ID do item.
     * @return bool True se o item de locação foi removido com sucesso, false caso contrário.
     */
    public function remover(int $locacaoId, int $itemId): bool;
}
