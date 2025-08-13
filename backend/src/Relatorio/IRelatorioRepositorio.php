<?php   


interface IRelatorioRepositorio
{
    /**
     * Buscar locações devolvidas por período
     * @param DateTimeImmutable $dataInicial Data inicial do período
     * @param DateTimeImmutable $dataFinal Data final do período
     * @return array Lista de locações devolvidas com detalhes
     */
    public function buscarLocacoesDevolvidas(DateTimeImmutable $dataInicial, DateTimeImmutable $dataFinal): array;

    /**
     * Buscar top itens mais alugados por período
     * @param DateTimeImmutable $dataInicial Data inicial do período
     * @param DateTimeImmutable $dataFinal Data final do período
     * @return array Lista de itens mais alugados com detalhes
     */
    public function buscarTopItens(DateTimeImmutable $dataInicial, DateTimeImmutable $dataFinal): array;
}

?>