<?php

namespace App;

use PHPUnit\Framework\TestCase;
use SWouters\SqlMigrationsBundle\Service\MigrationsFilesService;

class MigrationsFilesServiceTest extends TestCase
{
    public function testListFiles()
    {
        $filesServices = new MigrationsFilesService(__DIR__ . '/mokes');
        $files = $filesServices->getFileList();
        $this->assertNotEmpty($files);
        $this->assertTrue(file_exists($files[0]));
    }

    public function testChecksum()
    {
        $file = __DIR__ . '/mokes/01_init.sql';
        $this->assertEquals('38bf568512c1e97c1449da7c4503b281', md5_file($file));

    }

}
