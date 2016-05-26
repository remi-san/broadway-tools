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
     * @return array
     * @throws SerializationException
     */
    public function serialize($object)
    {
        return $this->serializer->serialize($object);
    }

    /**
     * @param array $serializedObject
     * @return mixed
     * @throws SerializationException
     */
    public function deserialize(array $serializedObject)
    {
        return $this->serializer->deserialize($serializedObject);
    }
}
