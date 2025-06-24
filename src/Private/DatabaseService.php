<?php

namespace SWouters\SqlMigrationsBundle\Private;

use Doctrine\DBAL\Connection;

readonly class DatabaseService
{

    public function __construct(
        private Connection $db,
    ) { }

    public function resetDatabase(): void
    {
        $this->db->executeQuery("DROP SCHEMA IF EXISTS public CASCADE;");
        $this->db->executeQuery("CREATE SCHEMA public;");
    }

    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollback(): void
    {
        $this->db->rollback();
    }

    /**
     * @param string $filename the path to the file to execute
     */
    public function executeSqlFile(string $filename): void
    {
        if (!file_exists($filename)) {
            throw new \Exception("The file $filename does not exist.");
        }

        $sql = file_get_contents($filename);
        $this->db->executeStatement($sql);
    }

}
