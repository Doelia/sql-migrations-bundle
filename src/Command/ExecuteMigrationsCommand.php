<?php

namespace SWouters\SqlMigrationsBundle\Command;

use SWouters\SqlMigrationsBundle\Service\DatabaseService;
use SWouters\SqlMigrationsBundle\Service\MigrationsFilesService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'sql-migrations:execute')]
class ExecuteMigrationsCommand extends Command
{

    public function __construct(
        private readonly DatabaseService $db,
        private readonly MigrationsFilesService $files,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Execute all sql migrations');

        $this->addOption(
            'skip-integrity-check',
            null,
            InputOption::VALUE_NONE,
            'Dont check if already executed files have changed'
        );

        $this->addOption(
            'drop-database',
            null,
            InputOption::VALUE_NONE,
            'Drop the database before executing migrations'
        );

        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not execute anything, just show the files to be executed');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        if ($input->getOption('drop-database') && !$input->getOption('dry-run')) {
            $output->writeln("drop database...");
            $this->db->resetDatabase();
        }

        $this->db->createMigrationTableIfNotExists();

        $files_available = $this->files->getFileList();
        $files_to_process = $this->db->filterFilesToProcess($files_available, $input->getOption('skip-integrity-check'));

        if (!count($files_to_process)) {
            $output->writeln("Already up to date.");
            return 0;
        }

        if ($input->getOption('dry-run')) {
            $output->writeln("Files to process :");
            $output->writeln($files_to_process);
            $output->writeln("remove --dry-run to execute");
            return Command::SUCCESS;
        }

        $this->db->beginTransaction();

        foreach ($files_to_process as $file) {
            $output->writeln("execute $file...");
            $this->db->executeSqlFile($file);
            $this->db->markAsExecuted($file);
        }

        $this->db->commit();

        return Command::SUCCESS;
    }


}
