<?php

namespace Synapse\RestBundle\Entity;

interface DtoInterface
{
    /**
     * Assign entity properties using an array
     *
     * @param array $attributes assoc array of values to assign
     * @return null
     */
    public function fromArray(array $attributes);
}
