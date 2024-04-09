<?php

namespace Tests;

class MainTest extends TestKernel
{

    public function testMigration(): void
    {
        $this->bootKernel();

        $databaseService = $this->getDatabaseService();
        $dbal = $this->getDbal();

        $databaseService->resetDatabase();

        $files = $this->getFilesService()->getFileList();
        foreach ($files as $file) {
            $databaseService->executeSqlFile($file);
        }

        $count = $dbal->executeQuery('SELECT * FROM users')->rowCount();
        $this->assertEquals(1, $count);

    }

    public function testResetDatabase()
    {
        $this->bootKernel();

        $databaseService = $this->getDatabaseService();
        $dbal = $this->getDbal();

        $databaseService->resetDatabase();

        $files = $this->getFilesService()->getFileList();

        foreach ($files as $file) {
            $databaseService->executeSqlFile($file);
        }

        $tableExists = $dbal->executeQuery("SELECT to_regclass('public.users')")->fetchOne();
        $this->assertEquals('users', $tableExists);

        $databaseService->resetDatabase();

        $tableExists = $dbal->executeQuery("SELECT to_regclass('public.users')")->fetchOne();
        $this->assertNull($tableExists);

    }

    public function testMarkAsExecuted()
    {
        $this->bootKernel();

        $databaseService = $this->getDatabaseService();
        $dbal = $this->getDbal();

        $databaseService->resetDatabase();

        $md5 = $dbal->executeQuery("SELECT checksum from _migrations order by executed_at desc")->fetchOne();
        $this->assertFalse($md5);

        $files = $this->getFilesService()->getFileList();
        foreach ($files as $file) {
            $databaseService->markAsExecuted($file);
        }

        $md5 = $dbal->executeQuery("SELECT checksum from _migrations order by executed_at desc")->fetchOne();
        $this->assertEquals('5a697ab66486ecc3d1d51ab8560e321f', $md5);


    }

    public function testFilterFilesToProcess()
    {
        $this->bootKernel();

        $databaseService = $this->getDatabaseService();

        $databaseService->resetDatabase();

        $files = $this->getFilesService()->getFileList();
        $filesToProcess = $databaseService->filterFilesToProcess($files);

        $this->greaterThanOrEqual(2, count($filesToProcess));

        foreach ($files as $file) {
            $databaseService->markAsExecuted($file);
        }

        $files = $this->getFilesService()->getFileList();
        $filesToProcess = $databaseService->filterFilesToProcess($files);

        $this->assertEquals(0, count($filesToProcess));

    }



}
