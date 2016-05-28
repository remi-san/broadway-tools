<?php

namespace Broadway\Tools\Test\Metadata\Context;

use Broadway\Domain\Metadata;
use Broadway\Tools\Metadata\Context\ContextEnricher;
use RemiSan\Context\Context;
use RemiSan\Context\ContextContainer;

class ContextEnricherTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
        ContextContainer::reset();
    }

    /**
     * @test
     */
    public function itShouldEnrichMetadataWithContext()
    {
        $context = \Mockery::mock(Context::class);
        ContextContainer::setContext($context);

        $metadata = \Mockery::mock(Metadata::class, function ($metadata) use ($context) {
            $metadata->shouldReceive('merge')->with(\Mockery::on(function ($m) use ($context) {
                $this->assertInstanceOf(Metadata::class, $m);
                $this->assertEquals([ 'context' => $context ], $m->serialize());
                return true;
            }))->andReturn($metadata)->once();
        });

        $enricher = new ContextEnricher();

        $return = $enricher->enrich($metadata);

        $this->assertEquals($metadata, $return);
    }
}
