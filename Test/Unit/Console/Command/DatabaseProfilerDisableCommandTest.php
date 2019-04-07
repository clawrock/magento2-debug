<?php

namespace ClawRock\Debug\Test\Unit\Console\Command;

use ClawRock\Debug\Console\Command\DatabaseProfilerDisableCommand;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \ClawRock\Debug\Console\Command\DatabaseProfilerDisableCommand
 */
class DatabaseProfilerDisableCommandTest extends TestCase
{
    /**
     * @var \ClawRock\Debug\Model\Config\Database\ProfilerWriter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $profilerWriterMock;

    /**
     * @var \Symfony\Component\Console\Application
     */
    private $application;

    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    private $commandTester;

    /**
     * @var \Symfony\Component\Console\Command\Command
     */
    private $command;

    /**
     * @var \ClawRock\Debug\Console\Command\DatabaseProfilerDisableCommand
     */
    private $commandObject;

    protected function setUp()
    {
        parent::setUp();

        $this->profilerWriterMock = $this->getMockBuilder(\ClawRock\Debug\Model\Config\Database\ProfilerWriter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->application = new Application();
        $this->commandObject = (new ObjectManager($this))->getObject(DatabaseProfilerDisableCommand::class, [
            'profilerWriter' => $this->profilerWriterMock,
        ]);

        $this->application->add($this->commandObject);
        $this->command = $this->application->find('debug:db-profiler:disable');
        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecute()
    {
        $this->profilerWriterMock->expects($this->once())->method('save')->with(false);
        $this->commandTester->execute(['command' => $this->command->getName()]);
    }
}
