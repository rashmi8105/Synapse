<?php
namespace Synapse\ReportsBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\ReportsBundle\Entity\ReportSectionElements;
use Synapse\CoreBundle\Repository\SynapseRepository;

class ReportSectionElementsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseReportsBundle:ReportSectionElements';

    public function remove(ReportSectionElements $reportSectionElements)
    {
        $em = $this->getEntityManager();
        $em->remove($reportSectionElements);
    }
    
    public function findElementIdForSection($sectionId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();        
        $qb->select('elements.id');
        $qb->from('SynapseReportsBundle:ReportSectionElements', 'elements');
        $qb->where('elements.sectionId = :sectionId');
        $qb->setParameters(array(
            'sectionId' => $sectionId
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return  $resultSet;
    }
    
     
    public function deleteSectionElements($sectionId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('SynapseReportsBundle:ReportSectionElements', 'elements');
        $qb->set('elements.deletedAt', 'CURRENT_TIMESTAMP()');
        $qb->where($qb->expr()
            ->eq('elements.sectionId', ':sectionId'));
        $qb->setParameters(array(
            'sectionId' => $sectionId
        ));        
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }
    
    public function getReportListByReportId($repordId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        
        $qb->select('elements.id','rs.title as sectionName','elements.title as displayLabel','rs.id as sectionId');
        $qb->from('SynapseReportsBundle:ReportSectionElements', 'elements');
        $qb->join('SynapseReportsBundle:ReportSections', 'rs',\Doctrine\ORM\Query\Expr\Join::WITH, 'rs.id = elements.sectionId');
        $qb->where('rs.reports = :reports');
        $qb->setParameters(array(
            'reports' => $repordId
        ));
        $qb->orderBy('sectionName');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return  $resultSet;
        
    }
		
    public function getSectionElementsReportId($repordId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();        
        $qb->select('elements.id as elementId','rs.title as sectionName','elements.title as elementName','rs.id as sectionId', 'elements.iconFileName as element_icon');
        $qb->from('SynapseReportsBundle:ReportSectionElements', 'elements');
        $qb->join('SynapseReportsBundle:ReportSections', 'rs',\Doctrine\ORM\Query\Expr\Join::WITH, 'rs.id = elements.sectionId');
        $qb->where('rs.reports = :reports');
        $qb->setParameters(array(
            'reports' => $repordId
        ));
        $qb->orderBy('sectionId, elementName');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return  $resultSet;
        
    }
	
	public function createElements(ReportSectionElements $elements) 
    {
        $em = $this->getEntityManager();
        $em->persist($elements);		
        return $elements;
    }
    
    /*
     * It will return the section-elements for the student reports  
     */		
	public function dumpSectionElements()
	{		
        /*
         * Changing this query in order to link the section elements with student report.
         */        
        $sql = "select 
                    e.section_id as sectionId,
                    e.id as elementId,
                    s.title as sectionName,
                    e.title as elementName,
                    e.source_type as sourceType,
                    e.factor_id as factorId,
                    e.survey_question_id as surveyQuestionId
                from
                    report_section_elements e
                        join
                    report_sections s ON (e.section_id = s.id)
                        join
                    reports r ON (r.id = s.report_id)
                where
                    r.name = 'student-report'
                order by section_id , elementName"; 
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql);            
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }     
        $results = $stmt->fetchAll();
        return $results;
	}


    /**
     * Returns information about the report_section_elements for the given section.
     * If $hasDescription is true, only returns elements which have a non-null description.
     *
     * @param $sectionId
     * @param $hasText
     * @return array
     */
    public function getElementsForSelectedSection($sectionId, $hasText = null)
    {
        $parameters = [':sectionId' => $sectionId];

        if ($hasText) {
            $descriptionString = 'AND rse.description IS NOT NULL';
        } else {
            $descriptionString = '';
        }

        $sql = "SELECT rse.section_id, rs.title AS section_name, rse.id AS element_id, rse.title AS element_name, rse.description AS element_text
                FROM report_section_elements rse
                INNER JOIN report_sections rs ON rse.section_id = rs.id
                WHERE rse.section_id = :sectionId
                $descriptionString;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute($parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        return $results;
    }
}
