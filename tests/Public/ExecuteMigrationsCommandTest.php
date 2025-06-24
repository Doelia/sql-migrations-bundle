<?php

namespace Public;

use Tests\TestKernel;

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

        $status = self::runCommand('sql-migrations:execute --drop-database');
        $this->assertEquals(0, $status);

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
