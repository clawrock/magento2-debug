<?php
declare(strict_types=1);

namespace ClawRock\Debug\Test\Unit\Console\Command;

use ClawRock\Debug\Console\Command\DatabaseProfilerEnableCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \ClawRock\Debug\Console\Command\DatabaseProfilerEnableCommand
 */
class DatabaseProfilerEnableCommandTest extends TestCase
{
    /** @var \ClawRock\Debug\Model\Config\Database\ProfilerWriter&\PHPUnit\Framework\MockObject\MockObject */
    private \ClawRock\Debug\Model\Config\Database\ProfilerWriter $profilerWriterMock;
    private \Symfony\Component\Console\Application $application;
    private \Symfony\Component\Console\Tester\CommandTester $commandTester;
    private \Symfony\Component\Console\Command\Command $command;
    private \ClawRock\Debug\Console\Command\DatabaseProfilerEnableCommand $commandObject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->profilerWriterMock = $this->getMockBuilder(\ClawRock\Debug\Model\Config\Database\ProfilerWriter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->application = new Application();
        $this->commandObject = new DatabaseProfilerEnableCommand($this->profilerWriterMock);
        $this->application->add($this->commandObject);
        $this->command = $this->application->find('debug:db-profiler:enable');
        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecute(): void
    {
        $this->profilerWriterMock->expects($this->once())->method('save')->with(true);
        $this->commandTester->execute(['command' => $this->command->getName()]);
    }
}
