<?php

class UsuarioRepositorioEmBDR implements IUsuarioRepositorio
{

    public function __construct(private PDO $conexao) {}


    public function realizarLogin(string $login, string $senha): ?array
    {

        $stmt = $this->conexao->prepare('SELECT * FROM usuario WHERE login = :login');
        $stmt->execute(['login' => $login]);
        $usuarioData = $stmt->fetch();
        if (! $usuarioData) {
            throw DominioException::com(['Usuário "' . $login . '" não encontrado.']);
        }

        $usuario = Usuario::fromArray($usuarioData);

        if (! $usuario->verificarSenha($senha)) {
            throw DominioException::com(['Senha incorreta.']);
        }

        $loginObj = Usuario::fromArray($usuarioData);
        if (!$loginObj->verificarSenha($senha)) {
            return null;
        }

        $stmt2 = $this->conexao->prepare('
        SELECT
        f.*,
        u.login,
        u.salt,
        u.senha_hash
        FROM funcionario f
        JOIN usuario u ON u.id = f.usuario_id
        WHERE f.usuario_id = :usuario_id
    ');
        $stmt2->execute(['usuario_id' => $loginObj->getId()]);
        $funcData = $stmt2->fetch();
        if (!$funcData) {
            return null;
        }

        $func = Funcionario::fromArray($funcData);
        return $func->toArray();
    }

    public function buscarPorId(int $id): Usuario
    {
        if ($id <= 0) {
            throw DominioException::com(['ID inválido.']);
        }

        $stmt = $this->conexao->prepare('SELECT * FROM usuario WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $usuarioData = $stmt->fetch();
        if (!$usuarioData) {
            throw DominioException::com(['Usuário não encontrado.']);
        }

        return Usuario::fromArray($usuarioData);
    }
}
