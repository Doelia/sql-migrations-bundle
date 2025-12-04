<?php

namespace Tests\Private;

use Tests\TestKernel;

class MigrationTableServiceTest extends TestKernel
{

    public function testMarkAsExecuted()
    {
        $this->bootKernel();
        $databaseService = $this->getDatabaseService();
        $migrationTable = $this->getMigrationTableService();
        $dbal = $this->getDbal();

        $databaseService->resetDatabase();
        $migrationTable->createMigrationTableIfNotExists();

        $md5 = $dbal->executeQuery("SELECT checksum from _migrations order by executed_at desc")->fetchOne();
        $this->assertFalse($md5);

        $files = $this->getFilesService()->getFileList();
        foreach ($files as $file) {
            $migrationTable->markAsExecuted($file);
        }

        $md5 = $dbal->executeQuery("SELECT checksum from _migrations order by executed_at desc")->fetchOne();
        $this->assertEquals('5a697ab66486ecc3d1d51ab8560e321f', $md5);

    }

    public function testTruncateMigrationTable()
    {
        $this->bootKernel();
        $databaseService = $this->getDatabaseService();
        $migrationTable = $this->getMigrationTableService();
        $dbal = $this->getDbal();

        $databaseService->resetDatabase();
        $migrationTable->createMigrationTableIfNotExists();

        $files = $this->getFilesService()->getFileList();
        foreach ($files as $file) {
            $migrationTable->markAsExecuted($file);
        }

        $count = $dbal->executeQuery("SELECT count(*) from _migrations")->fetchOne();
        $this->assertEquals(count($files), $count);

        $migrationTable->truncateMigrationTable();

        $count = $dbal->executeQuery("SELECT count(*) from _migrations")->fetchOne();
        $this->assertEquals(0, $count);
    }

    public function testFilterFilesToProcess()
    {
        $this->bootKernel();
        $migrationTable = $this->getMigrationTableService();
        $databaseService = $this->getDatabaseService();

        $databaseService->resetDatabase();
        $migrationTable->createMigrationTableIfNotExists();

        $files = $this->getFilesService()->getFileList();
        $filesToProcess = $migrationTable->filterFilesToProcess($files);

        $this->assertGreaterThanOrEqual(2, count($filesToProcess));

        foreach ($files as $file) {
            $migrationTable->markAsExecuted($file);
        }

        $files = $this->getFilesService()->getFileList();
        $filesToProcess = $migrationTable->filterFilesToProcess($files);

        $this->assertEquals(0, count($filesToProcess));

    }



}
