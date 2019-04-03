<?php

namespace ClawRock\Debug\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseProfilerEnableCommand extends Command
{
    /**
     * @var \ClawRock\Debug\Model\Config\Database\ProfilerWriter
     */
    private $profilerWriter;

    public function __construct(
        \ClawRock\Debug\Model\Config\Database\ProfilerWriter $profilerWriter
    ) {
        parent::__construct('debug:db-profiler:enable');

        $this->profilerWriter = $profilerWriter;
    }

    protected function configure()
    {
        parent::configure();

        $this->setDescription('Enable database profiler required for database collector.');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->profilerWriter->save(true);

        $output->writeLn('<info>Database profiler enabled!</info>');
    }
}
