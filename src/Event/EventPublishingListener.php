<?php

namespace Broadway\Tools\Event;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListenerInterface;
use League\Event\Emitter;

class EventPublishingListener implements EventListenerInterface
{
    /** @var Emitter */
    private $emitter;

    /**
     * EventPublishingListener constructor.
     *
     * @param Emitter $emitter
     */
    public function __construct(Emitter $emitter)
    {
        $this->emitter = $emitter;
    }

    /**
     * @inheritDoc
     */
    public function handle(DomainMessage $domainMessage)
    {
        $this->emitter->emit($domainMessage->getPayload());
    }
}
