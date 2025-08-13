<?php

/**
 * Exception for repository-related errors
 */
class RepositorioException extends RuntimeException
{

    /**
     * @var string[]
     */
    private array $problemas = [];

    /**
     * Retorna os problemas
     *
     * @return string[]
     */
    public function getProblemas(): array
    {
        return $this->problemas;
    }

    /**
     * Cria uma exceção.
     *
     * @param string[] $problemas
     * @return RepositorioException
     */
    public static function com(array $problemas): RepositorioException
    {
        $e = new RepositorioException();
        $e->problemas = $problemas;
        return $e;
    }
}
