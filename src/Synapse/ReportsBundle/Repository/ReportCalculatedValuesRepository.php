<?php
namespace Synapse\ReportsBundle\Repository;

use Synapse\ReportsBundle\Entity\ReportCalculatedValues;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;

class ReportCalculatedValuesRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseReportsBundle:ReportCalculatedValues';

    /**
     *
     * @param ReportCalculatedValues $reportCal            
     * @return ReportCalculatedValues
     */
    public function create(ReportCalculatedValues $reportCal)
    {
        $em = $this->getEntityManager();
        $em->persist($reportCal);
        return $reportCal;
    }
	
	public function getReportDetailsByRepId($personId, $reportId)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('rs.id as section_id', 'rs.title as title')
            ->from(ReportsConstants::REPORTS_CALCULATED_REPO, 'rcv')
            ->join(ReportsConstants::REPORT_SECTION_REPO, 'rs', \Doctrine\ORM\Query\Expr\Join::WITH, 'rcv.sectionId = rs.id')
            ->where('rcv.person = :person')
			->andWhere('rcv.reportId = :report_id')
            ->setParameters(array(
            'report_id' => $reportId, 
			'person' => $personId
        ))
			->groupBy('rcv.sectionId')
            ->getQuery();
        return $qb->getArrayResult();
    }
	
	public function getReportElementDetailBySecId($sectionId, $reportId, $personId)
	{
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('rse.id as element_id', 'rse.title as element_name', 'rse.iconFileName as element_icon')
            ->from(ReportsConstants::REPORTS_CALCULATED_REPO, 'rcv')
            ->join(ReportsConstants::REPORT_SECTION_ELEMENT_REPO, 'rse', \Doctrine\ORM\Query\Expr\Join::WITH, 'rcv.elementId = rse.id')
            ->where('rcv.reportId = :report_id')
			->andWhere('rcv.sectionId = :section_id')
			->andWhere('rcv.person = :person_id')
            ->setParameters(array(
            'report_id' => $reportId, 
			'section_id' => $sectionId,
			'person_id' => $personId
        ))
            ->groupBy('rcv.elementId')
			->getQuery();
        return $qb->getArrayResult();
    }
	
	public function getElementBucketByElementName($sectionId, $elementName, $reportId, $personId)
	{
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('reb.bucketName as element_color', 'reb.bucketText as element_text', 'rcv.calculatedValue as element_score', 'IDENTITY(rcv.survey) as survey_id')
            ->from(ReportsConstants::REPORTS_CALCULATED_REPO, 'rcv')
            ->join(ReportsConstants::REPORT_ELEMENT_BUCKET_REPO, 'reb', \Doctrine\ORM\Query\Expr\Join::WITH, 'rcv.elementBucketId = reb.id')
            ->join(ReportsConstants::REPORT_SECTION_ELEMENT_REPO, 'rse', \Doctrine\ORM\Query\Expr\Join::WITH, 'reb.elementId = rse.id')
            ->where('rcv.reportId = :report_id')
			->andWhere('rse.title = :element_name')
			->andWhere('rcv.sectionId = :section_id')
			->andWhere('rcv.person = :person_id')
            ->setParameters(array(
            'report_id' => $reportId, 
			'element_name' => $elementName,
			'section_id' => $sectionId,
			'person_id' => $personId
        ))
			->getQuery();
        return $qb->getArrayResult();
    }

    /**
     *
     * @param ReportCalculatedValues $reportCalVal            
     */
    public function remove(Reports $reportCalVal)
    {
        $em = $this->getEntityManager();
        $em->remove($reportCalVal);
    }
}
