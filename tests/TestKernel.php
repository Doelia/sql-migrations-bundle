<?php

namespace Tests;

use SWouters\SqlMigrationsBundle\Command\ExecuteMigrationsCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\App\AppKernel;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use SWouters\SqlMigrationsBundle\Service\DatabaseService;
use SWouters\SqlMigrationsBundle\Service\MigrationsFilesService;
use Symfony\Bundle\FrameworkBundle\Console\Application;

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

}
