<?php
namespace Synapse\CoreBundle\Repository;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Doctrine\DBAL\Connection;

class OrgPersonStudentCohortRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgPersonStudentCohort';

    /**
     * Returns an array of data about all cohorts with students assigned for the given org
     *
     * @param int $orgId
     * @param array|null $orgAcademicYearIds
     * @return array
     */
    public function getCohortsByOrganization($orgId, $orgAcademicYearIds = [])
    {
        $parameters = ['orgId' => $orgId];

        $parameterTypes = [];

        if (!empty($orgAcademicYearIds)) {
            $parameterTypes = ['orgAcademicYearIds' => Connection::PARAM_INT_ARRAY];
            $parameters['orgAcademicYearIds'] = $orgAcademicYearIds;
            $orgAcademicYearString = 'AND opsc.org_academic_year_id IN (:orgAcademicYearIds)';
        } else {
            $orgAcademicYearString = '';
        }

        $sql = "SELECT oay.year_id, oay.id AS org_academic_year_id, opsc.cohort, ocn.cohort_name
            FROM org_person_student_cohort opsc
            INNER JOIN org_academic_year oay ON opsc.org_academic_year_id = oay.id
            INNER JOIN org_cohort_name ocn ON ocn.organization_id = opsc.organization_id
              AND ocn.org_academic_year_id = opsc.org_academic_year_id
              AND ocn.cohort = opsc.cohort
            WHERE opsc.organization_id = :orgId
            AND opsc.deleted_at is NULL
            AND ocn.deleted_at is NULL
            AND oay.deleted_at is NULL
            $orgAcademicYearString
            GROUP BY oay.year_id, opsc.org_academic_year_id, ocn.cohort, ocn.cohort_name;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();

        return $results;
    }

    
    /* Returns survey cohorts for particular student, organization, year
     */
    public function getOrgStudentsCohorts($orgId, $yearId, $externalId){
        $sql = "SELECT
                    opsc.cohort,
                    opssl.survey_id
                FROM 
                    synapse.org_person_student_cohort opsc
                        JOIN
                    org_person_student_survey_link opssl ON opsc.organization_id = opssl.org_id
                        AND opsc.org_academic_year_id = opssl.org_academic_year_id
                WHERE
                    opsc.organization_id = ?
                    AND opsc.org_academic_year_id = ?
                    AND opsc.person_id = (SELECT synapse.person.id FROM synapse.person WHERE synapse.person.external_id = ?) ;";
    	try{
    		$em = $this->getEntityManager();
    		$stmt = $em->getConnection()->prepare($sql);
    		$stmt->execute(array(
    				$orgId,
    				$yearId,
    				$externalId
    		));
    	}catch ( \Exception $e ) {
    		throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
    	}
    		
    	$result = $stmt->fetchAll();
    	return $result;
    }
    
    public function getPersonYearCohort($orgId, $personId, $orgAcademicYearId){
        $parameters = [
    		    'orgId' => $orgId,
    		    
    		    'personId' => $personId,
    		    'orgAcademicYearId' => $orgAcademicYearId
    		];
        
    	$sql = "SELECT
                    opsc.cohort,
                    oay.year_id
                FROM
                    org_person_student_cohort opsc
                JOIN
                    org_academic_year oay
                ON
                    opsc.org_academic_year_id = oay.id
                WHERE
                    opsc.organization_id = :orgId
                AND
                    opsc.person_id = :personId
                AND
                    opsc.org_academic_year_id = :orgAcademicYearId
                AND
                    opsc.deleted_at IS NULL
                AND
                    oay.deleted_at IS NULL";
    
    	try {
    		$em = $this->getEntityManager();
    		$stmt = $em->getConnection()->executeQuery($sql, $parameters);
    	} catch (\Exception $e) {
    		throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
    	}
    
    	$results = $stmt->fetchAll();
    
    	return $results;
    
    }
	

}