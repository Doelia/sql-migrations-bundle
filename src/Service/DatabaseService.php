<?php

namespace SWouters\SqlMigrationsBundle\Service;

use Doctrine\DBAL\Connection;

readonly class DatabaseService
{

    public function __construct(
        private Connection $db,
        private string     $migration_table
    ) { }

    public function resetDatabase(): void
    {
        $this->db->executeQuery("DROP SCHEMA IF EXISTS public CASCADE;");
        $this->db->executeQuery("CREATE SCHEMA public;");
        $this->createMigrationTableIfNotExists();
    }

    public function createMigrationTableIfNotExists(): void
    {
        $table = $this->migration_table;

        $this->db->executeQuery("
            create table if not exists $table (
                file varchar not null primary key,
                executed_at timestamptz not null default NOW(),
                checksum varchar not null
            )
        ");
    }

    /**
     * @param string $filename the path to the file to execute
     */
    public function executeSqlFile(string $filename): void
    {
        $sql = file_get_contents($filename);
        $this->db->executeStatement($sql);
    }

    /**
     * @param string $filename the path to the file to check
     */
    public function markAsExecuted(string $filename): void
    {
        $table = $this->migration_table;

        $this->db->executeQuery("insert into $table (file, checksum) values (?, ?)", [
            basename($filename),
            md5_file($filename)
        ]);
    }

    /**
     * @param string $filename the path to the file to check
     * @return string the checksum of the file if it is already executed, false otherwise
     */
    public function alreadyExecuted(string $filename): string
    {
        $table = $this->migration_table;

        return $this->db
            ->executeQuery("select checksum from $table where file=?", [basename($filename)])
            ->fetchOne();
    }

    /**
     * @param array $files_available the full list of migrations files
     * @param $skipIntegrityCheck bool if true, the integrity check will be skipped
     * @return array the list of files not already executed
     */
    public function filterFilesToProcess(array $files_available, $skipIntegrityCheck = false): array
    {
        $files_to_execute = [];


        foreach ($files_available as $file) {

            $checksum_executed = $this->alreadyExecuted($file);

            if ($checksum_executed) {
                if (!$skipIntegrityCheck && $checksum_executed != md5_file($file)) {
                    throw new \Exception("The file $file have changed before last execution. 
                    Check the file then retry. Add --skip-integrity-check to force execution.");
                }
            } else {
                $files_to_execute[] = $file;
            }
        }

        return $files_to_execute;
    }

    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

}
