<?php

namespace Broadway\Tools\Test\Metadata\Serializer;

use Broadway\Tools\Metadata\Serializer\RecursiveSerializer;
use RemiSan\Serializer\Serializer;

class RecursiveSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function setUp()
    {
        $this->serializer = \Mockery::mock(Serializer::class);
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @test
     */
    public function itShouldDeferSerializationToInnerSerializer()
    {
        $return = [ 'test' ];
        $object = [ 'original' ];

        $this->serializer->shouldReceive('serialize')->with($object)->andReturn($return)->once();

        $serializer = new RecursiveSerializer($this->serializer);

        $actualReturn = $serializer->serialize($object);

        $this->assertEquals($return, $actualReturn);
    }

    /**
     * @test
     */
    public function itShouldDeferDeserializationToInnerSerializer()
    {
        $serialized = [ 'test' ];
        $return = [ 'original' ];

        $this->serializer->shouldReceive('deserialize')->with($serialized)->andReturn($return)->once();

        $serializer = new RecursiveSerializer($this->serializer);

        $actualReturn = $serializer->deserialize($serialized);

        $this->assertEquals($return, $actualReturn);
    }
}
