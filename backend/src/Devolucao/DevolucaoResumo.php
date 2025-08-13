<?php

class DevolucaoResumo
{
    /**
     * @param int $id
     * @param array $cliente
     * @param array $funcionario
     * @param \DateTimeImmutable $dataHora
     * @param int $locacaoId
     * @param float $desconto
     * @param float $valorPago
     */
    public function __construct(
        private int $id,
        private array $cliente,
        private array $funcionario,
        private DateTimeImmutable $dataHora,
        private int $locacaoId,
        private float $desconto,
        private float $valorPago,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            cliente: [
                'id' => (int) $data['cliente_id'],
                'nome' => $data['cliente_nome'],
                'telefone' => $data['cliente_telefone'] ?? null,
                'cpf' => $data['cliente_cpf'] ?? null
            ],
            funcionario: [
                'id' => (int) $data['funcionario_id'],
                'nome' => $data['funcionario_nome']
            ],
            dataHora: new DateTimeImmutable($data['data_hora'], new DateTimeZone('America/Sao_Paulo')),
            locacaoId: (int) $data['locacao_id'],
            desconto: (float) $data['desconto_aplicado'],
            valorPago: (float) $data['valor_pago']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'numero_locacao' => $this->locacaoId,
            'nome_cliente' => $this->cliente["nome"],
            'data_hora_devolucao' => $this->dataHora->format('Y-m-d H:i:s'),
            'valor_pago' => $this->valorPago,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getCliente(): array
    {
        return $this->cliente;
    }
    public function getFuncionario(): array
    {
        return $this->funcionario;
    }
    public function getDataHora(): DateTimeImmutable
    {
        return $this->dataHora;
    }
    public function getLocacaoId(): int
    {
        return $this->locacaoId;
    }
    public function getDesconto(): float
    {
        return $this->desconto;
    }
    public function getValorPago(): float
    {
        return $this->valorPago;
    }
}
