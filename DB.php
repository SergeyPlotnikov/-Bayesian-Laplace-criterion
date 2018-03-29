<?php

/**
 * Created by PhpStorm.
 * User: Serhii
 * Date: 29.03.2018
 * Time: 17:04
 */
class DB
{
    private $pdo;
    private const HOST = 'localhost';
    private const DBNAME = 'bayes-laplas';
    private const USER = 'root';
    private const PASSSWORD = '21091992';

    function __construct()
    {
        $this->pdo = new PDO('mysql:host=' . self::HOST . ';dbname=' . self::DBNAME . '', self::USER, self::PASSSWORD);
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}