<?php

namespace Broadway\Tools\Metadata\Serializer;

use Broadway\Serializer\SerializationException;
use Broadway\Serializer\SerializerInterface;
use RemiSan\Serializer\Serializer;

class RecursiveSerializer implements SerializerInterface
{
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * Constructor.
     *
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param $object
     * @throws SerializationException
     * @return array
     */
    public function serialize($object)
    {
        return $this->serializer->serialize($object);
    }

    /**
     * @param array $serializedObject
     * @throws SerializationException
     * @return mixed
     */
    public function deserialize(array $serializedObject)
    {
        return $this->serializer->deserialize($serializedObject);
    }
}
