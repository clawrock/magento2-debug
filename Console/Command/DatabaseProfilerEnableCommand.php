<?php
declare(strict_types=1);

namespace ClawRock\Debug\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseProfilerEnableCommand extends Command
{
    private \ClawRock\Debug\Model\Config\Database\ProfilerWriter $profilerWriter;

    public function __construct(
        \ClawRock\Debug\Model\Config\Database\ProfilerWriter $profilerWriter
    ) {
        parent::__construct('debug:db-profiler:enable');

        $this->profilerWriter = $profilerWriter;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Enable database profiler required for database collector.');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->profilerWriter->save(true);

        $output->writeLn('<info>Database profiler enabled!</info>');

        return Cli::RETURN_SUCCESS;
    }
}
