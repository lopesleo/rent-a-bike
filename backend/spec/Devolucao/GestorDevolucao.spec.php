<?php

describe('GestorDevolucao', function () {

    beforeAll(function () {
        $this->pdo = Connection::getConnection();
        $this->repositorioItem = new ItemRepositorioEmBDR($this->pdo);
        $this->repositorioLocacao = new LocacaoRepositorioEmBDR($this->pdo);
        $this->devolucaoRepo = new DevolucaoRepositorioEmBDR($this->pdo);
        $this->avariaRepo = new AvariaRepositorioEmBDR($this->pdo);
        $this->locacaoItemRepo = new LocacaoItemRepositorioEmBDR($this->pdo);

        $this->gestorDevolucao = new GestorDevolucao(
            $this->devolucaoRepo,
            $this->repositorioItem,
            $this->repositorioLocacao,
            $this->locacaoItemRepo,
            new GestorLocacaoItem($this->locacaoItemRepo),
            new GestorItem($this->repositorioItem),
            new GestorCliente(new ClienteRepositorioEmBDR($this->pdo)),
            new GestorFuncionario(new FuncionarioRepositorioEmBDR($this->pdo)),
            new TransacaoEmBDR($this->pdo),
            $this->avariaRepo
        );
    });



    context("ao criar uma devolução", function () {

        it('deve alterar o status da locação para FINALIZADA', function () {
            $locacaoAntes = $this->repositorioLocacao->buscarPorIdModel(1);
            expect($locacaoAntes->getStatus())->toBe('EM_ANDAMENTO');

            $dados = ['locacao_id' => 1, 'funcionario_id' => 1, 'itens' => []];
            $this->gestorDevolucao->criarDevolucao($dados);

            $locacaoDepois = $this->repositorioLocacao->buscarPorIdModel(1);
            expect($locacaoDepois->getStatus())->toBe('FINALIZADA');
        });

        it('deve calcular o valor final corretamente com avarias', function () {
            $locacao = $this->repositorioLocacao->buscarPorIdModel(2);
            $valorOriginal = $locacao->getValorTotal(); // 2.50

            $dados = [
                'locacao_id' => 2,
                'funcionario_id' => 1,
                'itens' => [
                    ['item_id' => 2, 'limpeza_aplicada' => false, 'avarias' => [['descricao' => 'dano', 'valor' => 50.00, 'foto' => '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAMCAgICAgMCAgIDAwMDBAYEBAQEBAgGBgUGCQgKCgkICQkKDA8MCgsOCwkJDRENDg8QEBEQCgwSExIQEw8QEBD/2wBDAQMDAwQDBAgEBAgQCwkLEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBD/wAARCAABAAEDAREAAhEBAxEB/8QAFAABAAAAAAAAAAAAAAAAAAAACP/EABQQAQAAAAAAAAAAAAAAAAAAAAD/xAAVAQEBAAAAAAAAAAAAAAAAAAAGCP/EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhEDEQA/AHacJUf/2Q==']]] //1px image
                ]
            ];

            $valorEsperado = $valorOriginal + 50.00;
            $this->gestorDevolucao->criarDevolucao($dados);

            $devolucoes = $this->devolucaoRepo->buscarTodos();
            $devolucaoCriada = end($devolucoes);
            expect($devolucaoCriada->getValorPago())->toBeCloseTo($valorEsperado);
        });

        it('deve disponibilizar o item novamente após a devolução', function () {
            $itemAntes = $this->repositorioItem->buscarPorId(3);
            expect($itemAntes->isDisponivel())->toBe(false);

            $dados = ['locacao_id' => 3, 'funcionario_id' => 2, 'itens' => []];
            $this->gestorDevolucao->criarDevolucao($dados);

            $itemDepois = $this->repositorioItem->buscarPorId(3);
            expect($itemDepois->isDisponivel())->toBe(true);
        });

        it('deve adicionar 10% de taxa de limpeza sobre o valor do item', function () {
            $locacao = $this->repositorioLocacao->buscarPorIdModel(4);
            $valorOriginal = $locacao->getValorTotal(); // R$15.00
            $taxaLimpeza = $valorOriginal * 0.10; // R$1.50

            $dados = [
                'locacao_id' => 4,
                'funcionario_id' => 1,
                'itens' => [
                    ['item_id' => 4, 'limpeza_aplicada' => true, 'avarias' => []]
                ]
            ];

            $valorEsperado = $valorOriginal + $taxaLimpeza; // 15.00 + 1.50 = 16.50

            $this->gestorDevolucao->criarDevolucao($dados);

            $devolucoes = $this->devolucaoRepo->buscarTodos();
            $devolucaoCriada = null;
            foreach ($devolucoes as $d) {
                if ($d->getLocacaoId() === 4) {
                    $devolucaoCriada = $d;
                    break;
                }
            }

            expect($devolucaoCriada->getValorPago())->toBeCloseTo($valorEsperado);
        });
        it('deve cobrar uma hora extra se a devolução atrasar mais de 15 minutos', function () {
            $locacaoId = 5;
            $horasContratadas = 1;
            $valorPorHoraItem = 16.00; // Valor por hora do item

            $dados = ['locacao_id' => $locacaoId, 'funcionario_id' => 1, 'itens' => []];
            $this->gestorDevolucao->criarDevolucao($dados);

            $valorEsperado = 2 * $valorPorHoraItem;

            $devolucoes = $this->devolucaoRepo->buscarTodos();
            $devolucaoCriada = null;
            foreach ($devolucoes as $d) {
                if ($d->getLocacaoId() === $locacaoId) {
                    $devolucaoCriada = $d;
                    break;
                }
            }
            expect($devolucaoCriada->getValorPago())->toBeCloseTo($valorEsperado);
        });
    });
});
