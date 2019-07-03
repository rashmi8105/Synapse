<?php
namespace Synapse\ReportsBundle\Repository;

use Synapse\ReportsBundle\Entity\ReportTips;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;

class ReportTipsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseReportsBundle:ReportTips';

    public function remove(ReportTips $reportTips)
    {
        $em = $this->getEntityManager();
        $em->remove($reportTips);
    }
	
	public function getTipsForSection($sectionId)
	{
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('t.title as title', 't.description as description')
            ->from(ReportsConstants::REPORT_TIPS_REPO, 't')
            ->where('t.sectionId = :section_id')
            ->setParameters(array(
            'section_id' => $sectionId
        ))
			->getQuery();
        return $qb->getArrayResult();
    }
    
    public function deleteTipsBySectionId($sectionIds)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('SynapseReportsBundle:ReportTips', 'tips');
        $qb->set('tips.deletedAt', 'CURRENT_TIMESTAMP()');
        $qb->where($qb->expr()
            ->in('tips.sectionId', ':sectionIds'));
        $qb->setParameters(array(
            'sectionIds' => $sectionIds
        ));
        
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
	
	public function getSectionTipsByReportId($repordId)
    {
        $em = $this->getEntityManager();		
        $qb = $em->createQueryBuilder();        
        $qb->select('tips.id as tipId','tips.title as tipName','rs.title as sectionName','rs.id as sectionId');
        $qb->from('SynapseReportsBundle:ReportTips', 'tips');
        $qb->join('SynapseReportsBundle:ReportSections', 'rs',\Doctrine\ORM\Query\Expr\Join::WITH, 'rs.id = tips.sectionId');		
        $qb->where('rs.reports = :reports');
        $qb->setParameters(array(
            'reports' => $repordId
        ));
        $qb->orderBy('sectionId, tipName');
        $query = $qb->getQuery();		
        $resultSet = $query->getArrayResult();
        return  $resultSet;
        
    }
	
	public function createTip(ReportTips $reportTips)
    {
        $em = $this->getEntityManager();
        $em->persist($reportTips);	
		$em->flush();		
        return $reportTips;
    }
    
	/*
     * It will return the available section-tips for student report
     */	
	public function dumpSectionTips()
	{
		/*
         * Changing this query in order to link the section tips with student report.
         */        
        $sql = "select 
                    t.section_id as sectionId,
                    t.id as tipId,
                    s.title as sectionName,
                    t.title as tipName,
                    t.description as tipText,
                    t.sequence as sequence
                from
                    report_tips t
                        join
                    report_sections s ON (s.id = t.section_id)
                        join
                    reports r ON (r.id = s.report_id)
                where
                    r.name = 'student-report'
                order by sectionId , tipName"; 
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql);            
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }     
        $results = $stmt->fetchAll();
        return $results;
	}
}
