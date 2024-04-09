<?php

namespace Tests;

use Symfony\Component\Console\Tester\CommandTester;

class ExecuteMigrationsCommandTest extends TestKernel
{

    public function testExecuteDryRun()
    {
        $this->bootKernel();

        $this->getDatabaseService()->resetDatabase();

        self::runCommand('sql-migrations:execute --dry-run');

        $dbal = $this->getDbal();

        $tableExists = $dbal->executeQuery("SELECT to_regclass('public.users')")->fetchOne();

        $this->assertEquals(null, $tableExists);

    }

    public function testExecute()
    {
        $this->bootKernel();

        $this->getDatabaseService()->resetDatabase();

        self::runCommand('sql-migrations:execute');

        $dbal = $this->getDbal();

        $tableExists = $dbal->executeQuery("SELECT to_regclass('public.users')")->fetchOne();
        $this->assertEquals('users', $tableExists);

    }

    public function testExecuteUpToDate()
    {
        $this->bootKernel();

        $this->getDatabaseService()->resetDatabase();

        self::runCommand('sql-migrations:execute');
        $dbal = $this->getDbal();

        $tableExists = $dbal->executeQuery("SELECT to_regclass('public.users')")->fetchOne();
        $this->assertEquals('users', $tableExists);
    }


}
