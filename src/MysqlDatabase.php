<?php

namespace app;

use PDO;
use RuntimeException;

/**
 * Class for working with MySql database
 */
class MysqlDatabase implements DatabaseInterface
{
    private ?PDO $connection = null;
    private static ?MysqlDatabase $instance = null;

    /**
     * Construct for set up connection to db
     */
    private function __construct()
    {
        $this->connection = new PDO(
            'mysql:host=' . Config::getInst()->getHost() . ';dbname=' . Config::getInst()->getDbName(),
            Config::getInst()->getUser(),
            Config::getInst()->getPassword(),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    /**
     * Getting an existing instance instead of creating a new one
     * @return MysqlDatabase
     */
    public static function getInst(): MysqlDatabase
    {
        if (self::$instance) {
            return self::$instance;
        }

        return self::$instance = new self();
    }

    /**
     * Selection method for selection requests
     * @param string $query
     * @param array $values
     * @return array
     */
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

    /**
     * Selection method for select requests for one entity
     * @param string $query
     * @param array $values
     * @return array|false
     */
    public function selectOne(string $query, array $values): array|false
    {
        $data = $this->select($query, $values);

        return current($data);
    }

    /**
     * Execute method for else requests
     * @param string $query
     * @param array $values
     * @return bool
     */
    public function execute(string $query, array $values): bool
    {
        $statement = $this->connection->prepare($query);

        $result = false;

        if (!$result = $statement->execute($values)) {
            throw new RuntimeException('Can\'t proceed insert!' . $query);
        }

        return $result;
    }

    /**
     * Getting the last inserted id (for generation)
     * @return false|string
     */
    public function getLastInsertId(): false|string
    {
        return $this->connection->lastInsertId();
    }

}