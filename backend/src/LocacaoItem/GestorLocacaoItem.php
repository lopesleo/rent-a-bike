<?php

class GestorLocacaoItem
{
    /**
     * Construtor do GestorLocacaoItem.
     *
     * @param ILocacaoItemRepositorio $iLocacaoItemRepositorio Repositório de itens de locação.
     */
    public function __construct(
        private ILocacaoItemRepositorio $iLocacaoItemRepositorio,
    ) {}

    /**
     * Obtém todos os itens associados a um ID de locação.
     *
     * @param int $id ID da locação.
     * @return LocacaoItemResumo[] Array de resumos de itens de locação.
     * @throws RepositorioException Se nenhum item for encontrado para a locação.
     */
    public function obterItensPorLocacaoId(int $id): array
    {
        $locacaoItem = $this->iLocacaoItemRepositorio->buscarPorLocacaoId($id);
        if (empty($locacaoItem)) {
            throw new RepositorioException("Nenhum item encontrado para a locação com ID: {$id}");
        }

        return $locacaoItem;
    }
}
