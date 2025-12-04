<?php

namespace Tests\Public;

use Tests\TestKernel;

class SqlMigrationsBundleServiceTest extends TestKernel
{


    public function testResetDatabase()
    {
        $this->bootKernel();
        $service = $this->getSqlMigrationsBundleService();
        $dbal = $this->getDbal();

        $service->resetDatabase();

        $tableExists = $dbal->executeQuery("SELECT to_regclass('public.users')")->fetchOne();
        $this->assertNull($tableExists);
    }

    public function testExecute()
    {
        $this->bootKernel();
        $service = $this->getSqlMigrationsBundleService();
        $dbal = $this->getDbal();

        $service->resetDatabase();
        $service->executeMigrations();

        $tableExists = $dbal->executeQuery("SELECT to_regclass('public.users')")->fetchOne();
        $this->assertEquals('users', $tableExists);
    }

    public function testIsUpToDate()
    {
        $this->bootKernel();
        $service = $this->getSqlMigrationsBundleService();

        $service->resetDatabase();
        $service->executeMigrations();

        $isUpToDate = $service->isUpToDate();
        $this->assertTrue($isUpToDate);
    }

    public function testIsNotUpToDate()
    {
        $this->bootKernel();
        $service = $this->getSqlMigrationsBundleService();

        $service->resetDatabase();

        $isUpToDate = $service->isUpToDate();
        $this->assertFalse($isUpToDate);
    }

}
