<?php

namespace Broadway\Tools\Test\Event;

use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\Tools\Event\EventPublishingListener;
use League\Event\Emitter;
use League\Event\EventInterface;
use Mockery\Mock;

class EventPublishingListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Emitter | Mock
     */
    private $emitter;

    /**
     * @var EventInterface | Mock
     */
    private $event;

    /**
     * @var DomainMessage
     */
    private $message;

    /**
     * @var EventPublishingListener
     */
    private $eventPublishingListener;

    public function setUp()
    {
        $this->emitter = \Mockery::spy(Emitter::class);
        $this->event = \Mockery::mock(EventInterface::class);
        $this->message = DomainMessage::recordNow(0, 0, new Metadata(), $this->event);

        $this->eventPublishingListener = new EventPublishingListener($this->emitter);
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @test
     */
    public function itShouldPublishTheEvent()
    {
        $this->eventPublishingListener->handle($this->message);

        $this->emitter->shouldHaveReceived('emit')->with($this->event);
    }
}
