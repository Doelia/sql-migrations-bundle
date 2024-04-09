<?php

namespace SWouters\SqlMigrationsBundle\Service;

use Doctrine\DBAL\Connection;

readonly class MigrationsFilesService
{
    public function __construct(
        private string     $folder
    ) { }

    public function getFileList()
    {
        $files_available = glob($this->folder . '/*.sql');

        usort($files_available, function ($a, $b) {
            $order_a = (int)explode('_', basename($a))[0];
            $order_b = (int)explode('_', basename($b))[0];

            return $order_a <=> $order_b;
        });

        foreach ($files_available as $file) {
            if (!preg_match('/\d+_\w+\.sql/', basename($file))) {
                throw new \Exception(
                    "The file " .
                    basename($file) .
                    " is incorrectly named, it must start with a number followed by an underscore."
                );
            }
        }

        return $files_available;
    }

}
