<?php

namespace Broadway\Tools\Metadata\Context;

use Broadway\Domain\Metadata;
use Broadway\EventSourcing\MetadataEnrichment\MetadataEnricher;
use RemiSan\Context\ContextContainer;

class ContextEnricher implements MetadataEnricher
{
    const CONTEXT = 'context';

    /**
     * Add the context info to the Metadata
     *
     * @param  Metadata $metadata
     *
     * @return Metadata
     */
    public function enrich(Metadata $metadata)
    {
        return $metadata->merge(new Metadata([static::CONTEXT => ContextContainer::getContext()]));
    }
}
