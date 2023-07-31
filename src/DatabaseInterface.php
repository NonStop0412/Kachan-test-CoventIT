<?php

namespace app;

/**
 * Interface for databases classes
 */
interface DatabaseInterface
{
    /**
     * Selection method for selection requests
     */
    public function select(string $query, array $values): array;

    /**
     * Selection method for select requests for one entity
     */
    public function selectOne(string $query, array $values): array|false;

    /**
     * Execute method for else requests
     */
    public function execute(string $query, array $values): bool;
}