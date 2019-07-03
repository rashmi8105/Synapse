<?php
namespace Synapse\ReportsBundle\Repository;

use Synapse\ReportsBundle\Entity\ReportRunDetails;
use Synapse\CoreBundle\Repository\SynapseRepository;

class ReportRunDetailsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseReportsBundle:ReportRunDetails';

	public function create(ReportRunDetails $reportRunDetails)
    {
        $em = $this->getEntityManager();
        $em->persist($reportRunDetails);
        return $reportRunDetails;
    }
}