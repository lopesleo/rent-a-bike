<?php

class Funcionario
{
    private int     $id;
    private Usuario $usuario;
    private string  $codigo;
    private string  $nome;
    private ?string $telefone;
    private ?string $email;
    private string  $cargo;
    private string  $cpf;

    public function __construct(
        int $id,
        Usuario $usuario,
        string $codigo,
        string $nome,
        ?string $telefone,
        ?string $email,
        string $cargo,
        string $cpf
    ) {
        $this->id       = $id;
        $this->usuario  = $usuario;
        $this->codigo   = $codigo;
        $this->nome     = $nome;
        $this->telefone = $telefone;
        $this->email    = $email;
        $this->cargo    = $cargo;
        $this->cpf      = $cpf;
    }

    public static function fromArray(array $dados): self
    {
        $usuarioData = [
            'id'         => $dados['usuario_id'],
            'login'      => $dados['login'],
            'salt'       => $dados['salt'],
            'senha_hash' => $dados['senha_hash'],
        ];
        $usuario = Usuario::fromArray($usuarioData);

        return new self(
            (int)$dados['id'],
            $usuario,
            $dados['codigo'],
            $dados['nome'],
            $dados['telefone'] ?? null,
            $dados['email'] ?? null,
            $dados['cargo'],
            $dados['cpf']
        );
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'usuario_id' => $this->usuario->getId(),
            'login'      => $this->usuario->getLogin(),
            'codigo'     => $this->codigo,
            'nome'       => $this->nome,
            'telefone'   => $this->telefone,
            'email'      => $this->email,
            'cargo'      => $this->cargo,
            'cpf'        => $this->cpf,
        ];
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }
    public function getCodigo(): string
    {
        return $this->codigo;
    }
    public function getNome(): string
    {
        return $this->nome;
    }
    public function getTelefone(): ?string
    {
        return $this->telefone;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function getCargo(): string
    {
        return $this->cargo;
    }
    public function getCpf(): string
    {
        return $this->cpf;
    }
    public function getId(): int
    {
        return $this->id;
    }

    //setters...
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setTelefone(?string $telefone): void
    {
        $this->telefone = $telefone;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function setCargo(string $cargo): void
    {
        $this->cargo = $cargo;
    }

    public function setCpf(string $cpf): void
    {
        $this->cpf = $cpf;
    }
    public function setUsuario(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }


    public function validar(): array
    {
        $erros = [];
        if (empty($this->usuario->getLogin())) {
            $erros[] = 'Login obrigatório.';
        }
        if (empty($this->codigo)) {
            $erros[] = 'Código obrigatório.';
        }
        if (empty($this->nome)) {
            $erros[] = 'Nome obrigatório.';
        }
        if (! preg_match('/^\d{11}$/', $this->cpf)) {
            $erros[] = 'CPF inválido.';
        }
        return $erros;
    }
}
