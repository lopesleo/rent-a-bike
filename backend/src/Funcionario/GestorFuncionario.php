<?php

class GestorFuncionario
{
    /**
     * Construtor do GestorFuncionario.
     *
     * @param IFuncionarioRepositorio $repositorio O repositório de funcionários.
     */
    function __construct(private IFuncionarioRepositorio $repositorio) {}

    /**
     * Busca um funcionário pelo ID.
     *
     * @param int $id ID do funcionário.
     * @return Funcionario|null O funcionário encontrado ou null se não existir.
     */
    public function buscarPorId(int $id): ?Funcionario
    {
        return $this->repositorio->buscarPorId($id);
    }

    /**
     * Busca todos os funcionários.
     *
     * @return Funcionario[] Array de funcionários.
     */
    public function buscarTodos(): array
    {
        return $this->repositorio->buscarTodos();
    }


    /**
     * Busca um funcionário pelo CPF.
     *
     * @param string $cpf CPF do funcionário.
     * @return Funcionario|null O funcionário encontrado ou null se não existir.
     */
    public function buscaPorCPF(string $cpf): ?Funcionario
    {
        return $this->repositorio->buscaPorCPF($cpf);
    }

    /**
     * Busca um funcionário pelo código.
     *
     * @param string $codigo Código do funcionário.
     * @return Funcionario|null O funcionário encontrado ou null se não existir.
     */
    public function buscarPorCodigo(string $codigo): ?Funcionario
    {
        return $this->repositorio->buscarPorCodigo($codigo);
    }

    /**
     * Salva um funcionário.
     *
     * @param Funcionario $funcionario O funcionário a ser salvo.
     * @return void
     */
    public function salvar(Funcionario $funcionario): void
    {
        $erros = $funcionario->validar();
        if ($erros) {
            throw DominioException::com($erros);
        }
        $this->repositorio->salvar($funcionario);
    }

    public function buscarPorUsuarioId(int $usuarioId): ?Funcionario
    {
        return $this->repositorio->buscarPorUsuarioId($usuarioId);
    }

    /**
     * Atualiza um funcionário.
     *
     * @param Funcionario $funcionario O funcionário a ser atualizado.
     * @return void
     */
    public function atualizar(Funcionario $funcionario): void
    {
        $this->repositorio->atualizar($funcionario);
    }

    /**
     * Remove um funcionário pelo ID.
     *
     * @param int $id ID do funcionário a ser removido.
     * @return void
     */
    public function remover(int $id): void
    {
        $this->repositorio->remover($id);
    }
}
