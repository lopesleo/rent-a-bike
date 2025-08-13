<?php

class Avaria
{
    private int $id;
    private DateTimeImmutable $dataHora;
    private Funcionario $funcionario;
    private string $descricao;
    private float $valor;
    private string $foto;
    private Item $item;
    private Devolucao $devolucao;

    public function __construct(

        DateTimeImmutable $dataHora,
        Funcionario $funcionario,
        string $descricao,
        float $valor,
        string $foto,
        Item $item,
        Devolucao $devolucao,
        int $id = 0
    ) {
        $this->dataHora   = $dataHora;
        $this->funcionario  = $funcionario;
        $this->descricao  = $descricao;
        $this->valor      = $valor;
        $this->foto       = $foto;
        $this->item       = $item;
        $this->devolucao  = $devolucao;
        $this->id         = $id;
    }

    public function validar(): array
    {
        $erros = [];

        if (empty($this->descricao)) {
            $erros[] = 'Descrição não pode ser vazia.';
        }

        if ($this->valor <= 0) {
            $erros[] = 'Valor deve ser maior que zero.';
        }


        if (!$this->item) {
            $erros[] = 'Item não pode ser vazio.';
        }

        if (!$this->devolucao) {
            $erros[] = 'Devolução não pode ser vazia.';
        }

        return $erros;
    }


    public function toArray(): array
    {
        return [
            'id'          => $this->getId(),
            'data_hora'   => $this->getDataHora()->format('Y-m-d H:i:s'),
            'funcionario'   => $this->getFuncionario()->toArray(),
            'descricao'   => $this->getDescricao(),
            'valor'       => $this->getValor(),
            'foto'        => $this->getFoto(),
            'item'        => $this->getItem()->toArray(),
            'devolucao'   => $this->getDevolucao()->toArray(),
        ];
    }
    public function getId()
    {
        return $this->id;
    }

    public function getDataHora(): DateTimeImmutable
    {
        return $this->dataHora;
    }
    public function getFuncionario(): Funcionario
    {
        return $this->funcionario;
    }
    public function getDescricao(): string
    {
        return $this->descricao;
    }
    public function getValor(): float
    {
        return $this->valor;
    }
    public function getFoto(): string
    {
        return $this->foto;
    }
    public function getItem(): Item
    {
        return $this->item;
    }
    public function getDevolucao(): Devolucao
    {
        return $this->devolucao;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setDataHora(DateTimeImmutable $dataHora): void
    {
        $this->dataHora = $dataHora;
    }
    public function setFuncionario(Funcionario $funcionario): void
    {
        $this->funcionario = $funcionario;
    }
    public function setDescricao(string $descricao): void
    {

        $this->descricao = $descricao;
    }
    public function setValor(float $valor): void
    {
        $this->valor = $valor;
    }
    public function setFoto(string $foto): void
    {
        $this->foto = $foto;
    }
    public function setItem(Item $item): void
    {
        $this->item = $item;
    }
    public function setDevolucao(Devolucao $devolucao): void
    {
        $this->devolucao = $devolucao;
    }
}
