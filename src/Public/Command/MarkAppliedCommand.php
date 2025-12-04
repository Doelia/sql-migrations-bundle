<?php

namespace SWouters\SqlMigrationsBundle\Public\Command;

use SWouters\SqlMigrationsBundle\Private\DatabaseService;
use SWouters\SqlMigrationsBundle\Private\MigrationsFilesService;
use SWouters\SqlMigrationsBundle\Private\MigrationTableService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'sql-migrations:mark-applied')]
class MarkAppliedCommand extends Command
{

    public function __construct(
        private readonly DatabaseService  $db,
        private readonly MigrationTableService $migrationTable,
        private readonly MigrationsFilesService $files,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Mark a migration as applied without executing it');

        $this->addArgument(
            'migration_filename',
            null,
            'The filename of the migration to mark as applied'
        );

        $this->addOption(
            'all',
            null,
            InputOption::VALUE_NONE,
            'Mark all migrations as applied',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->db->beginTransaction();

        $this->migrationTable->createMigrationTableIfNotExists();

        $files_available = $this->files->getFileList();

        if ($input->getOption('all') && $input->getArgument('migration_filename')) {
            $output->writeln("You cannot use both the --all option and the migration_filename argument at the same time.");
            return Command::FAILURE;
        }

        if (!$input->getOption('all') && !$input->getArgument('migration_filename')) {
            $output->writeln("You must provide either the --all option or the migration_filename argument.");
            return Command::FAILURE;
        }

        if ($input->getOption('all')) {
            $output->writeln("drop migration table...");
            $this->migrationTable->truncateMigrationTable();

            foreach ($files_available as $file) {
                $output->writeln("mark $file as executed (without executing)...");
                $this->migrationTable->markAsExecuted($file);
            }
        }

        if ($input->getArgument('migration_filename')) {
            $filename = $input->getArgument('migration_filename');

            $filenames_available = array_map(fn($f) => basename($f), $files_available);

            if (!in_array(basename($filename), $filenames_available)) {
                $output->writeln("The migration file $filename does not exist in the migrations folder.");
                return Command::FAILURE;
            }

            $output->writeln("mark $filename as executed (without executing)...");
            $this->migrationTable->markAsExecuted($filename);
        }

        $this->db->commit();

        return Command::SUCCESS;
    }


}
