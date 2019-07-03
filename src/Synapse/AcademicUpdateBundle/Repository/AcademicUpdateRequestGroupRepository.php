<?php
namespace Synapse\AcademicUpdateBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;

class AcademicUpdateRequestGroupRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseAcademicUpdateBundle:AcademicUpdateRequestGroup';

    public function isAUExistsForGroup ($groupId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('aurg.id');
        $qb->from('SynapseAcademicUpdateBundle:AcademicUpdateRequestGroup', 'aurg');
        $qb->where('aurg.orgGroup = :groupId');
        $qb->setParameters(['groupId' =>  $groupId]);
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }
}