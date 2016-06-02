<?php

namespace Broadway\Tools\Event;

use Broadway\Domain\DomainEventStreamInterface;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventHandling\EventListenerInterface;
use RemiSan\TransactionManager\TransactionManager;

class TransactionalEventBus implements EventBusInterface
{
    /**
     * @var EventBusInterface
     */
    private $eventBus;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @param EventBusInterface  $eventBus
     * @param TransactionManager $transactionManager
     */
    public function __construct(EventBusInterface $eventBus, TransactionManager $transactionManager)
    {
        $this->eventBus = $eventBus;
        $this->transactionManager = $transactionManager;
    }

    /**
     * Subscribes the event listener to the event bus.
     *
     * @param EventListenerInterface $eventListener
     */
    public function subscribe(EventListenerInterface $eventListener)
    {
        $this->eventBus->subscribe($eventListener);
    }

    /**
     * Publishes the events from the domain event stream to the listeners.
     *
     * @param DomainEventStreamInterface $domainMessages
     * @throws \Exception
     */
    public function publish(DomainEventStreamInterface $domainMessages)
    {
        $this->transactionManager->beginTransaction();
        try {
            $this->eventBus->publish($domainMessages);
            $this->transactionManager->commit();
        } catch (\Exception $e) {
            $this->transactionManager->rollback();
            throw $e;
        }
    }
}
