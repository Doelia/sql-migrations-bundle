<?php

namespace Tests\Public;

use Tests\TestKernel;

class MarkAppliedCommandTest extends TestKernel
{

    public function testExecuteAll()
    {
        $this->bootKernel();

        $this->getDatabaseService()->resetDatabase();

        $status = self::runCommand('sql-migrations:mark-applied --all');
        $this->assertEquals(0, $status);

        $dbal = $this->getDbal();

        $countExecuted = $dbal->executeQuery("SELECT count(*) from _migrations")->fetchOne();
        $this->assertEquals(2, $countExecuted);

    }

    public function testExecuteOne()
    {
        $this->bootKernel();

        $this->getDatabaseService()->resetDatabase();

        $status = self::runCommand('sql-migrations:mark-applied ./tests/mokes/02_fixtures.sql');
        $this->assertEquals(0, $status);

        $dbal = $this->getDbal();

        $countExecuted = $dbal->executeQuery("SELECT count(*) from _migrations")->fetchOne();
        $this->assertEquals(1, $countExecuted);
    }


}
