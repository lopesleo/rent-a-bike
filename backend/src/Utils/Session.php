<?php

class Session
{
    /**
     * Configura os parâmetros da sessão e a inicia.
     * Deve ser chamado uma única vez no início do script (ex: no index.php).
     */
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {

            session_set_cookie_params([
                'lifetime' => 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_name('funcionario');
            session_start();
        }
    }

    /**
     * Define os dados da sessão para um usuário após o login.
     * Regenera o ID da sessão para prevenir ataques de Session Fixation.
     * @param array $dadosFuncionario Dados do funcionário para armazenar.
     */
    public static function definirSessao(array $dadosFuncionario): void
    {
        self::iniciar();
        session_regenerate_id(true);
        $_SESSION = [];
        $_SESSION['id'] = $dadosFuncionario['id'];
        $_SESSION['nome'] = $dadosFuncionario['nome'];
        $_SESSION['cargo'] = $dadosFuncionario['cargo'];
        $_SESSION['logado_em'] = time();
    }

    /**
     * Encerra a sessão do usuário (logout).
     */
    public static function encerrarSessao(): void
    {
        self::iniciar();

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Verifica se a sessão do usuário é válida.
     */
    public static function isSessaoValida(): bool
    {
        self::iniciar();

        return isset($_SESSION['id']) && !empty($_SESSION['id']);
    }
}
