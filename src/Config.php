<?php

namespace app;

/**
 * Class for working with configs
 */
class Config
{
    private static $instance;
    private array $config;

    /**
     * Construct to include config
     * @param array $config
     */
    private function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Getting an existing instance instead of creating a new one
     * @return self
     */
    public static function getInst(): self
    {
        if (self::$instance instanceof Config) {
            return self::$instance;
        }

        return self::$instance = new Config(include(getcwd() . '/config.php'));
    }

    /**
     * Getting host from config.php
     * @return string
     */
    public function getHost(): string
    {
        return $this->config['host'] ?? '';
    }

    /**
     * Getting dbname from config.php
     * @return string
     */
    public function getDbName(): string
    {
        return $this->config['dbname'] ?? '';
    }

    /**
     * Getting db_username from config.php
     * @return string
     */
    public function getUser(): string
    {
        return $this->config['db_username'] ?? '';
    }

    /**
     * Getting db_password from config.php
     * @return string
     */
    public function getPassword(): string
    {
        return $this->config['db_password'] ?? '';
    }
}