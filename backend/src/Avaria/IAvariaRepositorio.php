<?php

interface IAvariaRepositorio
{
    /**
     * Cria uma nova avaria no repositório.
     *
     * @param Avaria $avaria Avaria a ser criada.
     * @return Avaria Avaria criada com ID gerado.
     */
    public function criar(Avaria $avaria): Avaria;

    /**
     * Busca uma avaria pelo ID.
     *
     * @param int $id ID da avaria.
     * @return Avaria|null Avaria encontrada ou null se não existir.
     */
    public function buscarPorId(int $id): ?Avaria;

    /**
     * Busca uma avaria pelo ID do item associado.
     *
     * @param int $itemId ID do item.
     * @return array Lista de avarias associadas ao item.
     */
    public function buscarPorItem(int $itemId): array;


    /**
     * Atualiza uma avaria existente.
     *
     * @param Avaria $avaria Avaria com dados atualizados.
     * @return void
     */
    public function atualizar(Avaria $avaria): void;

    /**
     * Atualiza o caminho da foto de uma avaria.
     *
     * @param int $id ID da avaria a ser atualizada.
     * @param string $caminhoFoto Novo caminho da foto.
     * @return void
     */
    public function atualizarFoto(int $id, string $caminhoFoto): void;
    /**
     * Remove uma avaria pelo ID.
     *
     * @param int $id ID da avaria a ser removida.
     * @return void
     */
    public function remover(int $id): void;
}
