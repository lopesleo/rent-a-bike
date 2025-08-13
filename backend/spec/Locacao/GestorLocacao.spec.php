<?php

describe('GestorLocacao', function () {

    beforeAll(function () {
        shell_exec('composer db');

        $this->pdo = Connection::getConnection();
        $this->repositorioItem = new ItemRepositorioEmBDR($this->pdo);
        $this->repositorioLocacao = new LocacaoRepositorioEmBDR($this->pdo);

        $this->gestorLocacao = new GestorLocacao(
            $this->repositorioLocacao,
            $this->repositorioItem,
            new LocacaoItemRepositorioEmBDR($this->pdo),
            new GestorCliente(new ClienteRepositorioEmBDR($this->pdo)),
            new GestorFuncionario(new FuncionarioRepositorioEmBDR($this->pdo)),
            new TransacaoEmBDR($this->pdo)
        );
    });

    context("ao criar uma locação válida", function () {

        it('deve marcar o item da locação como indisponível', function () {
            // Usa o item 10, que está disponível no mock
            $itemAntes = $this->repositorioItem->buscarPorId(10);
            expect($itemAntes->isDisponivel())->toBe(true);

            $dados = ['cliente_id' => 1, 'funcionario_id' => 1, 'horas_contratadas' => 2, 'itens' => [['id' => 10]]];
            $this->gestorLocacao->criarLocacao($dados);

            $itemDepois = $this->repositorioItem->buscarPorId(10);
            expect($itemDepois->isDisponivel())->toBe(false);
        });

        it('deve registrar uma nova locação no banco', function () {
            $locacoesAntes = $this->repositorioLocacao->buscarTodos();

            $dadosValidos = ['cliente_id' => 2, 'funcionario_id' => 2, 'horas_contratadas' => 3, 'itens' => [['id' => 12]]];
            $this->gestorLocacao->criarLocacao($dadosValidos);

            $locacoesDepois = $this->repositorioLocacao->buscarTodos();
            expect($locacoesDepois)->toHaveLength(count($locacoesAntes) + 1);
        });
    });

    context("ao tentar criar uma locação inválida", function () {

        it('deve lançar DominioException ao tentar locar um item já indisponível', function () {
            $dados = ['cliente_id' => 1, 'funcionario_id' => 1, 'horas_contratadas' => 1, 'itens' => [['id' => 1]]];

            $closure = function () use ($dados) {
                $this->gestorLocacao->criarLocacao($dados);
            };

            expect($closure)->toThrow(new DominioException('Item IT001 indisponível'));
        });
    });
});
