<?php

namespace Tests\Private;

use Tests\TestKernel;

class DatabaseServiceTest extends TestKernel
{

    public function testResetDatabase()
    {
        $this->bootKernel();
        $databaseService = $this->getDatabaseService();
        $dbal = $this->getDbal();

        $databaseService->resetDatabase();

        $tableExists = $dbal->executeQuery("SELECT to_regclass('public.users')")->fetchOne();
        $this->assertNull($tableExists);
    }

    public function testExecuteFile()
    {
        $this->bootKernel();
        $dbal = $this->getDbal();

        // prepare
        $dbal->executeQuery("DROP TABLE IF EXISTS users");

        $databaseService = $this->getDatabaseService();
        $databaseService->executeSqlFile(__DIR__ . '/../mokes/01_init.sql');

        $tableExists = $dbal->executeQuery("SELECT to_regclass('public.users')")->fetchOne();
        $this->assertEquals('users', $tableExists);

    }

    public function testResetAfterInsert()
    {
        $this->bootKernel();
        $databaseService = $this->getDatabaseService();
        $dbal = $this->getDbal();

        $databaseService->resetDatabase();

        $databaseService->executeSqlFile(__DIR__ . '/../mokes/01_init.sql');

        $tableExists = $dbal->executeQuery("SELECT to_regclass('public.users')")->fetchOne();
        $this->assertEquals('users', $tableExists);

        $databaseService->resetDatabase();

        $tableExists = $dbal->executeQuery("SELECT to_regclass('public.users')")->fetchOne();
        $this->assertNull($tableExists);

    }


}
