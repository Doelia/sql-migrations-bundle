<?php

namespace Tests;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use SWouters\SqlMigrationsBundle\Private\DatabaseService;
use SWouters\SqlMigrationsBundle\Private\MigrationsFilesService;
use SWouters\SqlMigrationsBundle\Private\MigrationTableService;
use SWouters\SqlMigrationsBundle\Public\SqlMigrationsBundleService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\App\AppKernel;

abstract class TestKernel extends TestCase
{

    private ContainerInterface $container;

    public function bootKernel():  void
    {
        $kernel = new AppKernel('test', true);
        $kernel->boot();

        $this->container = $kernel->getContainer();
    }

    public static function runCommand($command)
    {
        $kernel = new AppKernel('test', true);
        $kernel->boot();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $command = sprintf('%s --quiet', $command);

        $input = new StringInput($command);
        $input->setInteractive(false);

        $output = new BufferedOutput();

        return $application->run($input, $output);
    }

    public function getMigrationTableService(): MigrationTableService
    {
        return $this->container->get('test.' . MigrationTableService::class);
    }

    public function getDatabaseService(): DatabaseService
    {
        return $this->container->get('test.' . DatabaseService::class);
    }

    public function getFilesService(): MigrationsFilesService
    {
        return $this->container->get('test.' . MigrationsFilesService::class);
    }

    public function getDbal(): Connection
    {
        return $this->container->get('doctrine.dbal.default_connection');
    }

    public function getSqlMigrationsBundleService(): SqlMigrationsBundleService
    {
        return $this->container->get(SqlMigrationsBundleService::class);
    }

}
