<?php

class GestorRelatorio
{
    public function __construct(
        private IRelatorioRepositorio $repositorio,
        private ITransacao $transacao
    ) {}

    /**
     * Buscar dados de locações devolvidas no período.
     * @param string $dataInicial Data inicial no formato 'Y-m-d'.
     * @param string $dataFinal Data final no formato 'Y-m-d'.
     * @return array Lista de locações devolvidas com detalhes.
     * @throws DominioException Se a data inicial for posterior à data final.
     */
    public function buscarLocacoesDevolvidas(string $dataInicial, string $dataFinal): array
    {
        $inicio = new DateTimeImmutable($dataInicial);
        $fim = new DateTimeImmutable($dataFinal);

        if ($inicio > $fim) {
            throw new DominioException("Data inicial deve ser anterior à data final");
        }
        
        $dadosLocacoes = $this->repositorio->buscarLocacoesDevolvidas($inicio, $fim);
        return $this->processarRelatorioLocacoes($dadosLocacoes);
    }

    /**
     * Buscar dados de top itens no período.
     * @param string $dataInicial Data inicial no formato 'Y-m-d'.
     * @param string $dataFinal Data final no formato 'Y-m-d'.
     * @return array Lista de itens mais alugados com detalhes.
     * @throws DominioException Se a data inicial for posterior à data final.
     */
    public function buscarTopItens(string $dataInicial, string $dataFinal): array
    {
        $inicio = new DateTimeImmutable($dataInicial);
        $fim = new DateTimeImmutable($dataFinal);
        
        if ($inicio > $fim) {
            throw new DominioException("Data inicial deve ser anterior à data final");
        }

        $dadosItens = $this->repositorio->buscarTopItens($inicio, $fim);
        return $this->processarRelatorioTopItens($dadosItens);
    }

    /**
     * Processar relatório de locações devolvidas.
     * @param array $dadosLocacoes Lista de locações devolvidas com detalhes.
     * @return array Dados processados para gráfico de colunas com datas e valores totais
     * 
     */
    private function processarRelatorioLocacoes(array $dadosLocacoes): array
    {
        if (empty($dadosLocacoes)) {
            throw new DominioException("Nenhum dado de locação disponível para processamento");
        }
        $dadosAgrupados = [];
        foreach ($dadosLocacoes as $locacao) {
            $dataLocacao = $locacao['data_locacao'];
            $valorPago = (float) $locacao['valor_pago'];
            
            if (!isset($dadosAgrupados[$dataLocacao])) {
                $dadosAgrupados[$dataLocacao] = 0.0;
            }
            $dadosAgrupados[$dataLocacao] += $valorPago;
        }
        $resultado = [];
        foreach ($dadosAgrupados as $data => $valor) {
            $resultado[] = [
                'data' => $data,
                'valor_pago' => $valor
            ];
        }
        usort($resultado, function($a, $b) {
            return strcmp($a['data'], $b['data']);
        });
        return $resultado;
    }

    /**
     * Processar relatório de top itens.
     * @param array $dadosItens Lista de itens mais alugados com detalhes.
     * @return array Dados processados para o formato compatível.
     * 
     */
    private function processarRelatorioTopItens(array $dadosItens): array
    {
        if (empty($dadosItens)) {
            throw new DominioException("Nenhum dado de item disponível para processamento");
        }
        $resultado = [];
        foreach ($dadosItens as $item) {
            $resultado[] = [
                'codigo' => $item['codigo'],
                'modelo' => $item['modelo'],
                'quantidade' => (int) $item['quantidade']
            ];
        }
        usort($resultado, function($a, $b) {
            return $b['quantidade'] <=> $a['quantidade'];
        });
        return $resultado;
    }
}

?>
