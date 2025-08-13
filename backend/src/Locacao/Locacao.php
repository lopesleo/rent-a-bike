<?php

class Locacao
{

    private DateTimeImmutable $inicio;
    private DateTimeImmutable $prevista;
    private string            $status;
    private float             $desconto;
    private float             $valorTotal;
    private int               $id = 0;
    /**
     * @param Cliente     $cliente
     * @param Funcionario $funcionario
     * @param int         $horasContratadas
     * @param LocacaoItem[] $itens
     */
    public function __construct(
        private Cliente     $cliente,
        private Funcionario $funcionario,
        private int         $horasContratadas,
        private array       $itens
    ) {
        $this->inicio  = new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo'));
        $this->prevista = $this->inicio->modify("+{$this->horasContratadas} hours");

        $this->calcularTotais();
    }
    private function calcularTotais(): void
    {
        $bruto = array_sum(array_map(fn(LocacaoItem $i) => $i->getValorHora() * $this->horasContratadas, $this->itens));
        $this->desconto   = $this->horasContratadas > 2 ? $bruto * 0.10 : 0.0;
        $this->valorTotal = $bruto - $this->desconto;
    }

    /**
     * Valida os dados da locação.
     *
     * @return string[] Um array de mensagens de erro, vazio se a locação for válida.
     */
    function validar(): array
    {
        $erros = [];
        if ($this->horasContratadas < 1)    $erros[] = 'Horas contratadas deve ser >= 1';
        if (empty($this->itens))            $erros[] = 'Deve ter ao menos um item';
        if ($this->valorTotal <= 0)         $erros[] = 'Valor total deve ser > 0';
        if ($this->desconto < 0)            $erros[] = 'Desconto não pode ser negativo';
        if ($this->desconto > $this->valorTotal) {
            $erros[] = 'Desconto não pode ser maior que o valor total';
        }
        if ($this->inicio > $this->prevista) {
            $erros[] = 'Data de início não pode ser maior que a data prevista';
        }
        if ($this->inicio < new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo'))) {
            $erros[] = 'Data de início não pode ser no passado';
        }
        //validar se desconto está correto

        if ($this->desconto > 0 && $this->horasContratadas <= 2) {
            $erros[] = 'Desconto só é válido para locações com mais de 2 horas';
        }

        if ($this->desconto > 0 && $this->desconto != ($this->valorTotal * 0.10)) {
            $erros[] = 'Desconto deve ser 10% do valor total para locações com mais de 2 horas';
        }
        return $erros;
    }


    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function getCliente(): Cliente
    {
        return $this->cliente;
    }
    public function setCliente(Cliente $cliente): void
    {
        $this->cliente = $cliente;
    }

    public function getFuncionario(): Funcionario
    {
        return $this->funcionario;
    }

    public function setFuncionario(Funcionario $funcionario): void
    {
        $this->funcionario = $funcionario;
    }

    public function getInicio(): DateTimeImmutable
    {
        return $this->inicio;
    }


    public function getHorasContratadas(): int
    {
        return $this->horasContratadas;
    }

    public function getPrevista(): DateTimeImmutable
    {
        return $this->prevista;
    }

    public function getDesconto(): float
    {
        return $this->desconto;
    }

    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }

    /** @return LocacaoItem[] */
    public function getItens(): array
    {
        return $this->itens;
    }

    public function setItem(LocacaoItem $item): void
    {
        $this->itens[] = $item;
        $this->calcularTotais();
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    public function setInicio(DateTimeImmutable $inicio): void
    {
        $this->inicio = $inicio;
        $this->prevista = $inicio->modify("+{$this->horasContratadas} hours");
        $this->calcularTotais();
    }
    public function setPrevista(DateTimeImmutable $prevista): void
    {
        $this->prevista = $prevista;
        $this->calcularTotais();
    }
    public function setDesconto(float $desconto): void
    {
        $this->desconto = $desconto;
        $this->calcularTotais();
    }
    public function setValorTotal(float $valorTotal): void
    {
        $this->valorTotal = $valorTotal;
        $this->calcularTotais();
    }
}
