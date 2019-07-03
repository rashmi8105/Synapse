<?php
namespace Synapse\AcademicBundle\Repository;

use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\AcademicBundle\Entity\Year;

class YearRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseAcademicBundle:Year';

    public function listYearIds()
    {
        $em = $this->getEntityManager();
        $records = $em->createQueryBuilder()
            ->select('yr.id')
            ->from('SynapseAcademicBundle:Year', 'yr')
            ->orderBy('yr.id')
            ->getQuery()
            ->getResult();
        return $records;
    }
}