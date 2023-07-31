<?php

namespace app;

/**
 * Interface for databases classes
 */
interface DatabaseInterface
{
    /**
     * Selection method for selection requests
     * @param string $query
     * @param array $values
     * @return array
     */
    public function select(string $query, array $values): array;

    /**
     * Selection method for select requests for one entity
     * @param string $query
     * @param array $values
     * @return array|false
     */
    public function selectOne(string $query, array $values): array|false;

    /**
     * Execute method for else requests
     * @param string $query
     * @param array $values
     * @return bool
     */
    public function execute(string $query, array $values): bool;
}