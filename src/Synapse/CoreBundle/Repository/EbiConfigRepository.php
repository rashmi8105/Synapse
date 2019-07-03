<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\EbiConfig;
use Synapse\CoreBundle\Exception\SynapseException;

class EbiConfigRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:EbiConfig';

    /**
     * @param array $criteria
     * @param SynapseException|null $exception
     * @param array|null $orderBy
     * @return EbiConfig|null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $ebiConfigEntity = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($ebiConfigEntity, $exception);
    }
}