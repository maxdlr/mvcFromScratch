<?php

namespace App\Routing\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\ORMSetup;
use Exception;
use Symfony\Component\Dotenv\Dotenv;

class DatabaseManager
{
    public function getDatabaseParameters(): array
    {
        $dotenv = new Dotenv();
        try {
            $dotenv->loadEnv(__DIR__ . '/../../../.env');
        } catch (Exception $e) {
            var_dump($e);
        }

        return [
            'driver'   => $_ENV['DB_DRIVER'],
            'user'     => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'dbname'   => $_ENV['DB_NAME'],
            'host'     => $_ENV['DB_HOST'],
            'port'     => $_ENV['DB_PORT'],
        ];
    }

    public function setupOrm(): Configuration
    {
        $paths = [__DIR__ . '/../src/Entity'];
        $isDevMode = $_ENV['APP_ENV'] === 'dev';

        return ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
    }
    public function connect(): Connection
    {
        try {
            return DriverManager::getConnection(
                $this->getDatabaseParameters(),
                $this->setupOrm()
            );
        } catch (\Doctrine\DBAL\Exception $e) {
            var_dump($e);
            exit();
        }
    }

    public function getEntityManager(): EntityManager
    {
        try {
            return new EntityManager(
                $this->connect(),
                $this->setupOrm()
            );
        } catch (MissingMappingDriverImplementation $e) {
            var_dump($e);
            exit();
        }
    }
}