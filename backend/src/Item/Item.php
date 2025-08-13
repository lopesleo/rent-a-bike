<?php

class Item
{
    /**
     * Construtor da classe Item.
     *
     * @param int $id ID do item.
     * @param string $codigo Código único do item.
     * @param string|null $modelo Modelo do item.
     * @param string|null $fabricante Fabricante do item.
     * @param string|null $descricao Descrição do item.
     * @param float $valorHora Valor da locação por hora.
     * @param string|null $numeroSeguro Número do seguro do item.
     * @param bool $disponivel Indica se o item está disponível para locação.
     * @param string $tipo Tipo do item (ex: bicicleta, patinete).
     */
    public function __construct(
        private int $id,
        private string $codigo,
        private ?string $modelo,
        private ?string $fabricante,
        private ?string $descricao,
        private float $valorHora,
        private ?string $numeroSeguro,
        private bool $disponivel,
        private string $tipo,
        private array $avarias = [],
    ) {}

    /**
     * Cria uma instância de Item a partir de um array de dados.
     *
     * @param array{id:int|string, codigo:string, modelo?:string|null, fabricante?:string|null, descricao?:string|null, valor_hora:float|string, numero_seguro?:string|null, disponivel:bool|int|string, tipo:string} $data Dados para criar o item.
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int)   $data['id'],
            $data['codigo'],
            $data['modelo']         ?? null,
            $data['fabricante']     ?? null,
            $data['descricao']      ?? null,
            (float) $data['valor_hora'],
            $data['numero_seguro']  ?? null,
            (bool)  $data['disponivel'],
            $data['tipo'],
            [],
        );
    }


    /**
     * Converte o objeto Item para um array.
     *
     * @return array{id:int, codigo:string, modelo:string|null, fabricante:string|null, descricao:string|null, valor_hora:float, avarias:string|null, numero_seguro:string|null, disponivel:bool, tipo:string}
     */
    public function toArray(): array
    {
        return [
            'id'            => $this->getId(),
            'codigo'        => $this->getCodigo(),
            'modelo'        => $this->getModelo(),
            'fabricante'    => $this->getFabricante(),
            'descricao'     => $this->getDescricao(),
            'valor_hora'    => $this->getValorHora(),
            'numero_seguro' => $this->getNumeroSeguro(),
            'disponivel'    => $this->isDisponivel(),
            'tipo'          => $this->getTipo(),
            'avarias'       => array_map(fn($avaria) => $avaria->toArray(), $this->avarias),

        ];
    }

    /**
     * Obtém o ID do item.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    /**
     * Define o ID do item.
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Obtém o código do item.
     * @return string
     */
    public function getCodigo(): string
    {
        return $this->codigo;
    }
    /**
     * Define o código do item.
     * @param string $codigo
     * @return void
     */
    public function setCodigo(string $codigo): void
    {
        $this->codigo = $codigo;
    }

    /**
     * Obtém o modelo do item.
     * @return string|null
     */
    public function getModelo(): ?string
    {
        return $this->modelo;
    }
    /**
     * Define o modelo do item.
     * @param string|null $modelo
     * @return void
     */
    public function setModelo(?string $modelo): void
    {
        $this->modelo = $modelo;
    }

    /**
     * Obtém o fabricante do item.
     * @return string|null
     */
    public function getFabricante(): ?string
    {
        return $this->fabricante;
    }
    /**
     * Define o fabricante do item.
     * @param string|null $fabricante
     * @return void
     */
    public function setFabricante(?string $fabricante): void
    {
        $this->fabricante = $fabricante;
    }

    /**
     * Obtém a descrição do item.
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }
    /**
     * Define a descrição do item.
     * @param string|null $descricao
     * @return void
     */
    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }

    /**
     * Obtém o valor da locação por hora do item.
     * @return float
     */
    public function getValorHora(): float
    {
        return $this->valorHora;
    }
    /**
     * Define o valor da locação por hora do item.
     * @param float $valorHora
     * @return void
     */
    public function setValorHora(float $valorHora): void
    {
        $this->valorHora = $valorHora;
    }


    /**
     * Obtém o número do seguro do item.
     * @return string|null
     */
    public function getNumeroSeguro(): ?string
    {
        return $this->numeroSeguro;
    }
    /**
     * Define o número do seguro do item.
     * @param string|null $numeroSeguro
     * @return void
     */
    public function setNumeroSeguro(?string $numeroSeguro): void
    {
        $this->numeroSeguro = $numeroSeguro;
    }

    /**
     * Verifica se o item está disponível.
     * @return bool
     */
    public function isDisponivel(): bool
    {
        return $this->disponivel;
    }
    /**
     * Define a disponibilidade do item.
     * @param bool $disponivel
     * @return void
     */
    public function setDisponivel(bool $disponivel): void
    {
        $this->disponivel = $disponivel;
    }

    /**
     * Obtém o tipo do item.
     * @return string
     */
    public function getTipo(): string
    {
        return $this->tipo;
    }
    /**
     * Define o tipo do item.
     * @param string $tipo
     * @return void
     */
    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getAvarias(): array
    {
        return $this->avarias;
    }
    public function setAvarias(?array $avarias): void
    {
        $this->avarias = $avarias ?? [];
    }
}
