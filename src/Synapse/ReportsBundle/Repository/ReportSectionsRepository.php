<?php
namespace Synapse\ReportsBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\ReportsBundle\Entity\ReportSections;
use Synapse\CoreBundle\Repository\SynapseRepository;

class ReportSectionsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseReportsBundle:ReportSections';

    public function remove(ReportSections $reportSection)
    {
        $em = $this->getEntityManager();
        $em->remove($reportSection);
    }

    public function ourStudentReportExistingData()
    {
        $sql = "(select 
    r.id as report_id,
    rs.id as section_id,
    rs.title as report_section_name,
    rs.sequence as report_sequence,
    rse.source_type as source_type,
    rse.factor_id as factor,
    rse.survey_id as survey,
    rse.ebi_question_id as longitu,
    reb.is_choices,
    group_concat(reb.bucket_name) as bucket_name,
    group_concat(reb.range_min) as min_range,
    group_concat(reb.range_max) as max_range,
    '' as bucket_value
from
    reports r
        INNER JOIN
    report_sections rs ON (rs.report_id = r.id
        AND rs.deleted_at IS NULL)
        INNER JOIN
    report_section_elements rse ON (rse.section_id = rs.id
        AND rse.deleted_at IS NULL)
        INNER JOIN
    report_element_buckets reb ON (reb.element_id = rse.id
        AND rse.deleted_at IS NULL)
where
    r.short_code = 'OSR'
        and r.deleted_at IS NULL
        AND reb.is_choices IS NULL
group by reb.element_id) UNION (select 
    r.id as report_id,
    rs.id as section_id,
    rs.title as report_section_name,
    rs.sequence as report_sequence,
    rse.source_type as source_type,
    rse.factor_id as factor,
    rse.survey_id as survey,
    rse.ebi_question_id as longitu,
    reb.is_choices,
    group_concat(reb.bucket_name) as bucket_name,
    NULL as min_range,
    NULL as max_range,
    group_concat(rbr.value) as bucket_value
from
    reports r
        INNER JOIN
    report_sections rs ON (rs.report_id = r.id
        AND rs.deleted_at IS NULL)
        INNER JOIN
    report_section_elements rse ON (rse.section_id = rs.id
        AND rse.deleted_at IS NULL)
        INNER JOIN
    report_element_buckets reb ON (reb.element_id = rse.id
        AND rse.deleted_at IS NULL)
        INNER JOIN
    report_bucket_range rbr ON (rbr.element_id = reb.id
        AND rbr.deleted_at IS NULL)
where
    r.short_code = 'OSR'
        and r.deleted_at IS NULL
        AND reb.is_choices = 1
group by reb.element_id) order by  section_id";
        $em = $this->getEntityManager();
        $resultSet = $em->getConnection()->fetchAll($sql);
        
        return $resultSet;
    }
    
	public function getSequenceOrder()
    {
        $results = '';
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('max(section.sequence) as sequence_order')
            ->from('SynapseReportsBundle:ReportSections', 'section')
            ->getQuery();
        $result = $qb->getArrayResult();
        if (! empty($result)) {
            $results = $result[0]['sequence_order'];
        }
        return (int) $results;
    }
	
	public function checkSectionName($sectionName, $id = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('section.id', 'section.title as sectionName');
        $qb->from('SynapseReportsBundle:ReportSections', 'section');        
        $qb->where('section.title = :sectionName');
        if (isset($id)) {
            $qb->andWhere('section.id ! =:id');
            $qb->setParameters(array(
                'id' => $id,
                'sectionName' => $sectionName
            ));
        } else {
            $qb->setParameters(array(
                'sectionName' => $sectionName
            ));
        }
        $query = $qb->getQuery();
        $resultSet = $query->getResult();
        return $resultSet;
    }


    /**
     * Returns an array of data about the report sections for the selected report.
     * If the parameter $retentionTrackingType is present, only returns sections of that type.
     * 
     * @param int $reportId
     * @param string|null $retentionTrackingType - 'required' or 'optional' or 'none'
     * @return array
     */
    public function getReportSectionsForSelectedReport($reportId, $retentionTrackingType = null)
    {
        $parameters = [':reportId' => $reportId];

        if (!empty($retentionTrackingType)) {
            $parameters[':retentionTrackingType'] = $retentionTrackingType;
            $retentionTrackingTypeString = 'AND retention_tracking_type = :retentionTrackingType';
        } else {
            $retentionTrackingTypeString = '';
        }

        $sql = "SELECT id AS section_id, title AS section_name, sequence, retention_tracking_type, survey_contingent, academic_term_contingent, risk_contingent
                FROM report_sections
                WHERE report_id = :reportId
                $retentionTrackingTypeString;";

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
