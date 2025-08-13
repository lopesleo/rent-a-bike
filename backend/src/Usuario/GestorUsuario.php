<?php

class GestorUsuario
{
    /**
     * Construtor do GestorUsuario.
     *
     * @param IUsuarioRepositorio $repositorio O repositório de funcionários.
     */
    function __construct(private IUsuarioRepositorio $repositorio) {}

    /**
     * Realizar Login.
     *
     * @param array $dados CPF do funcionario
     * @return array|null O funcionário encontrado ou null se não existir.
     */
    public function fazerLogin(array $dados): ?array
    {
        $erros = [];

        foreach (['usuario', 'senha'] as $campo) {
            if (empty($dados[$campo])) {
                $erros[] = "Campo '{$campo}' é obrigatório.";
            }
        }
        if ($erros) {
            throw DominioException::com($erros);
        }

        $usuario = $dados['usuario'];
        $senha = $dados['senha'];

        return $this->repositorio->realizarLogin($usuario, $senha);
    }

    /**
     * Buscar usuário por ID.
     *
     * @param int $id ID do usuário.
     * @return array|null O usuário encontrado ou null se não existir.
     */
    public function buscarPorId(int $id): ?array
    {
        if ($id <= 0) {
            throw DominioException::com(['ID inválido.']);
        }

        return $this->repositorio->buscarPorId($id);
    }
}
