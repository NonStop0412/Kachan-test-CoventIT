<?php

namespace app;

use PDO;
use RuntimeException;

// Class for working with MySql database
class MysqlDatabase implements DatabaseInterface
{
    private ?PDO $connection = null;
    private static ?MysqlDatabase $instance = null;

    private function __construct()
    {
        $this->connection = new PDO(
            'mysql:host=' . Config::getInst()->getHost() . ';dbname=' . Config::getInst()->getDbName(),
            Config::getInst()->getUser(),
            Config::getInst()->getPassword(),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    // Getting an existing instance instead of creating a new one
    public static function getInst(): MysqlDatabase
    {
        if (self::$instance) {
            return self::$instance;
        }

        return self::$instance = new self();
    }

    // Selection method for selection requests
    public function select(string $query, array $values): array
    {
        $stm = $this->connection->prepare($query);

        foreach ($values as $key => $value) {
            $stm->bindValue($key, $value);
        }

        if (!$stm->execute()) {
            throw new RuntimeException($stm->errorInfo());
        }

        $data = $stm->fetchAll(\PDO::FETCH_ASSOC);

        return $data ?: [];
    }

    // Selection method for select requests for one entity
    public function selectOne(string $query, array $values): array|false
    {
        $data = $this->select($query, $values);

        return current($data);
    }

    // Execute method for else requests
    public function execute(string $query, array $values): bool
    {
        $statement = $this->connection->prepare($query);

        $result = false;

        if (!$result = $statement->execute($values)) {
            throw new RuntimeException('Can\'t proceed insert!' . $query);
        }

        return $result;
    }

    // Getting the last inserted id (for generation)
    public function getLastInsertId(): false|string
    {
        return $this->connection->lastInsertId();
    }

}