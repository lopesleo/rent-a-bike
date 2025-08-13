<?php

interface IUsuarioRepositorio
{
    /**
     * @param string $login  Nome de usuário (login)
     * @param string $senha  Senha em texto puro
     * @return array|null    Dados do funcionário (ou perfil) para a sessão
     */
    public function realizarLogin(string $login, string $senha): ?array;

    /**
     * @param int $id ID do usuário
     * @return array|null Dados do usuário ou null se não encontrado
     */
    public function buscarPorId(int $id): ?Usuario;
}
