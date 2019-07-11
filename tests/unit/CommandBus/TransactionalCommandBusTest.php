<?php

namespace Broadway\Tools\Test\CommandBus;

use Broadway\CommandHandling\CommandBus;
use Broadway\CommandHandling\CommandHandler;
use Broadway\Tools\Command\TransactionalCommandBus;
use Exception;
use Psr\Log\LoggerInterface;
use RemiSan\TransactionManager\TransactionManager;

class TransactionalCommandBusTest extends \PHPUnit_Framework_TestCase
{
    /** @var TransactionalCommandBus */
    private $sut;

    /** @var TransactionManager|\PHPUnit_Framework_MockObject_MockObject */
    private $transactionManager;

    /** @var CommandBus|\PHPUnit_Framework_MockObject_MockObject */
    private $simpleCommandBus;

    /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    protected function setUp()
    {
        $this->simpleCommandBus = $this->getMockBuilder(CommandBus::class)
            ->getMock();

        $this->transactionManager = $this->getMockBuilder(TransactionManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMock(LoggerInterface::class);

        $this->sut = new TransactionalCommandBus($this->simpleCommandBus, $this->transactionManager, $this->logger);
    }

    /** @test */
    public function itCommits()
    {
        $command = new \StdClass();

        $this->transactionManager->expects(self::once())
            ->method('beginTransaction');

        $this->transactionManager->expects(self::once())
            ->method('commit');

        $this->simpleCommandBus->expects(self::once())
            ->method('dispatch');

        $this->sut->dispatch($command);
    }

    /** @test */
    public function itSubscribes()
    {
        $handler = $this->getMockBuilder(CommandHandler::class)
            ->getMock();

        $this->simpleCommandBus->expects(self::once())
            ->method('subscribe')
            ->with($handler);

        $this->sut->subscribe($handler);
    }

    /** @test */
    public function itRollbacksOnException()
    {
        $this->setExpectedException(Exception::class);

        $command = new \StdClass();

        $this->transactionManager->expects(self::once())
            ->method('beginTransaction');

        $this->transactionManager->expects(self::once())
            ->method('rollback');

        $this->simpleCommandBus->expects(self::once())
            ->method('dispatch')
            ->willThrowException(new Exception());

        $this->sut->dispatch($command);
    }

    /** @test */
    public function itLogsExceptionWhenFailingToRollback()
    {
        $this->setExpectedException(Exception::class);

        $command = new \StdClass();

        $this->transactionManager->expects(self::once())
            ->method('beginTransaction');

        $this->simpleCommandBus->expects(self::once())
            ->method('dispatch')
            ->willThrowException(new Exception());

        $this->transactionManager->expects(self::once())
            ->method('rollback')
            ->willThrowException(new Exception());

        $this->logger->expects(self::once())
            ->method('critical');

        $this->sut->dispatch($command);
    }
}
