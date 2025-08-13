<?php

describe('GestorUsuario', function () {

    beforeAll(function () {
        shell_exec('composer db');
        $this->pdo = Connection::getConnection();
        $this->repositorioUsuario = new UsuarioRepositorioEmBDR($this->pdo);
        $this->gestorUsuario = new GestorUsuario($this->repositorioUsuario);
    });

    context("ao tentar fazer login", function () {

        it('deve autenticar com sucesso um usuário e senha válidos', function () {
            $dadosLogin = ['usuario' => '12345678910', 'senha' => 'teste'];

            $resultado = $this->gestorUsuario->fazerLogin($dadosLogin);

            // Verifica se o login retornou os dados do funcionário
            expect($resultado)->not->toBeNull();
            expect($resultado['nome'])->toBe('Carlos Lima');
            expect($resultado['cargo'])->toBe('GERENTE');
        });

        it('deve lançar DominioException para uma senha incorreta', function () {
            $dadosLogin = ['usuario' => '12345678910', 'senha' => 'senha_errada'];

            $closure = function () use ($dadosLogin) {
                $this->gestorUsuario->fazerLogin($dadosLogin);
            };

            expect($closure)->toThrow(new DominioException('Senha incorreta.'));
        });

        it('deve lançar DominioException para um usuário inexistente', function () {
            $dadosLogin = ['usuario' => '00000000000', 'senha' => 'qualquer_senha'];

            $closure = function () use ($dadosLogin) {
                $this->gestorUsuario->fazerLogin($dadosLogin);
            };

            expect($closure)->toThrow(new DominioException('Usuário "00000000000" não encontrado.'));
        });
    });
});
