<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class AccessLevelDto implements DtoInterface
{

    /**
     * Boolean determining whether a permission set has both individual and aggregate access.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $individualAndAggregate;

    /**
     * If True, then the permission set has only aggregate access.
     * 
     * @var boolean @JMS\Type("boolean")
     */
    private $aggregateOnly;

    /**
     *
     * @return boolean
     */
    public function getAggregateOnly()
    {
        return $this->aggregateOnly;
    }

    /**
     *
     * @param boolean $aggregateOnly
     */
    public function setAggregateOnly($aggregateOnly)
    {
        $this->aggregateOnly = $aggregateOnly;
    }

    /**
     *
     * @return boolean
     */
    public function getIndividualAndAggregate()
    {
        return $this->individualAndAggregate;
    }

    /**
     *
     * @param boolean $individualAndAggregate
     */
    public function setIndividualAndAggregate($individualAndAggregate)
    {
        $this->individualAndAggregate = $individualAndAggregate;
    }

    /**
     * Assign entity properties using an array
     *
     * @param array $attributes assoc array of values to assign
     * @return null
     */
    public function fromArray(array $attributes)
    {
        // Build from the org_permissionset table.
        $attributesLocal = $attributes;
        if (array_key_exists('accesslevel_ind_agg', $attributes))
        {
            // Avoid cyclic memleak.
            unset($attributesLocal);
            $attributesLocal = [
                'individualAndAggregate' => $attributes['accesslevel_ind_agg'],
                'aggregateOnly' => $attributes['accesslevel_agg']
            ];
        }

        $this->individualAndAggregate = (isset($attributesLocal['individualAndAggregate'])) ? (bool)$attributesLocal['individualAndAggregate'] : false;
        $this->aggregateOnly = (isset($attributesLocal['aggregateOnly'])) ? (bool)$attributesLocal['aggregateOnly'] : false;

        // Avoid cyclic memleak.
        unset($attributesLocal);
    }
}
