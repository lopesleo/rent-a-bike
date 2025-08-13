<?php

class GestorDevolucao
{
    /**
     * @param IDevolucaoRepositorio $devolucaoRepositorio
     * @param IItemRepositorio $itemRepositorio
     * @param IlocacaoRepositorio $locacaoRepositorio
     * @param ILocacaoItemRepositorio $locacaoItemRepositorio
     * @param GestorLocacaoItem $gestorLocacaoItem
     * @param GestorItem $gestorItem
     * @param ITransacao $transacao
     * @param IAvariaRepositorio $avariaRepositorio
     */
    public function __construct(
        private IDevolucaoRepositorio $devolucaoRepositorio,
        private IItemRepositorio $itemRepositorio,
        private ILocacaoRepositorio $locacaoRepositorio,
        private ILocacaoItemRepositorio $locacaoItemRepositorio,
        private GestorLocacaoItem $gestorLocacaoItem,
        private GestorItem $gestorItem,
        private GestorCliente $gestorCliente,
        private GestorFuncionario $gestorFuncionario,
        private ITransacao $transacao,
        private AvariaRepositorioEmBDR $avariaRepositorio,
    ) {}

    /**
     * Cria uma Devolução com os dados fornecidos.
     *
     * @param array $dados Dados da devolução, incluindo itens.
     * @throws DominioException Se houver erros de validação.
     * @throws Exception Se ocorrer um erro inesperado.
     */
    public function criarDevolucao(array $dados): array
    {
        [$locacao, $funcionario] = $this->validarDadosParaDevolucao($dados);
        $itensData = $dados['itens'] ?? [];

        $locacaoItemIds = array_map(
            fn(LocacaoItem $li) => $li->getItem()->getId(),
            $locacao->getItens()
        );
        $invalidIds = [];
        foreach ($itensData as $item) {
            if (!in_array($item['item_id'], $locacaoItemIds, true)) {
                $invalidIds[] = $item['item_id'];
            }
        }
        if (!empty($invalidIds)) {
            $idsList = implode(', ', array_unique($invalidIds));
            throw DominioException::com([
                "Os itens [{$idsList}] não estão vinculados à locação #{$locacao->getId()}."
            ]);
        }

        $devolucao = new Devolucao(
            locacao: $locacao,
            funcionario: $funcionario
        );
        $devolucao->calcularValores();

        $resumos = $this->locacaoItemRepositorio
            ->buscarPorLocacaoId($locacao->getId());

        $totalAvarias     = 0.0;
        $totalTaxaLimpeza = 0.0;

        foreach ($itensData as $itemData) {
            foreach ($itemData['avarias'] ?? [] as $av) {
                $totalAvarias += (float) $av['valor'];
            }

            if (!empty($itemData['limpeza_aplicada'])) {
                $match = array_filter(
                    $resumos,
                    fn(LocacaoItemResumo $li) => $li->getItemId() === $itemData['item_id']
                );
                $liResumo = reset($match);
                if (!$liResumo) {
                    throw DominioException::com([
                        "Item #{$itemData['item_id']} não encontrado na locação."
                    ]);
                }
                $horas    = $devolucao->getHorasUsadas();
                $subtotal = $liResumo->getValorHora() * $horas;
                $totalTaxaLimpeza += round($subtotal * 0.10, 2);
            }
        }

        $devolucao->setValorPago(
            $devolucao->getValorPago()
                + $totalAvarias
                + $totalTaxaLimpeza
        );

        $this->transacao->iniciarTransacao();
        try {
            $idDev = $this->devolucaoRepositorio->salvar($devolucao);
            $devolucao->setId($idDev);

            foreach ($locacao->getItens() as $li) {
                $this->gestorItem->setDisponivel($li->getItem()->getId());
            }
            $this->locacaoRepositorio->atualizarStatus($locacao->getId(), 'FINALIZADA');

            foreach ($itensData as $itemData) {
                foreach ($itemData['avarias'] ?? [] as $av) {
                    $imageData = base64_decode($av['foto']);
                    if (getimagesizefromstring($imageData) === false) {
                        throw DominioException::com(['Imagem inválida.']);
                    }
                    $itemId = $itemData['item_id'];
                    $avaria = new Avaria(
                        new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo')),
                        $funcionario,
                        $av['descricao'],
                        $av['valor'],
                        '',
                        $this->itemRepositorio->buscarPorId($itemId),
                        $devolucao,
                    );
                    $avariaErros = $avaria->validar();
                    if (count($avariaErros) > 0) {
                        throw DominioException::com($avariaErros);
                    }
                    $persistida = $this->avariaRepositorio->criar($avaria);
                    $avariaId   = $persistida->getId();


                    $prefixo = "dev-{$devolucao->getId()}-item-{$itemId}";

                    $sufixoAleatorio = substr(bin2hex(random_bytes(8)), 0, 8);

                    $filename = "{$prefixo}-{$sufixoAleatorio}.jpg";

                    $uploadDir = __DIR__ . '/../../public/fotos/avarias/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    if (!is_writable($uploadDir)) {
                        throw DominioException::com(["Não é possível escrever em $uploadDir"]);
                    }
                    $fullPath = $uploadDir . $filename;
                    if (file_put_contents($fullPath, $imageData) === false) {
                        throw DominioException::com(["Falha ao salvar foto em $fullPath"]);
                    }
                    $relativePath = "fotos/avarias/{$filename}";
                    $this->avariaRepositorio->atualizarFoto($avariaId, $relativePath);
                }
            }

            foreach ($itensData as $itemData) {
                if (!empty($itemData['limpeza_aplicada'])) {
                    $match = array_filter(
                        $resumos,
                        fn(LocacaoItemResumo $li) => $li->getItemId() === $itemData['item_id']
                    );
                    $liResumo = reset($match);
                    $taxa = round(
                        $liResumo->getValorHora()
                            * $devolucao->getHorasUsadas()
                            * 0.10,
                        2
                    );
                    $this->locacaoItemRepositorio->marcarLimpeza(
                        $locacao->getId(),
                        $itemData['item_id'],
                        $taxa
                    );
                }
            }

            $this->transacao->salvarTransacao();
            return ['Mensagem' => 'Devolução criada com sucesso.'];
        } catch (DominioException $e) {
            $this->transacao->desfazerTransacao();
            throw $e;
        }
    }


    /**
     * Valida os dados da devolução e retorna as entidades necessárias.
     * @param array $dados
     * @return array{0: Locacao, 1: Funcionario}
     * @throws DominioException
     */
    private function validarDadosParaDevolucao(array $dados): array
    {
        $erros = [];
        $locacao = $this->locacaoRepositorio->buscarPorIdModel($dados['locacao_id']);
        $funcionario = $this->gestorFuncionario->buscarPorId($dados['funcionario_id']);

        if (!$locacao) {
            $erros[] = "Locação #{$dados['locacao_id']} não encontrada";
        }
        if ($locacao && $locacao->getStatus() == 'FINALIZADA') {
            $erros[] = "Locação #{$dados['locacao_id']} já finalizada";
        }
        if (!$funcionario) {
            $erros[] = "Funcionário #{$dados['funcionario_id']} não encontrado";
        }
        if (count($erros) > 0) {
            throw DominioException::com($erros);
        }
        return [$locacao, $funcionario];
    }
    /**
     * Lista todas as devoluções.
     * @return DevolucaoResumo[]
     */
    public function listarTodos(): array
    {
        return $this->devolucaoRepositorio->buscarTodos();
    }

    /**
     * Busca uma devolução por id.
     */
    public function buscarPorId(int $id): ?DevolucaoResumo
    {
        return $this->devolucaoRepositorio->buscarPorId($id);
    }
}
