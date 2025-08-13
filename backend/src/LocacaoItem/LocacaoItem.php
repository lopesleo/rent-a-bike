<?php

class LocacaoItem
{
    private ?Locacao $locacao;
    private Item $item;
    private float $valorHora;

    /**
     * Construtor da classe LocacaoItem.
     * @param Item $item O item associado à locação.
     * @param float $valorHora O valor por hora do item na locação.
     * @param Locacao|null $locacao A locação associada (opcional).
     */
    public function __construct(
        Item $item,
        float $valorHora,
        ?Locacao $locacao = null
    ) {
        $this->item = $item;
        $this->valorHora = $valorHora;
        $this->locacao   = $locacao;
    }



    /**
     * Valida os dados do item de locação.
     *
     * @return string[] Um array de mensagens de erro, vazio se não houver erros.
     */
    public function validar(): array
    {
        $erros = [];

        if ($this->valorHora <= 0) {
            $erros[] = 'Valor por hora deve ser maior que zero.';
        }
        if (!$this->item->isDisponivel()) {
            $erros[] = 'Item não está disponível para locação.';
        }

        if ($this->locacao !== null) {
            if ($this->locacao->getHorasContratadas() < 1) {
                $erros[] = 'Horas contratadas deve ser pelo menos 1.';
            }
        } else {
            $erros[] = 'Locação não associada.';
        }

        return $erros;
    }

    /**
     * Obtém a locação associada.
     *
     * @return Locacao|null
     */
    public function getLocacao(): ?Locacao
    {
        return $this->locacao;
    }
    /**
     * Obtém o item associado.
     *
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }
    /**
     * Obtém o valor por hora.
     *
     * @return float
     */
    public function getValorHora(): float
    {
        return $this->valorHora;
    }
    /**
     * Obtém o valor total.
     *
     * @return float
     */

    /**
     * Define a locação associada.
     *
     * @param Locacao $locacao A locação a ser associada.
     * @return void
     */
    public function setLocacao(Locacao $locacao): void
    {
        $this->locacao = $locacao;
    }
    /**
     * Define o item associado.
     *
     * @param Item $item
     * @return void
     */
    public function setItem(Item $item): void
    {
        $this->item = $item;
    }
    /**
     * Define o valor por hora.
     *
     * @param float $valorHora
     * @return void
     */
    public function setValorHora(float $valorHora): void
    {
        $this->valorHora = $valorHora;
    }
    /**
     * Define o valor total.
     *
     * @param float $valorTotal
     * @return void
     */
}
