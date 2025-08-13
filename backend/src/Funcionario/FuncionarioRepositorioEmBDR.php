<?php

class FuncionarioRepositorioEmBDR implements IFuncionarioRepositorio
{
    public function __construct(private PDO $conexao) {}

    public function buscarPorId(int $id): ?Funcionario
    {
        $sql = '
            SELECT f.*,
                   usuario.id         AS usuario_id,
                   usuario.login,
                   usuario.salt,
                   usuario.senha_hash
              FROM funcionario f
              JOIN usuario usuario ON f.usuario_id = usuario.id
             WHERE f.id = :id
        ';
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch();
        return $data ? Funcionario::fromArray($data) : null;
    }

    public function buscarTodos(): array
    {
        $sql = '
            SELECT f.*,
                   usuario.id         AS usuario_id,
                   usuario.login,
                   usuario.salt,
                   usuario.senha_hash
              FROM funcionario f
              JOIN usuario usuario ON f.usuario_id = usuario.id
        ';
        $stmt = $this->conexao->query($sql);
        $lista = [];
        while ($data = $stmt->fetch()) {
            $lista[] = Funcionario::fromArray($data);
        }
        return $lista;
    }

    public function buscaPorCPF(string $cpf): ?Funcionario
    {
        $stmt = $this->conexao->prepare('
            SELECT f.*,
                   usuario.id         AS usuario_id,
                   usuario.login,
                   usuario.salt,
                   usuario.senha_hash
              FROM funcionario f
              JOIN usuario usuario ON f.usuario_id = usuario.id
             WHERE f.cpf = :cpf
        ');
        $stmt->execute(['cpf' => $cpf]);
        $data = $stmt->fetch();
        return $data ? Funcionario::fromArray($data) : null;
    }

    public function buscarPorUsuarioId(int $usuarioId): ?Funcionario
    {
        $stmt = $this->conexao->prepare('
            SELECT f.*,
                   usuario.id         AS usuario_id,
                   usuario.login,
                   usuario.salt,
                   usuario.senha_hash
              FROM funcionario f
              JOIN usuario usuario ON f.usuario_id = usuario.id
             WHERE f.usuario_id = :usuario_id
        ');
        $stmt->execute(['usuario_id' => $usuarioId]);
        $data = $stmt->fetch();
        return $data ? Funcionario::fromArray($data) : null;
    }

    public function buscarPorCodigo(string $codigo): ?Funcionario
    {
        $stmt = $this->conexao->prepare('
            SELECT f.*,
                   usuario.id         AS usuario_id,
                   usuario.login,
                   usuario.salt,
                   usuario.senha_hash
              FROM funcionario f
              JOIN usuario usuario ON f.usuario_id = usuario.id
             WHERE f.codigo = :codigo
        ');
        $stmt->execute(['codigo' => $codigo]);
        $data = $stmt->fetch();
        return $data ? Funcionario::fromArray($data) : null;
    }

    public function salvar(Funcionario $funcionario): void
    {
        $usuario = $funcionario->getUsuario();
        $stmtU = $this->conexao->prepare('
            INSERT INTO usuario (login, salt, senha_hash)
            VALUES (:login, :salt, :hash)
        ');
        $stmtU->execute([
            'login' => $usuario->getLogin(),
            'salt'  => $usuario->getSalt(),
            'hash'  => $usuario->getSenhaHash(),
        ]);
        $usuario->setId((int)$this->conexao->lastInsertId());

        $stmtF = $this->conexao->prepare('
            INSERT INTO funcionario
                (usuario_id, codigo, nome, telefone, email, cargo, cpf)
            VALUES
                (:uid, :codigo, :nome, :telefone, :email, :cargo, :cpf)
        ');
        $stmtF->execute([
            'uid'      => $usuario->getId(),
            'codigo'   => $funcionario->getCodigo(),
            'nome'     => $funcionario->getNome(),
            'telefone' => $funcionario->getTelefone(),
            'email'    => $funcionario->getEmail(),
            'cargo'    => $funcionario->getCargo(),
            'cpf'      => $funcionario->getCpf(),
        ]);
        $funcionario->setId((int)$this->conexao->lastInsertId());
    }

    public function atualizar(Funcionario $funcionario): void {}

    public function remover(int $id): void
    {
        $stmt = $this->conexao->prepare('DELETE FROM funcionario WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
