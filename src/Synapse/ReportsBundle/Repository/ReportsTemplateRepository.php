<?php
namespace Synapse\ReportsBundle\Repository;

use Synapse\ReportsBundle\Entity\ReportsTemplate;
use Synapse\CoreBundle\Repository\SynapseRepository;

class ReportsTemplateRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseReportsBundle:ReportsTemplate';

	public function create(ReportsTemplate $reportsTemplate)
    {
        $em = $this->getEntityManager();
        $em->persist($reportsTemplate);
        return $reportsTemplate;
    }
	
	public function getReportTemplates($person, $organization)
	{
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t.id as id, IDENTITY(t.reports) as reports, IDENTITY(t.organization) as organization, t.filterCriteria as filterCriteria, t.templateName as templateName, IDENTITY(t.person) as person, rp.shortCode as short_code, rp.name as report_name, t.createdAt as template_date ')
            ->from('SynapseReportsBundle:ReportsTemplate', 't')
			->LEFTJoin('SynapseReportsBundle:Reports', 'rp', \Doctrine\ORM\Query\Expr\Join::WITH, 't.reports = rp.id')
            ->where('t.person = :person')
			->andWhere('t.organization = :organization')
			->orderBy('t.id', 'DESC')
            ->setParameters(array(
            'person' => $person,
			'organization' => $organization
        ))
			->getQuery();
        return $qb->getArrayResult();
    }
}