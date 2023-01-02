<?php
declare(strict_types=1);

namespace ClawRock\Debug\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseProfilerDisableCommand extends Command
{
    private \ClawRock\Debug\Model\Config\Database\ProfilerWriter $profilerWriter;

    public function __construct(
        \ClawRock\Debug\Model\Config\Database\ProfilerWriter $profilerWriter
    ) {
        parent::__construct('debug:db-profiler:disable');

        $this->profilerWriter = $profilerWriter;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Disable database profiler required for database collector.');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->profilerWriter->save(false);

        $output->writeLn('<info>Database profiler disabled!</info>');

        return Cli::RETURN_SUCCESS;
    }
}
