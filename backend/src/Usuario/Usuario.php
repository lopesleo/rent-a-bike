<?php

class Usuario
{
    const PEPPER = 'k#pW7$@ZgR9b!qN4sT8u&v*Yx%C2e(H5';
    private int    $id;
    private string $login;
    private string $senhaHash;
    private string $salt;

    public function __construct(string $login, string $senha)
    {
        $this->login      = $login;
        $this->salt       = bin2hex(random_bytes(16));
        $this->senhaHash  = hash('sha512', $this->salt . $senha . self::PEPPER);
    }


    public static function fromArray(array $data): self
    {
        $usuario = new self($data['login'], '');
        $usuario->id = (int)$data['id'];
        $usuario->senhaHash = $data['senha_hash'];
        $usuario->salt = $data['salt'];
        return $usuario;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getLogin(): string
    {
        return $this->login;
    }

    public function getSenhaHash(): string
    {
        return $this->senhaHash;
    }

    public function getSalt(): string
    {
        return $this->salt;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function setSenhaHash(string $senhaHash): void
    {
        $this->senhaHash = $senhaHash;
    }

    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }


    public function verificarSenha(string $senha): bool
    {
        $hash = hash('sha512', $this->salt . $senha . self::PEPPER);
        return hash_equals($this->senhaHash, $hash);
    }
}
