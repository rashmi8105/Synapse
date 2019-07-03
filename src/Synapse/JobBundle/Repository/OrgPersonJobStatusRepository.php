<?php
namespace Synapse\JobBundle\Repository;

use Facile\DoctrineMySQLComeBack\Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\JobBundle\Entity\OrgPersonJobStatus;

class OrgPersonJobStatusRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseJobBundle:OrgPersonJobStatus';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return OrgPersonJobStatus |null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }


    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return OrgPersonJobStatus[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     *
     * @return OrgPersonJobStatus|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * Check any pending jobs through blocked mapping for users.
     *
     * @param int $organizationId
     * @param array $jobType
     * @param array $jobStatus
     * @param int $personId
     * @return array
     */
    public function getJobActionForRequestedJob($organizationId, $jobType, $jobStatus, $personId)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'jobType' => $jobType,
            'jobStatus' => $jobStatus,
            'personId' => $personId
        ];
        $parameterTypes['jobType'] = Connection::PARAM_STR_ARRAY;
        $parameterTypes['jobStatus'] = Connection::PARAM_STR_ARRAY;

        $sql = "SELECT jtbm.action              
                    FROM
                        org_person_job_status opjs
                          INNER JOIN
                        job_type_blocked_mapping jtbm ON opjs.job_type_id = jtbm.blocked_by_job_type_id
                          INNER JOIN
                        job_type jt ON jt.id = jtbm.job_type_id
                          INNER JOIN
                        job_status_description jsd ON jsd.id = opjs.job_status_id
                    WHERE
                        opjs.organization_id = :organizationId
                        AND opjs.person_id = :personId
                        AND opjs.deleted_at IS NULL
                        AND jt.job_type IN (:jobType)
                        AND jt.deleted_at IS NULL
                        AND jtbm.deleted_at IS NULL                        
                        AND jsd.job_status_description IN (:jobStatus)
                        AND jsd.deleted_at IS NULL
                  UNION
                    SELECT jtbm.action                
                      FROM
                        org_person_job_queue opjq
                          INNER JOIN
                        job_type_blocked_mapping jtbm ON opjq.job_type_id = jtbm.blocked_by_job_type_id
                          INNER JOIN
                        job_type jt ON jt.id = jtbm.job_type_id                        
                      WHERE
                        opjq.organization_id = :organizationId
                        AND opjq.person_id = :personId
                        AND opjq.deleted_at IS NULL
                        AND jt.job_type IN (:jobType)
                        AND jt.deleted_at IS NULL
                        AND jtbm.deleted_at IS NULL
                        AND opjq.queued_status = 0";

        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        $results = array_map('current', $resultSet);
        return $results;
    }

    /**
     * Check any pending job for users.
     *
     * @param int $organizationId
     * @param int $personId
     * @param array $jobType
     * @param array $jobStatus
     * @return int|null
     */
    public function checkPendingJobsByJobType($organizationId, $personId, $jobType, $jobStatus)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'personId' => $personId,
            'jobType' => $jobType,
            'jobStatus' => $jobStatus
        ];
        $parameterTypes['jobType'] = Connection::PARAM_STR_ARRAY;
        $parameterTypes['jobStatus'] = Connection::PARAM_STR_ARRAY;

        $sql = "SELECT 
                    COUNT(*) AS total_pending_jobs
                FROM
                    org_person_job_status opjs
                        INNER JOIN
                    job_status_description jsd ON jsd.id = opjs.job_status_id
                        INNER JOIN
                    job_type jt ON jt.id = opjs.job_type_id
                WHERE
                    opjs.organization_id = :organizationId
                        AND opjs.person_id = :personId                        
                        AND jsd.job_status_description IN (:jobStatus)
                        AND jt.job_type IN (:jobType)
                        AND jt.deleted_at IS NULL 
                        AND jsd.deleted_at IS NULL 
                        AND opjs.deleted_at IS NULL
                ";
        $resultSet = $this->executeQueryFetch($sql, $parameters, $parameterTypes);
        return $resultSet['total_pending_jobs'];
    }

    /**
     * Check any pending job for user/organization.
     *
     * @param int $organizationId
     * @param int $personId
     * @param array $jobStatus
     * @return int|null
     */
    public function checkPendingJobsByRole($organizationId, $personId, $jobStatus)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'jobStatus' => $jobStatus
        ];
        if ($personId) {
            $parameters['personId'] = $personId;
        }
        $parameterTypes['jobStatus'] = Connection::PARAM_STR_ARRAY;

        $sql = "SELECT 
                    COUNT(*) AS total_pending_jobs
                FROM
                    org_person_job_status opjs
                        INNER JOIN
                    job_status_description jsd ON jsd.id = opjs.job_status_id                                            
                WHERE
                    opjs.organization_id = :organizationId
                        AND opjs.person_id = :personId                        
                        AND jsd.job_status_description IN (:jobStatus)     
                        AND jsd.deleted_at IS NULL 
                        AND opjs.deleted_at IS NULL
                ";
        $resultSet = $this->executeQueryFetch($sql, $parameters, $parameterTypes);
        return $resultSet['total_pending_jobs'];
    }

    /**
     * Gets the count of organization level pending jobs.
     *
     * @param int $organizationId
     * @param array $jobType
     * @param array $jobStatus
     * @param bool $personNullCheckRequired - When coordinator trying to change the sync we have to consider the person_id since if anyone person from his organization is doing sync/un sync he can not change status. In case when faculty is trying, we have to validate only the organization level pending jobs are there or not so person_id check is not required.
     * @return int
     */
    public function checkOrganizationPendingJobsByBlockMapping($organizationId, $jobType, $jobStatus, $personNullCheckRequired = true)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'jobType' => $jobType,
            'jobStatus' => $jobStatus
        ];
        $condition = '';
        if ($personNullCheckRequired) {
            $condition = " AND opjs.person_id IS NULL";
        }

        $parameterTypes['jobType'] = Connection::PARAM_STR_ARRAY;
        $parameterTypes['jobStatus'] = Connection::PARAM_STR_ARRAY;

        $sql = "SELECT SUM(total_pending_jobs) as total_pending_jobs 
                  FROM (
                      SELECT COUNT(*) AS total_pending_jobs
                        FROM
                          org_person_job_status opjs
                        INNER JOIN
                          job_type_blocked_mapping jtbm ON opjs.job_type_id = jtbm.blocked_by_job_type_id                      
                        INNER JOIN 
                          job_type jt ON jt.id = jtbm.job_type_id 
                        INNER JOIN 
                          job_status_description jsd ON jsd.id = opjs.job_status_id                      
                        WHERE
                          opjs.organization_id = :organizationId                                                         
                          AND jt.job_type IN (:jobType)
                          AND jsd.job_status_description IN (:jobStatus)
                          $condition
                          AND opjs.deleted_at IS NULL
                          AND jt.deleted_at IS NULL                    
                          AND jtbm.deleted_at IS NULL                                        
                          AND jsd.deleted_at IS NULL               
                  UNION
                      SELECT 
                            COUNT(*) AS total_pending_jobs
                        FROM
                            org_person_job_queue opjq
                                INNER JOIN
                            job_type_blocked_mapping jtbm ON opjq.job_type_id = jtbm.blocked_by_job_type_id
                                INNER JOIN
                            job_type jt ON jt.id = jtbm.job_type_id
                        WHERE
                            opjq.organization_id = :organizationId
                                AND opjq.person_id IS NULL
                                AND opjq.deleted_at IS NULL
                                AND opjq.queued_status = 0
                                AND jt.job_type IN (:jobType)
                                AND jt.deleted_at IS NULL
                                AND jtbm.deleted_at IS NULL
                  ) AS total_pending_jobs";
        $resultSet = $this->executeQueryFetch($sql, $parameters, $parameterTypes);
        return $resultSet['total_pending_jobs'];
    }

    /**
     * Get queued/in progress jobs
     *
     * @param int $organizationId
     * @param int $personId
     * @param array $jobStatus
     * @return array|null
     */
    public function getJobsByStatus($organizationId, $personId, $jobStatus)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'jobStatus' => $jobStatus,
            'personId' => $personId
        ];

        $parameterTypes['jobType'] = Connection::PARAM_STR_ARRAY;
        $parameterTypes['jobStatus'] = Connection::PARAM_STR_ARRAY;

        $sql = "SELECT 
                    opjs.id                    
                FROM
                    org_person_job_status opjs
                        INNER JOIN                   
                    job_status_description jsd ON jsd.id = opjs.job_status_id
                WHERE
                    opjs.organization_id = :organizationId
                        AND opjs.person_id = :personId
                        AND opjs.deleted_at IS NULL                        
                        AND jsd.job_status_description IN (:jobStatus)
                        AND jsd.deleted_at IS NULL";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }
}