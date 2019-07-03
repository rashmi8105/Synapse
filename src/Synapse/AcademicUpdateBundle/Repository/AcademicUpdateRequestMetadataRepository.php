<?php
namespace Synapse\AcademicUpdateBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;

class AcademicUpdateRequestMetadataRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseAcademicUpdateBundle:AcademicUpdateRequestMetadata';

    public function isIspExists ($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('aurg.id');
        $qb->from('SynapseAcademicUpdateBundle:AcademicUpdateRequestMetadata', 'aurg');
        $qb->where('aurg.orgMetadata = :id');
        $qb->setParameters(['id' =>  $id]);
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }
    
    public function isEbiExists ($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('aurg.id');
        $qb->from('SynapseAcademicUpdateBundle:AcademicUpdateRequestMetadata', 'aurg');
        $qb->where('aurg.ebiMetadata = :id');
        $qb->setParameters(['id' =>  $id]);
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }
}