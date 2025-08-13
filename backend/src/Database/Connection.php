<?php

class Connection
{
    public static function  getConnection(): PDO
    {
        $host     = '127.0.0.1';
        $port     = '3306';
        $db       = 'rent_a_bike';
        $user     = 'rabike';
        $password = '123456';
        $charset  = 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $pdo = new PDO($dsn, $user, $password, $options);

        return $pdo;
    }
}
