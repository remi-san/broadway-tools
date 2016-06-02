<?php

namespace Broadway\Tools\Test\Event;

use Broadway\Domain\DomainEventStreamInterface;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventHandling\EventListenerInterface;
use Broadway\Tools\Event\TransactionalEventBus;
use RemiSan\TransactionManager\TransactionManager;

class TransactionalEventBusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventBusInterface
     */
    private $eventBus;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    public function setUp()
    {
        $this->eventBus = \Mockery::mock(EventBusInterface::class);
        $this->transactionManager = \Mockery::mock(TransactionManager::class);
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @test
     */
    public function itShouldSubscribeInTheInnerBus()
    {
        $listener = \Mockery::mock(EventListenerInterface::class);

        $this->eventBus->shouldReceive('subscribe')->with($listener)->once();

        $eventBus = new TransactionalEventBus($this->eventBus, $this->transactionManager);
        $eventBus->subscribe($listener);
    }

    /**
     * @test
     */
    public function itShouldCommitAfterPublishing()
    {
        $stream = \Mockery::mock(DomainEventStreamInterface::class);

        $this->transactionManager->shouldReceive('beginTransaction')->once();
        $this->transactionManager->shouldReceive('commit')->once();

        $this->eventBus->shouldReceive('publish')->with($stream)->once();

        $eventBus = new TransactionalEventBus($this->eventBus, $this->transactionManager);
        $eventBus->publish($stream);
    }

    /**
     * @test
     */
    public function itShouldRollbackAfterPublishingFailed()
    {
        $stream = \Mockery::mock(DomainEventStreamInterface::class);

        $this->transactionManager->shouldReceive('beginTransaction')->once();
        $this->transactionManager->shouldReceive('rollback')->once();

        $this->eventBus->shouldReceive('publish')->with($stream)->andThrow(\Exception::class);

        $this->setExpectedException(\Exception::class);

        $eventBus = new TransactionalEventBus($this->eventBus, $this->transactionManager);
        $eventBus->publish($stream);
    }
}
