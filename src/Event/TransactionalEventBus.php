<?php

namespace Broadway\Tools\Event;

use Broadway\Domain\DomainEventStream;
use Broadway\EventHandling\EventBus;
use Broadway\EventHandling\EventListener;
use RemiSan\TransactionManager\TransactionManager;

class TransactionalEventBus implements EventBus
{
    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @param EventBus           $eventBus
     * @param TransactionManager $transactionManager
     */
    public function __construct(EventBus $eventBus, TransactionManager $transactionManager)
    {
        $this->eventBus = $eventBus;
        $this->transactionManager = $transactionManager;
    }

    /**
     * Subscribes the event listener to the event bus.
     *
     * @param EventListener $eventListener
     */
    public function subscribe(EventListener $eventListener)
    {
        $this->eventBus->subscribe($eventListener);
    }

    /**
     * Publishes the events from the domain event stream to the listeners.
     *
     * @param DomainEventStream $domainMessages
     * @throws \Exception
     */
    public function publish(DomainEventStream $domainMessages)
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
