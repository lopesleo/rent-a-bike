<?php

class GestorLocacao
{
    /**
     * @param ILocacaoRepositorio $locacaoRepositorio
     * @param IItemRepositorio $itemRepositorio
     * @param ILocacaoItemRepositorio $locacaoItemRepositorio
     * @param GestorCliente $gestorCliente
     * @param GestorFuncionario $gestorFuncionario
     * @param ITransacao $transacao
     */
    public function __construct(
        private ILocacaoRepositorio $locacaoRepositorio,
        private IItemRepositorio $itemRepositorio,
        private ILocacaoItemRepositorio $locacaoItemRepositorio,
        private GestorCliente $gestorCliente,
        private GestorFuncionario $gestorFuncionario,
        private ITransacao $transacao
    ) {}

    /**
     * Cria uma nova locação.
     *
     * @param array<string, mixed> $dados Dados da locação.
     * @return array<string, string> Mensagem de sucesso.
     * @throws DominioException Se houver erros de validação.
     */
    public function criarLocacao(array $dados): array
    {

        $erros = [];

        foreach (['cliente_id', 'funcionario_id', 'horas_contratadas'] as $campo) {
            if (!isset($dados[$campo]) || !is_int($dados[$campo])) {
                $erros[] = "Campo “{$campo}” é obrigatório e deve ser inteiro.";
            }
        }

        if (!isset($dados['itens']) || !is_array($dados['itens'])) {
            $erros[] = 'Campo “itens” é obrigatório e deve ser um array.';
        } else {
            foreach ($dados['itens'] as $i => $item) {
                if (!isset($item['id']) || !is_int($item['id'])) {
                    $erros[] = "itens[{$i}].id é obrigatório e deve ser inteiro.";
                }
            }
        }

        if (count($erros) > 0) {
            throw DominioException::com($erros);
        }


        $horas      = $dados['horas_contratadas'];
        $cliente  = $this->gestorCliente->buscarPorId($dados['cliente_id']);
        $funcionario = $this->gestorFuncionario->buscarPorId($dados['funcionario_id']);

        if (!$cliente) {
            $erros[] = "Cliente #{$dados['cliente_id']} não encontrado";
        }
        if (!$funcionario) {
            $erros[] = "Funcionário #{$dados['funcionario_id']} não encontrado";
        }
        $ids = array_column($dados['itens'], 'id');
        $todosItens = $this->itemRepositorio->buscarPorIds($ids);
        $mapItens = [];
        foreach ($todosItens as $itemObj) {
            $mapItens[$itemObj->getId()] = $itemObj;
        }

        $itensValidos = [];
        $erros = [];

        foreach ($dados['itens'] as $i) {
            $item = $mapItens[$i['id']] ?? null;
            if (!$item) {
                $erros[] = "Item #{$i['id']} não encontrado";
                continue;
            }
            if (!$item->isDisponivel()) {
                $erros[] = "Item {$item->getCodigo()} indisponível";
                continue;
            }

            $itensValidos[] = new LocacaoItem(
                locacao: null,
                item: $item,
                valorHora: $item->getValorHora(),
            );
        }

        if (count($erros) > 0) {
            throw DominioException::com($erros);
        }


        $locacao = new Locacao(
            cliente: $cliente,
            funcionario: $funcionario,
            horasContratadas: $horas,
            itens: $itensValidos
        );

        $this->transacao->iniciarTransacao();
        try {
            $locId = $this->locacaoRepositorio->salvar($locacao);
            $locacao->setId($locId);

            foreach ($locacao->getItens() as $li) {
                $li->setLocacao($locacao);
                $this->locacaoItemRepositorio->salvar($li);
                $this->itemRepositorio->setIndisponivel($li->getItem()->getId());
            }

            $this->transacao->salvarTransacao();
        } catch (Exception $e) {
            $this->transacao->desfazerTransacao();
            throw $e;
        }

        return [
            'Mensagem' => 'Locação criada com sucesso.',
        ];
    }

    /**
     * Lista todas as locações com seus respectivos itens.
     *
     * @return array<LocacaoResumo> Array contendo todas as locações cadastradas com seus itens.
     */
    public function listarTodos(): array
    {
        $locacoes = $this->locacaoRepositorio->buscarTodos();
        if (empty($locacoes)) {
            throw DominioException::com(['Nenhuma locação encontrada.']);
        }
        foreach ($locacoes as $locacao) {
            $itensParaAdicionar = $this->locacaoItemRepositorio->buscarPorLocacaoId($locacao->getId());
            $locacao->addItens($itensParaAdicionar);
        }
        return $locacoes;
    }
}
