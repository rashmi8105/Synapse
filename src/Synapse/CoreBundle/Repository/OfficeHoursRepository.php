<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query\Expr\Expr;
use Doctrine\ORM\Query\Expr\Join;
use SebastianBergmann\Exporter\Exception;
use Synapse\CoreBundle\Entity\OfficeHours;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Entity\Error;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;
use Synapse\RestBundle\Entity\OfficeHoursDto;

class OfficeHoursRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OfficeHours';

    /**
     * Parent class override for typing.
     *
     * @param int $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return OfficeHours
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * Parent class override for typing.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return OfficeHours[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Parent class override for typing.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @return OfficeHours
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }


    public function createOfficeHours(OfficeHours $officeHours)
    {
        $em = $this->getEntityManager();
        $em->persist($officeHours);
        return $officeHours;
    }

    public function remove(OfficeHours $officeHours)
    {
        $this->getEntityManager()->remove($officeHours);
    }

    /**
     * Get the list of appointments for the proxy users
     *
     * @param int $proxyId
     * @param string $fromDate
     * @param string $toDate
     * @param string $frequency
     * @param int $managedPersonIds
     * @param string $currentDateTime
     * @param int $orgAcademicYearId
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getProxyUsersAppointments($proxyId, $fromDate, $toDate, $frequency, $managedPersonIds, $currentDateTime, $orgAcademicYearId)
    {
        $includeSharedBy = "";
        $includePersonId = "";

        $managedPersonIds = explode(",", $managedPersonIds);
        if (!empty($managedPersonIds)) {
            $includeSharedBy = " AND person_id_sharedby IN (:managedPersonIds)";
            $includePersonId = " AND person_id IN (:managedPersonIds)";
        }

        $toDate = $toDate . AppointmentsConstant::TIME235959;

        $parameters = ['proxyId' => $proxyId, 'managedPersonIds' => $managedPersonIds];
        if ($frequency == "past") {

            $sql = "SELECT 
                        oh.appointments_id,
                        oh.id as office_hours_id,
                        oh.person_id,
                        oh.slot_type,
                        oh.location,
                        a.type,
                        a.location AS app_loc,
                        oh.slot_start,
                        oh.slot_end,
                        oh.meeting_length,
                        a.type,
                        a.start_date_time,
                        a.end_date_time,
                        a.is_free_standing,
                        a.title,
                        a.activity_category_id,
                        oh.is_cancelled,
                        oh.google_appointment_id
                    FROM
                        office_hours oh
                            LEFT OUTER JOIN
                        Appointments a ON (oh.appointments_id = a.id)
                    WHERE
                        oh.person_id in (SELECT 
                                person_id_sharedby
                            FROM
                                calendar_sharing
                            WHERE
                                person_id_sharedto = :proxyId
                                $includeSharedBy)
                            AND oh.slot_start < :currentDateTime
                            AND oh.deleted_at IS NULL 
                    UNION SELECT 
                        a.id AS appointments_id,
                        0 AS office_hours_id,
                        a.person_id,
                        '' AS slot_type,
                        a.location,
                        a.type,
                        null AS app_loc,
                        a.start_date_time AS slot_start,
                        a.end_date_time AS slot_end,
                        null AS meeting_length,
                        a.type,
                        a.start_date_time,
                        a.end_date_time,
                        a.is_free_standing,
                        a.title,
                        a.activity_category_id,
                        '' AS is_cancelled,
                        a.google_appointment_id
                    FROM
                      Appointments AS a
                      	LEFT JOIN
                      	appointment_recepient_and_status AS aras ON a.person_id = aras.person_id_faculty 
                      	        AND a.id = aras.appointments_id
                      	        AND aras.deleted_at IS NULL
                      	    INNER JOIN
                      	    org_person_student_year AS opsy ON opsy.person_id = aras.person_id_student
                      	WHERE
                      	a.is_free_standing IS TRUE
                      	  AND a.person_id IN (:managedPersonIds)
                      	  	AND a.start_date_time < :currentDateTime
                      	  	AND opsy.org_academic_year_id = :orgAcademicYearId
                      	  	AND opsy.deleted_at IS NULL
                      	  	AND a.deleted_at IS NULL
                      	ORDER BY slot_start , slot_end";

            $parameters['currentDateTime'] = $currentDateTime;
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;

        } else {

            $sql = "SELECT 
                        oh.appointments_id,
                        oh.id AS office_hours_id,
                        oh.person_id,
                        oh.slot_type,
                        oh.location,
                        a.type,
                        a.location AS app_loc,
                        oh.slot_start,
                        oh.slot_end,
                        oh.meeting_length,
                        a.type,
                        a.start_date_time,
                        a.end_date_time,
                        a.is_free_standing,
                        a.title,
                        a.activity_category_id,
                        oh.is_cancelled,
                        oh.google_appointment_id AS google_appointment_id
                    FROM
                        office_hours oh
                            LEFT JOIN
                        appointment_recepient_and_status aras ON aras.organization_id = oh.organization_id
                            AND aras.person_id_faculty = oh.person_id
                            AND aras.deleted_at IS NULL
                            LEFT JOIN
                        Appointments a ON aras.organization_id = a.organization_id
                            AND aras.appointments_id = a.id
                            AND a.deleted_at IS NULL
                    WHERE
                        oh.person_id IN (SELECT 
                                person_id_sharedby
                            FROM
                                calendar_sharing
                            WHERE
                                person_id_sharedto = :proxyId
                                $includeSharedBy)
                                AND IF((oh.appointments_id IS NOT NULL), oh.appointments_id = a.id, 1)
                                AND oh.slot_start >= :fromDate
                                AND oh.slot_start <= :toDate
                                AND ((oh.deleted_at IS NULL	AND oh.appointments_id IS NULL)
                                	OR oh.appointments_id IS NOT NULL)
                                GROUP BY CASE 
                                WHEN (oh.appointments_id IS NOT NULL) THEN oh.appointments_id 
                                  ELSE oh.id 
                                END
                    UNION 
                    SELECT 
                        a.id AS appointments_id,
                        0 AS office_hours_id,
                        a.person_id,
                        '' AS slot_type,
                        a.location,
                        a.type,
                        null AS app_loc,
                        a.start_date_time AS slot_start,
                        a.end_date_time AS slot_end,
                        null AS meeting_length,
                        a.type,
                        a.start_date_time,
                        a.end_date_time,
                        a.is_free_standing,
                        a.title,
                        a.activity_category_id,
                        '' AS is_cancelled,
                        a.google_appointment_id
                    FROM
                        Appointments a
                        INNER JOIN 
                        appointment_recepient_and_status ars ON a.id = ars.appointments_id
                    WHERE
                        a.is_free_standing IS TRUE
                            AND a.start_date_time >= :fromDate
                            AND a.start_date_time <= :toDate
                            AND a.deleted_at IS NULL
                            $includePersonId
                    ORDER BY slot_start , slot_end";

            $parameters['fromDate'] = $fromDate;
            $parameters['toDate'] = $toDate;
        }

        $parameterTypes['managedPersonIds'] = Connection::PARAM_INT_ARRAY;
        try {
            $results = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }

    /**
     * Get the list of appointments for the users
     *
     * @param int $personId
     * @param string $fromDate
     * @param string $toDate
     * @param string $frequency
     * @param string $currentDateTime
     * @param int $orgAcademicYearId
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getUsersAppointments($personId, $fromDate, $toDate, $frequency, $currentDateTime, $orgAcademicYearId)
    {
        $toDate = $toDate . AppointmentsConstant::TIME235959;
        if ($frequency == "past") {
            $sql = "SELECT 
                    oh.appointments_id,
                    oh.id AS office_hours_id,
                    a.person_id_proxy AS person_id_proxy,
                    oh.person_id,
                    oh.slot_type,
                    oh.location,
                    a.location AS app_loc,
                    oh.slot_start,
                    oh.slot_end,
                    oh.meeting_length,
                    a.type,
                    a.start_date_time,
                    a.end_date_time,
                    a.is_free_standing,
                    a.title,
                    a.activity_category_id,
                    oh.is_cancelled,
                    oh.google_appointment_id AS office_hour_google_appointment_id,
                    a.google_appointment_id AS appointment_google_appointment_id
                  FROM
                    office_hours AS oh
                      LEFT OUTER JOIN
                    Appointments AS a ON oh.appointments_id = a.id
                  WHERE
					oh.person_id = :personId
						AND oh.slot_start < :currentDateTime
						AND oh.deleted_at IS NULL 
				UNION 
				SELECT 
					a.id AS appointments_id,
					0 AS office_hours_id,
					a.person_id,
					a.person_id_proxy,
					'' AS slot_type,
					a.location,
					NULL AS app_loc,
					a.start_date_time AS slot_start,
					a.end_date_time AS slot_end,
					NULL AS meeting_length,
					type,
					a.start_date_time,
					a.end_date_time,
					a.is_free_standing,
					a.title,
					a.activity_category_id,
					'' AS is_cancelled,
					'' AS office_hour_google_appointment_id,
					a.google_appointment_id AS appointment_google_appointment_id
				FROM
					Appointments AS a
					    LEFT JOIN 
					appointment_recepient_and_status AS aras ON a.id = aras.appointments_id 
					AND aras.deleted_at IS NULL
					    INNER JOIN
                    org_person_student_year AS opsy ON opsy.person_id = aras.person_id_student
				WHERE
					a.is_free_standing IS TRUE
						AND aras.person_id_faculty = :personId
						AND a.start_date_time < :currentDateTime
						AND opsy.org_academic_year_id = :orgAcademicYearId
						AND opsy.deleted_at IS NULL
						AND a.deleted_at IS NULL
				ORDER BY slot_start , slot_end";

            $parameters = [
                'personId' => $personId,
                'currentDateTime' => $currentDateTime,
                'orgAcademicYearId' => $orgAcademicYearId
            ];
        } else {
            $sql = "SELECT 
					    oh.appointments_id,
					    oh.id AS office_hours_id,
					    oh.person_id,
					    a.person_id_proxy,
					    oh.slot_type,
					    oh.location,
					    a.location AS app_loc,
					    oh.slot_start,
					    oh.slot_end,
					    oh.meeting_length,
					    a.type,
					    a.start_date_time,
					    a.end_date_time,
					    a.is_free_standing,
					    a.title,
					    a.activity_category_id,
					    oh.is_cancelled,
					    oh.google_appointment_id AS office_hour_google_appointment_id,
					    a.google_appointment_id AS appointment_google_appointment_id
				    FROM
				        office_hours oh
							LEFT JOIN
						appointment_recepient_and_status aras
                              ON aras.organization_id = oh.organization_id
                              AND aras.person_id_faculty = oh.person_id			  
                              AND aras.deleted_at IS NULL
							LEFT JOIN
						Appointments a
                              ON aras.organization_id = a.organization_id
                              AND aras.appointments_id = a.id
                              AND a.id = oh.appointments_id	
                              AND a.deleted_at IS NULL					
									        
				    WHERE 
				        oh.person_id = :personId
				        AND IF((oh.appointments_id IS NOT NULL),oh.appointments_id = a.id,1)
				        AND oh.slot_start >= :fromDate						
				        AND oh.slot_start <= :toDate
				        AND oh.deleted_at IS NULL				        
				    GROUP BY CASE 
  				        WHEN (oh.appointments_id IS NOT NULL) THEN oh.appointments_id 
				            ELSE oh.id
				        END
				    UNION
				    SELECT
				        a.id AS appointments_id,
				        0 AS office_hours_id,
				        a.person_id,
				        a.person_id_proxy AS person_id_proxy,
				        '' AS slot_type,
				        a.location,
				        NULL AS app_loc,
				        a.start_date_time AS slot_start,
				        a.end_date_time AS slot_end,
				        NULL AS meeting_length,
				        a.type,
				        a.start_date_time,
				        a.end_date_time,
				        a.is_free_standing,
				        a.title,
				        a.activity_category_id,
				        '' AS is_cancelled,
				        '' AS office_hour_google_appointment_id,
				        a.google_appointment_id AS appointment_google_appointment_id
				    FROM
  				        Appointments a
                        INNER JOIN 
                        appointment_recepient_and_status ars ON a.id = ars.appointments_id
                    WHERE 
  				        a.is_free_standing IS TRUE
				        AND ars.person_id_faculty = :personId
				        AND a.start_date_time >= :fromDate
				        AND a.start_date_time <= :toDate
				        AND a.deleted_at IS NULL
                        AND ars.deleted_at IS NULL
				    ORDER BY slot_start , slot_end";

            $parameters = [
                'personId' => $personId,
                'fromDate' => $fromDate,
                'toDate' => $toDate
            ];
        }
        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }

    /**
     * Get all office hours for a faculty, and indicate whether or not they overlap an existing appointment.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param string $startDate - 'yyyy-mm-dd HH:mm:ss'
     * @param string $endDate - 'yyyy-mm-dd HH:mm:ss'
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getOfficeHoursForFaculty($facultyId, $organizationId, $startDate, $endDate)
    {

        $parameters = [
            'organizationId' => $organizationId,
            'facultyId' => $facultyId,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $sql = "
            SELECT DISTINCT
                oh.id AS office_hours_id,
                oh.organization_id,
                oh.slot_start,
                oh.slot_end,
                oh.slot_type,
                oh.location,
                oh.google_appointment_id,
                MAX(IF((a.start_date_time < oh.slot_end AND a.end_date_time > oh.slot_start), 1, 0)) AS overlaps_appointment
            FROM
                office_hours oh
                    LEFT JOIN
                appointment_recepient_and_status aras
                        ON aras.organization_id = oh.organization_id
                        AND aras.person_id_faculty = oh.person_id
                        AND aras.deleted_at IS NULL
                    LEFT JOIN
                Appointments a
                        ON aras.organization_id = a.organization_id
                        AND aras.appointments_id = a.id
                        AND a.deleted_at IS NULL
            WHERE
                oh.deleted_at IS NULL
                AND oh.organization_id = :organizationId
                AND oh.person_id = :facultyId
                AND oh.slot_end >= :startDate
                AND oh.slot_start <= :endDate
            GROUP BY oh.id
            ORDER BY oh.slot_start ASC, oh.slot_end ASC, oh.id ASC;
        ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();

        return $records;
    }

    public function checkSlotAvailable($officeHoursId, $facultyId, $orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $param = array();
        $param['id'] = $officeHoursId;
        $param['facultyId'] = $facultyId;
        $param['orgId'] = $orgId;
        $qb->select('oh.id');
        $qb->from(AppointmentsConstant::OFFICEHOURS_REPO, 'oh');
        $qb->where('oh.id = :id');
        $qb->andWhere('oh.person = :facultyId');
        $qb->andWhere('oh.organization = :orgId');
        $qb->andWhere('oh.appointments is null');
        $qb->setParameters($param);
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }

    /**
     * @param $organization
     * @param $faculty
     * @return OfficeHours[]
     */
    public function getSyncedMafOfficeHours($organization, $faculty)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('officehours')
            ->from(AppointmentsConstant::OFFICEHOURS_REPO, 'officehours')
            ->where('officehours.organization = :organization')
            ->andWhere('officehours.person = :person')
            ->andWhere('officehours.googleAppointmentId IS NOT NULL')
            ->setParameters(array(
            'organization' => $organization,
            'person' => $faculty
        ))
            ->getQuery();
        $resultSet = $queryBuilder->getResult();
        return $resultSet;
    }

    /**
     * Return all future office hours which is not synced with external Calendar
     *
     * @param int $organizationId
     * @param int $personId
     * @param string $startTime
     * @return OfficeHours[]
     * @throws SynapseDatabaseException
     */
    public function getFutureOfficeHoursForFaculty($organizationId, $personId, $startTime)
    {

        $parameters = [
            'organizationId' => $organizationId,
            'personId' => $personId,
            'startTime' => $startTime
        ];
        $sql = "SELECT
                    *
                FROM
                    office_hours
                WHERE
                    organization_id = :organizationId
                        AND person_id = :personId
                        AND slot_start >= :startTime                        
                        AND appointments_id IS NULL
                        AND deleted_at IS NULL
                ORDER BY slot_start";

        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }
    
    public function findMAFOfficeHourse($officeHourSeriesId, $organizationId, $personId, $start, $end)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('officehours')
            ->from(AppointmentsConstant::OFFICEHOURS_REPO, 'officehours')
            ->where('officehours.organization = :organization')
            ->andWhere('officehours.person = :person')
            ->andWhere('officehours.officeHoursSeries = :officeHoursSeries')
            ->andWhere('officehours.slotStart = :start')
            ->andWhere('officehours.slotStart = :end')
            ->setParameters(array(
                'organization' => $organizationId,
                'person' => $personId,
                'officeHoursSeries' => $officeHourSeriesId,
                'start' => $start,
                'end' => $end
            ))
            ->getQuery();            
            $resultSet = $queryBuilder->getResult();            
            return $resultSet;
    }


    /**
     * Returns a boolean true / false based on whether or not there are other office hours in the same timeframe as the one attempting to be created.
     *
     * @param int $facultyId
     * @param int $orgId
     * @param string $startDate - 'Y-m-d h:i:s'
     * @param string $endDate - 'Y-m-d h:i:s'
     * @param array|null $officeHourIdsToExclude
     * @return bool
     * @throws SynapseDatabaseException
     */
    public function isOverlappingOfficeHours($facultyId, $orgId, $startDate, $endDate, $officeHourIdsToExclude = null)
    {
        $parameters = [
            'personId' => $facultyId,
            'organizationId' => $orgId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
        $parameterTypes = [];

        if (!is_null($officeHourIdsToExclude)) {
            $excludeCondition = ' AND id NOT IN (:officeHourIdsToExclude) ';
            $parameters['officeHourIdsToExclude'] = $officeHourIdsToExclude;
            $parameterTypes['officeHourIdsToExclude'] = Connection::PARAM_INT_ARRAY;
        } else {
            $excludeCondition = '';
        }

        $sql = "
            SELECT
                id
            FROM
                office_hours
            WHERE
                person_id = :personId
                AND organization_id = :organizationId
                $excludeCondition
                AND (slot_end > :startDate AND slot_start < :endDate)
                AND deleted_at IS NULL;
        ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();

        if (empty($records)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the list of future office hours which has modified in mapworks
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param string $startDate
     * @param bool $isSyncWithExistingProvider
     * @param string $modifiedTime
     * @return OfficeHours[]
     * @throws SynapseDatabaseException
     */
    public function getFutureOfficeHoursModified($organizationId, $facultyId, $startDate, $modifiedTime, $isSyncWithExistingProvider = true)
    {
        $condition = '';
        if($isSyncWithExistingProvider) {
            $condition = "AND modified_at >= :modifiedTime AND last_synced <= :modifiedTime";
            $parameters = [
                'orgId' => $organizationId,
                'personId' => $facultyId,
                'startTime' => $startDate,
                'modifiedTime' => $modifiedTime
            ];
        } else {
            $parameters = [
                'orgId' => $organizationId,
                'personId' => $facultyId,
                'startTime' => $startDate
            ];
        }
        $sql = "SELECT
                    *
                FROM
                    office_hours
                WHERE
                    organization_id = :orgId
                        AND person_id = :personId
                        AND slot_start >= :startTime
                        $condition
                        AND appointments_id IS NULL
                        AND deleted_at IS NULL";

        try {
            $em = $this->getEntityManager();
            $statement = $em->getConnection()->prepare($sql);
            $statement->execute($parameters);
            $results = $statement->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }

    /**
     * Get the list of deleted future office hours when the sync is disabled in mapworks
     * @param int $organizationId
     * @param int $personId
     * @param string $startTime
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getDeletedFutureOfficeHours($organizationId, $personId, $startTime)
    {
        $parameters = [
            'orgId' => $organizationId,
            'personId' => $personId,
            'startTime' => $startTime
        ];

        $sql = "SELECT
                    o.id, o.google_appointment_id
                FROM
                    office_hours o
                WHERE
                    organization_id = :orgId
                        AND person_id = :personId
                        AND slot_start >= :startTime
                        AND google_appointment_id IS NOT NULL
                        AND deleted_at IS NOT NULL";
        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }

    /**
     * UPDATE google_appointment_id and last_synced in office_hours, office_hours_series tables.
     * @param string $table
     * @param string $field
     * @param int $id
     */
    public function updateOfficeHoursWithNULL($table, $field, $id)
    {
        $currentTime = new \DateTime('now');
        $sql = "UPDATE $table SET $field = NULL, last_synced = :currentTime WHERE id IN (:id) ";
        $parameterTypes = [];
        $parameters = ['id' => $id, 'currentTime' => $currentTime->format('Y-m-d H:i:s')];
        if ($table == 'office_hours_series') {
            $parameterTypes['id'] = Connection::PARAM_INT_ARRAY;
        }
        try {
            $em = $this->getEntityManager();
            $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    /**
     * UPDATE office_hours table with external calendar sync details.
     *
     * @param array $deletedIds
     * @param string $currentTime
     * @param int $loggedUserId
     * @return void
     * @throws SynapseDatabaseException
     */
    public function updateSyncDetailsToOfficeHours($deletedIds, $currentTime, $loggedUserId)
    {
        $parameters = [
            'id' => $deletedIds,
            'currentTime' => $currentTime,
            'modifiedBy' => $loggedUserId
        ];
        $parameterTypes['id'] = Connection::PARAM_INT_ARRAY;

        $sql = "UPDATE office_hours 
                SET 
                    google_appointment_id = NULL,
                    last_synced = :currentTime,
                    modified_at = :currentTime,
                    modified_by = :modifiedBy
                WHERE
                    id IN (:id)";
        try {
            $em = $this->getEntityManager();
            $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }


    /**
     * Get all future slots for a person with start_date and end_date as current academic year date
     *
     * @param int $personId
     * @param int $organizationId
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int|null $excludeOfficeHoursSeriesId
     * @param int|null $excludeOfficeHourId
     * @return array
     */
    public function getAllOfficeHourSlots($personId, $organizationId, $startDate, $endDate, $excludeOfficeHoursSeriesId = null, $excludeOfficeHourId = null)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'personId' => $personId,
            'slotStartDate' => $startDate->format(SynapseConstant::DEFAULT_DATE_FORMAT),
            'slotEndDate' => $endDate->format(SynapseConstant::DEFAULT_DATE_FORMAT),
            'slotStartTime' => $startDate->format(SynapseConstant::DEFAULT_TIME_FORMAT),
            'slotEndTime' => $endDate->format(SynapseConstant::DEFAULT_TIME_FORMAT),
        ];

        $excludeSeriesId = '';
        if ($excludeOfficeHoursSeriesId) {
            $parameters['excludeOfficeHoursSeriesId'] = $excludeOfficeHoursSeriesId;
            $excludeSeriesId = " AND (
				office_hours_series_id <> :excludeOfficeHoursSeriesId 
				OR office_hours_series_id IS NULL
			)";
        }

        $excludeHourId = '';
        if ($excludeOfficeHourId) {
            $parameters['excludeOfficeHourId'] = $excludeOfficeHourId;
            $excludeHourId = " AND id <> :excludeOfficeHourId";
        }

        $sql = "SELECT
                    slot_start,
                    slot_end
                FROM
                    office_hours
                WHERE
                    person_id = :personId
                        AND organization_id = :organizationId
                        AND (DATE_FORMAT(slot_start, '".SynapseConstant::METADATA_TYPE_DEFAULT_DATE_FORMAT."') >= :slotStartDate
                        AND DATE_FORMAT(slot_end, '".SynapseConstant::METADATA_TYPE_DEFAULT_DATE_FORMAT."') <= :slotEndDate)
                        AND ((DATE_FORMAT(slot_start, '".SynapseConstant::METADATA_TYPE_DEFAULT_TIME_FORMAT."') BETWEEN :slotStartTime AND :slotEndTime)
                        OR (DATE_FORMAT(slot_end, '".SynapseConstant::METADATA_TYPE_DEFAULT_TIME_FORMAT."') BETWEEN :slotStartTime AND :slotEndTime))
                        $excludeSeriesId
                        $excludeHourId
                        AND deleted_at IS NULL";

        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }

    /**
     * Update the deleted_at as the current time for the existing office hour series slots.
     *
     * @param int $officeHoursSeriesId
     * @param string $currentDate
     * @param int $loggedUserId
     * @return bool
     */
    public function removeExistingSlots($officeHoursSeriesId, $currentDate, $loggedUserId)
    {
        $parameters = [
            'officeHoursSeriesId' => $officeHoursSeriesId,
            'currentDate' => $currentDate,
            'deletedBy' => $loggedUserId
        ];

        $sql = "UPDATE office_hours 
                SET 
                    deleted_at = :currentDate,
                    deleted_by = :deletedBy
                WHERE
                    office_hours_series_id = :officeHoursSeriesId
                    AND deleted_at IS NULL";
        $this->executeQueryStatement($sql, $parameters);
        return true;
    }

    /**
     * While updating office hour series, convert the appointments as free standing if their timings are not matching with the new slots
     *
     * @param array|null $freeStandingAppointments
     * @param string $currentTime
     * @param int $loggedUserId
     * @return bool
     */
    public function updateAppointmentAsFreeStanding($freeStandingAppointments, $currentTime, $loggedUserId)
    {
        $parameters = [
            'freeStandingAppointments' => $freeStandingAppointments,
            'currentTime' => $currentTime,
            'modifiedBy' => $loggedUserId
        ];
        $parameterTypes['freeStandingAppointments'] = Connection::PARAM_INT_ARRAY;

        $sql = "UPDATE Appointments 
            SET 
                is_free_standing = 1,
                modified_at = :currentTime,
                modified_by = :modifiedBy
            WHERE
                id IN ( :freeStandingAppointments )";

        $this->executeQueryStatement($sql, $parameters, $parameterTypes);
        return true;
    }

    /**
     * Get the list of free standing appointments.
     *
     * @param int $officeHourSeriesId
     * @param string $currentTime
     * @return array
     */
    public function getFreeStandingAppointments($officeHourSeriesId, $currentTime)
    {
        $freeStandingAppointments = [];
        $parameters = [
            'officeHourSeriesId' => $officeHourSeriesId,
            'currentTime' => $currentTime
        ];

        $sql = "SELECT 
                    DISTINCT a.id
                FROM
                    office_hours o
                        INNER JOIN
                    Appointments a ON o.appointments_id = a.id
                        AND a.deleted_at IS NULL
                WHERE
                    o.deleted_at IS NOT NULL
                        AND o.office_hours_series_id = :officeHourSeriesId
                        AND a.start_date_time > :currentTime
                        AND o.appointments_id IS NOT NULL
                        AND a.id NOT IN (SELECT 
                            oh.appointments_id
                        FROM
                            office_hours oh
                        WHERE
                            oh.office_hours_series_id = :officeHourSeriesId
                                AND oh.deleted_at IS NULL
                                AND oh.appointments_id IS NOT NULL)";

        $results = $this->executeQueryFetchAll($sql, $parameters);
        if ($results) {
            $freeStandingAppointments = array_column($results, 'id');
        }
        return $freeStandingAppointments;
    }


    /**
     * Update all appointments from previous slots to new slots for an office hours series
     *
     * @param int $officeHourSeriesId
     * @param int $organizationId
     * @param string $currentDate
     * @return bool
     */
    public function updateAppointmentForEditedSlots($officeHourSeriesId, $organizationId, $currentDate)
    {
        $parameters = [
            'officeHourSeriesId' => $officeHourSeriesId,
            'currentDate' => $currentDate,
            'organizationId' => $organizationId
        ];

        $sql = "UPDATE office_hours AS oh
                        JOIN
                    (SELECT DISTINCT
                        current_slots.id AS office_hour_id,
                            deleted_slots.appointments_id AS appointments_id
                    FROM
                        office_hours current_slots
                    INNER JOIN office_hours deleted_slots ON (current_slots.office_hours_series_id = deleted_slots.office_hours_series_id
                        AND current_slots.person_id = deleted_slots.person_id
                        AND deleted_slots.deleted_at IS NOT NULL
                        AND current_slots.deleted_at IS NULL)
                    WHERE
                        current_slots.slot_start = deleted_slots.slot_start
                            AND current_slots.slot_end = deleted_slots.slot_end
                            AND current_slots.office_hours_series_id = :officeHourSeriesId
                            AND current_slots.organization_id = :organizationId
                            AND deleted_slots.appointments_id IS NOT NULL
                            AND deleted_slots.deleted_at = :currentDate) AS app_id 
                SET 
                    oh.appointments_id = app_id.appointments_id
                WHERE
                    oh.id = app_id.office_hour_id";

        $this->executeQueryStatement($sql, $parameters);
        return true;
    }

    /**
     * Update passed events status
     *
     * @param array|null $eventIdsArray
     * @param string $currentTime
     * @param bool $updateGoogleAppointmentId
     * @param int|null $loggedUserId
     * @return bool
     */
    public function updateBatchEventsOfficeHourStatus($eventIdsArray, $currentTime, $updateGoogleAppointmentId, $loggedUserId = null)
    {
        $parameters = [
            'eventIdsArray' => $eventIdsArray,
            'currentTime' => $currentTime,
            'modifiedBy' => $loggedUserId
        ];
        $parameterTypes['eventIdsArray'] = Connection::PARAM_INT_ARRAY;

        if ($updateGoogleAppointmentId) {
            $googleAppointmentId = " google_appointment_id = CONCAT('O', id) ";
        } else {
            $googleAppointmentId = " google_appointment_id = NULL ";
        }

        $sql = "UPDATE 
                  office_hours 
                SET 
                    $googleAppointmentId,
                    last_synced = :currentTime,
                    modified_at = :currentTime,
                    modified_by = :modifiedBy
                WHERE
                    id IN (:eventIdsArray)";

        $this->executeQueryStatement($sql, $parameters, $parameterTypes);
        return true;
    }
}