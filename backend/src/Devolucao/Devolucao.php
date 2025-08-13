<?php

class Devolucao
{

    private int $id;
    private DateTimeImmutable $dataHora;
    private int $horasUsadas;
    private float $descontoAplicado;
    private float $valorPago;

    /**
     * @param Locacao $locacao
     * @param Funcionario $funcionario
     */
    public function __construct(
        private Locacao $locacao,
        private Funcionario $funcionario,
    ) {
        $this->dataHora = new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo'));
    }
    /**
     * Cria uma instância de Devolucao a partir de um array de dados.
     *
     * @param array{id:int, data_hora:string, horas_usadas:int, desconto_aplicado:float, valor_pago:float} $data Dados para criar a devolução.
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $devolucao = new self(
            LocacaoResumo::fromArray($data['locacao']),
            Funcionario::fromArray($data['funcionario'])
        );
        $devolucao->setId((int) $data['id']);
        $devolucao->dataHora = new DateTimeImmutable($data['data_hora'], new DateTimeZone('America/Sao_Paulo'));
        $devolucao->setHorasUsadas((int) $data['horas_usadas']);
        $devolucao->setDescontoAplicado((float) $data['desconto_aplicado']);
        $devolucao->setValorPago((float) $data['valor_pago']);
        return $devolucao;
    }

    /**
     * Converte o objeto Devolucao para um array.
     *
     * @return array{id:int, data_hora:string, horas_usadas:int, desconto_aplicado:float, valor_pago:float, locacao:array, funcionario:array}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'data_hora' => $this->getDataHora()->format('Y-m-d H:i:s'),
            'horas_usadas' => $this->getHorasUsadas(),
            'desconto_aplicado' => $this->getDescontoAplicado(),
            'valor_pago' => $this->getValorPago(),
            'locacao' => $this->getLocacao()->toArray(),
            'funcionario' => $this->getFuncionario()->toArray(),
        ];
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getDataHora(): DateTimeImmutable
    {
        return $this->dataHora;
    }

    public function getHorasUsadas(): int
    {
        return $this->horasUsadas;
    }

    public function getDescontoAplicado(): float
    {
        return $this->descontoAplicado;
    }

    public function getValorPago(): float
    {
        return $this->valorPago;
    }

    public function getLocacao(): Locacao
    {
        return $this->locacao;
    }

    public function getFuncionario(): Funcionario
    {
        return $this->funcionario;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setHorasUsadas(int $horasUsadas): void
    {
        $this->horasUsadas = $horasUsadas;
    }

    public function setDescontoAplicado(float $descontoAplicado): void
    {
        $this->descontoAplicado = $descontoAplicado;
    }

    public function setValorPago(float $valorPago): void
    {
        $this->valorPago = $valorPago;
    }

    public function calcularValores(): void
    {
        $locacao = $this->locacao;
        $dataEsperada = $locacao->getPrevista();
        $dataDevolucao = $this->dataHora ?? new DateTimeImmutable('now', new DateTimeZone('America/Sao_Paulo'));
        $horasOriginais = $locacao->getHorasContratadas();

        $tempoLimite = $dataEsperada->add(new DateInterval('PT15M'));
        $horasExtras = 0;
        if ($dataDevolucao > $tempoLimite) {
            $diferencaSegundos = $dataDevolucao->getTimestamp() - $dataEsperada->getTimestamp();
            $minutosExtras = $diferencaSegundos / 60;
            if ($minutosExtras > 0) {
                $horasExtras = ceil($minutosExtras / 60);
            }
        }
        $totalHoras = $horasOriginais + $horasExtras;
        $this->horasUsadas = $totalHoras;
        $valorBruto = 0.0;
        foreach ($locacao->getItens() as $item) {
            $valorBruto += $item->getValorHora() * $totalHoras;
        }
        $desconto = $totalHoras > 2 ? $valorBruto * 0.10 : 0.0;
        $this->descontoAplicado = $desconto;
        $this->valorPago = $valorBruto - $desconto;
    }
}
