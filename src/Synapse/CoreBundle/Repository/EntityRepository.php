<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\RestBundle\Exception\ValidationException;
class EntityRepository extends SynapseRepository

{

    const REPOSITORY_KEY = 'SynapseCoreBundle:Entity';

    const ENTITY_NOT_FOUND = 'Entity Not Found.';
    
    /**
     * Getting Faculty Entity
     *
     * @return \Synapse\RestBundle\Entity\Error|\Synapse\CoreBundle\Entity\Entity
     */
    public function getUserTypeById($type)
    {
        $type = strtolower(trim($type));

        $typeId = 0;
        switch ($type) {
            case "staff":
                $typeId = 3;
                break;
            case "student":
                $typeId = 2;
                break;
            default:
                throw new ValidationException([
                self::ENTITY_NOT_FOUND
                        ], self::ENTITY_NOT_FOUND, 'entity_not_found');
                        break;
        }

        $userType = $this->getEntityManager()
        ->getRepository('SynapseCoreBundle:Entity')
        ->find($typeId);
        if (! isset($userType)) {
            throw new ValidationException([
                    self::ENTITY_NOT_FOUND
                    ], self::ENTITY_NOT_FOUND, 'entity_not_found');
        }

        return $userType;
    }

}