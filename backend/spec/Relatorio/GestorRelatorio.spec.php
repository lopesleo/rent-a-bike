<?php

describe('GestorRelatorio', function () {

    beforeAll(function () {
        shell_exec('composer db');
        $this->pdo = Connection::getConnection();

        $this->pdo->exec("
            INSERT INTO locacao_item (locacao_id, item_id, valor_hora) VALUES (1, 10, 1.00);
            INSERT INTO locacao_item (locacao_id, item_id, valor_hora) VALUES (2, 10, 1.00);
            INSERT INTO locacao_item (locacao_id, item_id, valor_hora) VALUES (2, 12, 5.00);
            INSERT INTO locacao_item (locacao_id, item_id, valor_hora) VALUES (3, 12, 5.00);
            INSERT INTO locacao_item (locacao_id, item_id, valor_hora) VALUES (4, 12, 5.00);
        ");

        $this->gestorRelatorio = new GestorRelatorio(
            new RelatorioRepositorioEmBDR($this->pdo),
            new TransacaoEmBDR($this->pdo)
        );
    });

    context("ao gerar o relatório de Top Itens", function () {
        it('deve retornar os itens mais alugados em ordem decrescente', function () {
            $dataInicial = date('Y-m-d', strtotime('-1 month'));
            $dataFinal = date('Y-m-d', strtotime('+1 day'));

            $relatorio = $this->gestorRelatorio->buscarTopItens($dataInicial, $dataFinal);

            expect($relatorio[0]['codigo'])->toBe('IT012');
            expect($relatorio[0]['quantidade'])->toBe(3);

            expect($relatorio[1]['codigo'])->toBe('IT010');
            expect($relatorio[1]['quantidade'])->toBe(2);
        });
    });
});
