<?php

namespace app;

interface DatabaseInterface
{
    public function select(string $query, array $values): array;

    public function selectOne(string $query, array $values): array|false;

    public function execute(string $query, array $values): bool;

}