<?php

class LocacaoResumo
{
    /**
     * Construtor da classe LocacaoResumo.
     *
     * @param int $id ID da locação.
     * @param array{id:int, nome:string, telefone?:string|null, cpf?:string|null} $cliente Dados do cliente.
     * @param array{id:int, nome:string} $funcionario Dados do funcionário.
     * @param DateTimeImmutable $inicio Data e hora de início da locação.
     * @param int $horasContratadas Quantidade de horas contratadas.
     * @param DateTimeImmutable $prevista Data e hora prevista para entrega.
     * @param float $desconto Valor do desconto aplicado.
     * @param float $valor_total_previsto Valor total da locação.
     * @param string $status Status atual da locação.
     * @param array<int, array{id: int, valor_hora: float}>|null $itens Itens associados à locação.
     */
    public function __construct(
        private int $id,
        private array $cliente,
        private array $funcionario,
        private DateTimeImmutable $inicio,
        private int $horasContratadas,
        private DateTimeImmutable $prevista,
        private float $desconto,
        private float $valor_total_previsto,
        private string $status,
        private ?array $itens = null
    ) {}

    /**
     * Converte o objeto LocacaoResumo para um array.
     *
     * @return array{
     *     id: int,
     *     cliente: array{id:int, nome:string, telefone?:string|null, cpf?:string|null},
     *     funcionario: array{id:int, nome:string},
     *     data_hora_locacao: string,
     *     horas_contratadas: int,
     *     data_hora_entrega_prevista: string,
     *     desconto: float,
     *     valor_total_previsto: float,
     *     status: string,
     *     itens: array<int, array{id: int, valor_hora: float}>|null
     * }
     */
    public function toArray(): array
    {
        $itensArray = [];
        foreach ($this->itens ?? [] as $item) {
            if ($item instanceof LocacaoItemResumo) {
                $itensArray[] = [
                    'id'         => $item->getItemId(),
                    'valor_hora' => $item->getValorHora(),
                ];
            } elseif (is_array($item)) {
                $itensArray[] = [
                    'id'         => (int)   $item['id'],
                    'valor_hora' => (float) $item['valor_hora'],
                ];
            }
        }

        return [
            'id' => $this->id,
            'cliente' => $this->cliente,
            'funcionario' => $this->funcionario,
            'data_hora_locacao' => $this->inicio->format('Y-m-d H:i:s'),
            'horas_contratadas' => $this->horasContratadas,
            'data_hora_entrega_prevista' => $this->prevista->format('Y-m-d H:i:s'),
            'desconto' => $this->desconto,
            'valor_total_previsto' => $this->valor_total_previsto,
            'status' => $this->status,
            'itens' => $itensArray
        ];
    }

    /**
     * Cria uma instância de LocacaoResumo a partir de um array de dados.
     *
     * @param array{
     *     id: int|string,
     *     cliente_id: int|string,
     *     cliente_nome: string,
     *     cliente_telefone?: string|null,
     *     cliente_cpf?: string|null,
     *     funcionario_id: int|string,
     *     funcionario_nome: string,
     *     data_hora_locacao: string,
     *     horas_contratadas: int|string,
     *     data_hora_entrega_prevista: string,
     *     desconto_aplicado: float|string,
     *     valor_total_previsto: float|string,
     *     status: string,
     *     itens?: array<int, array{item_id:int|string, valor_hora:float|string}>|null
     * } $data Dados para criar o LocacaoResumo.
     * @return self
     */
    public static function fromArray(array $data): self
    {

        $itens = array_map(fn($item) => [
            'id' => (int) $item['item_id'],
            'valor_hora' => (float) $item['valor_hora']
        ], $data['itens'] ?? []);

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
            inicio: new DateTimeImmutable($data['data_hora_locacao'], new DateTimeZone('America/Sao_Paulo')),
            horasContratadas: (int) $data['horas_contratadas'],
            prevista: new DateTimeImmutable($data['data_hora_entrega_prevista'], new DateTimeZone('America/Sao_Paulo')),
            desconto: (float) $data['desconto_aplicado'],
            valor_total_previsto: (float) $data['valor_total_previsto'],
            status: $data['status'],
            itens: $itens

        );
    }

    /**
     * @param LocacaoItemResumo[] $itens
     */
    public function addItens(array $itens): void
    {
        $this->itens = $itens;
    }

    //getters

    /**
     * Obtém o ID da locação.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Obtém os dados do cliente.
     * @return array{id:int, nome:string, telefone?:string|null, cpf?:string|null}
     */
    public function getCliente(): array
    {
        return $this->cliente;
    }

    /**
     * Obtém os dados do funcionário.
     * @return array{id:int, nome:string}
     */
    public function getFuncionario(): array
    {
        return $this->funcionario;
    }

    /**
     * Obtém a data e hora de início da locação.
     * @return DateTimeImmutable
     */
    public function getInicio(): DateTimeImmutable
    {
        return $this->inicio;
    }

    /**
     * Obtém a quantidade de horas contratadas.
     * @return int
     */
    public function getHorasContratadas(): int
    {
        return $this->horasContratadas;
    }

    /**
     * Obtém a data e hora prevista para entrega.
     * @return DateTimeImmutable
     */
    public function getPrevista(): DateTimeImmutable
    {
        return $this->prevista;
    }

    /**
     * Obtém o valor do desconto.
     * @return float
     */
    public function getDesconto(): float
    {
        return $this->desconto;
    }

    /**
     * Obtém o valor total da locação.
     * @return float
     */
    public function getvalor_total_previsto(): float
    {
        return $this->valor_total_previsto;
    }

    /**
     * Obtém o status da locação.
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Obtém os itens da locação.
     * @return array<int, array{id: int, valor_hora: float}>|null
     */
    public function getItens(): ?array
    {
        return $this->itens;
    }

    /**
     * Define o ID da locação.
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
