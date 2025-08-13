<?php

class LocacaoItemResumo
{

    /**
     * Construtor da classe LocacaoItemResumo.
     *
     * @param int $locacao_id ID da locação.
     * @param int $item_id ID do item.
     * @param float $valor_hora Valor por hora do item na locação.
     */
    function __construct(
        private int $locacao_id,
        private int $item_id,
        private float $valor_hora,
    ) {}
    /**
     * Converte o objeto LocacaoItemResumo em um array.
     *
     * @return array{item_id: int, valor_hora: float} Dados do item de locação.
     */
    public function toArray(): array
    {
        return [
            'item_id' => $this->item_id,
            'valor_hora' => $this->valor_hora,
        ];
    }
    /**
     * Cria uma instância de LocacaoItemResumo a partir de um array.
     *
     * @param array{locacao_id:int|string, item_id:int|string, valor_hora:float|string} $data Dados para criar o LocacaoItemResumo.
     * @return LocacaoItemResumo
     */
    public static function fromArray(array $data): self
    {
        return new self(
            locacao_id: (int)$data['locacao_id'],
            item_id: (int)$data['item_id'],
            valor_hora: (float)$data['valor_hora'],
        );
    }
    /**
     * Obtém o ID da locação.
     *
     * @return int
     */
    public function getLocacaoId(): int
    {
        return $this->locacao_id;
    }
    /**
     * Obtém o ID do item.
     *
     * @return int
     */
    public function getItemId(): int
    {
        return $this->item_id;
    }
    /**
     * Obtém o valor por hora.
     *
     * @return float
     */
    public function getValorHora(): float
    {
        return $this->valor_hora;
    }
    /**
     * Obtém o valor total.
     *
     * @return float
     */

    /**
     * Define o ID da locação.
     *
     * @param int $locacao_id
     * @return void
     */
    public function setLocacaoId(int $locacao_id): void
    {
        $this->locacao_id = $locacao_id;
    }
    /**
     * Define o ID do item.
     *
     * @param int $item_id
     * @return void
     */
    public function setItemId(int $item_id): void
    {
        $this->item_id = $item_id;
    }
    /**
     * Define o valor por hora.
     *
     * @param float $valor_hora
     * @return void
     */
    public function setValorHora(float $valor_hora): void
    {
        $this->valor_hora = $valor_hora;
    }
}
