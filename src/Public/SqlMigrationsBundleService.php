<?php

namespace SWouters\SqlMigrationsBundle\Public;

use SWouters\SqlMigrationsBundle\Private\DatabaseService;
use SWouters\SqlMigrationsBundle\Private\MigrationTableService;
use SWouters\SqlMigrationsBundle\Private\MigrationsFilesService;

readonly class SqlMigrationsBundleService
{

    public function __construct(
        private MigrationTableService  $migrationTable,
        private MigrationsFilesService $files,
        private DatabaseService        $db,
    ) {
    }

    public function isUpToDate(): bool
    {
        $this->migrationTable->createMigrationTableIfNotExists();

        $files_available = $this->files->getFileList();
        $files_to_process = $this->migrationTable->filterFilesToProcess($files_available);

        if (count($files_to_process) === 0) {
            return true;
        }

        return false;
    }

    public function resetDatabase(): void
    {
        $this->db->resetDatabase();
        $this->migrationTable->createMigrationTableIfNotExists();
    }

    public function executeMigrations(bool $skipIntegrityCheck = false): array
    {
        $this->migrationTable->createMigrationTableIfNotExists();

        $files_available = $this->files->getFileList();
        $files_to_process = $this->migrationTable->filterFilesToProcess($files_available, $skipIntegrityCheck);

        $executed = [];

        foreach ($files_to_process as $file) {
            $this->db->executeSqlFile($file);
            $this->migrationTable->markAsExecuted($file);
            $executed[] = $file;
        }

        return $executed;
    }

}
