<?php
namespace Synapse\CoreBundle\Repository;

/**
 * AppointmentsRepository
 */
use Doctrine\DBAL\Connection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Synapse\CoreBundle\Entity\Appointments;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RestBundle\Entity\AppointmentsDto;
use Synapse\RestBundle\Exception\ValidationException;



class AppointmentsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:Appointments';

    /**
     * @param mixed $id
     * @param int|null|null $lockMode
     * @param int|null|null $lockVersion
     * @return Appointments|null
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }


    public function remove(Appointments $appointmentEntity)
    {
        $this->getEntityManager()->remove($appointmentEntity);
    }

    /**
     *
     * @param AppointmentsDto $appointmentsDto            
     * @return Appointments
     * @throws ValidationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function createAppointments(AppointmentsDto $appointmentsDto)
    {
        $em = $this->getEntityManager();
        $personId = $appointmentsDto->getPersonId();
        
        // Instance for Appointments entity
        $appointments = new Appointments();
        
        /**
         * Getting Person Object - personId
         */
        $dql = <<<DQL
SELECT partial p.{id}
FROM SynapseCoreBundle:Person p
JOIN SynapseCoreBundle:Organization o WITH o=p.organization
WHERE p=:personId
DQL;
        
        $query = $em->createQuery($dql)->setParameter('personId', $personId);
        // echo "Person $personId"; exit;
        try {
            $person = $query->getSingleResult();
        } catch (\Exception $e) {
            throw new ValidationException([
                AppointmentsConstant::PERSON_NOT_FOUND
            ], AppointmentsConstant::PERSON_NOT_FOUND, AppointmentsConstant::PERSON_NOT_FOUND_KEY);
        }
        
        $organization = $person->getOrganization();
        
        // Validating referencing values are available
        if (! isset($person)) {
            throw new ValidationException([
                AppointmentsConstant::PERSON_NOT_FOUND
            ], AppointmentsConstant::PERSON_NOT_FOUND, AppointmentsConstant::PERSON_NOT_FOUND_KEY);
        }
        
      /*  $timezone = $organization->getTimezone();
        $timezone = $em->getRepository('SynapseCoreBundle:MetadataListValues')->findByListName($timezone);
        if ($timezone) {
            $timezone = $timezone[0]->getListValue();
        }*/
        
        // Call setters to set the Appointments property values
        // $appointments->setPerson($person);
        $appointments->setPerson($em->getReference(Person::class, $personId));
        
        /**
         * Getting Person Object - personProxy
         */
        // $personProxy = ($personIdProxy == 0 ? NULL : $personIdProxy);
        $personIdProxy = $appointmentsDto->getPersonIdProxy();
        if ($personIdProxy) {
            // $personProxy = $this->container->get('person_service')->findPerson($personProxy);
            // $appointments->setPersonIdProxy($personProxy);
            $appointments->setPersonIdProxy($em->getReference(Person::class, $personIdProxy));
        }
        
        // Call setters to set the Appointments property values
        $appointments->setOrganization($organization);
        $appointments->setTitle($appointmentsDto->getDetail());
        
        // $activityCategory = $this->activityRepository->find($appointmentsDto->getDetailId());
        $appointments->setActivityCategory($em->getReference(AppointmentsConstant::ACTIVITY_CATEGORY_REPO, $appointmentsDto->getDetailId()));
        
        $appointments->setLocation($appointmentsDto->getLocation());
        $appointments->setDescription($appointmentsDto->getDescription());
        $appointments->setIsFreeStanding($appointmentsDto->getIsFreeStanding());
        $appointments->setType($appointmentsDto->getType());
        $start = Helper::getUtcDate($appointmentsDto->getSlotStart());
        $end = Helper::getUtcDate($appointmentsDto->getSlotEnd());
        //$start = $appointmentsDto->getSlotStart();
        //$end = $appointmentsDto->getSlotEnd();
        
        $appointments->setStartDateTime($start);
        $appointments->setEndDateTime($end);
        $appointments->setSource('S');
        if(!empty($appointmentsDto->getShareOptions()))
        {
            $appointments->setAccessPrivate($appointmentsDto->getShareOptions()[0]->getPrivateShare());
            $appointments->setAccessPublic($appointmentsDto->getShareOptions()[0]->getPublicShare());        
            $appointments->setAccessTeam($appointmentsDto->getShareOptions()[0]->getTeamsShare());
        }
        try {
            $em->persist($appointments);
            $em->flush();
        } catch (\Exception $e) {
            // if (strpos($e->getMessage(), 'constraint fails') !== false && strpos($e->getMessage(), 'person_id_proxy') !== false)
            if ((strpos($e->getMessage(), 'constraint fails')) && (strpos($e->getMessage(), 'person_id_proxy'))) {
                throw new ValidationException([
                    'PersonIdProxy Not Found.'
                ], 'PersonIdProxy Not Found.', AppointmentsConstant::PERSON_NOT_FOUND_KEY);
            } else {
                throw $e;
            }
        }
        return $appointments;
    }

    public function viewAppointment($orgId, $appointmentId)
    {
        $em = $this->getEntityManager();
        
        $qb = $em->createQueryBuilder()
            ->select('s.id as appointment_id','IDENTITY(s.personIdProxy) as person_id_proxy',  'IDENTITY(s.person) as person_id', 'IDENTITY(s.organization) as organization_id', 'o.id as office_hours_id', 's.title as detail', 's.startDateTime as slot_start', 's.endDateTime as slot_end', 's.type as type', 's.location', 's.isFreeStanding as is_free_standing', 'IDENTITY(s.activityCategory) as detail_id', 's.description as description', 'IDENTITY(m.personIdStudent)', 'IDENTITY(m.personIdFaculty) as faculty_id', 's.accessPrivate as access_private', 's.accessPublic as access_public', 's.accessTeam as access_team', 's.googleAppointmentId as google_appointment_id')
            ->from(AppointmentsConstant::APPOINTMENT_REPO, 's')
            ->LEFTJoin(AppointmentsConstant::APP_RECEPIENT_REPO, 'm', \Doctrine\ORM\Query\Expr\Join::WITH, 'm.appointments = s.id')
            ->LEFTJoin('SynapseCoreBundle:OfficeHours', 'o', \Doctrine\ORM\Query\Expr\Join::WITH, 'o.appointments = s.id')
            ->where('s.id = :id')
            ->andWhere('s.organization = :orgId')
            ->setParameters(array(
            'id' => $appointmentId,
            'orgId' => $orgId
        ))
            ->groupBy('s.id')
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet[0];
    }

    public function getAppointmentsByDate(Person $person, $from, $to)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('a');
        $qb->from(AppointmentsConstant::APPOINTMENT_REPO, 'a');
        // $qb->join
        
        $qb->where('a.person = :person AND a.startDateTime >= :fromdate  AND a.endDateTime <= :todate');
        $qb->setParameters([
            'person' => $person,
            'fromdate' => $from,
            'todate' => $to
        ]);
        
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function viewTodayAppointment($startDateTime, $endDateTime, $personId, $orgId, $academicStartDate = null,$academicEndDate = null)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('AP.id as appointment_id', 'AC.id as reason_id', 'AC.shortName as reason_text', 'AP.startDateTime as appointment_start', 'AP.endDateTime as appointment_end')
            ->from(AppointmentsConstant::APPOINTMENT_REPO, 'AP')
            ->LEFTJoin(AppointmentsConstant::ACTIVITY_CATEGORY_REPO, 'AC', \Doctrine\ORM\Query\Expr\Join::WITH, 'AP.activityCategory = AC.id')            
            ->LEFTJoin(AppointmentsConstant::APP_RECEPIENT_REPO, 'ARS', \Doctrine\ORM\Query\Expr\Join::WITH, 'ARS.appointments = AP.id')
            ->where('ARS.personIdFaculty = :personIdFaculty')
            ->andWhere('AP.organization = :OrgId')
            ->andWhere('AP.startDateTime >= :startDateTime')
            ->andWhere('AP.startDateTime <= :endDateTime');
    
        if(!is_null($academicStartDate) and !is_null($academicEndDate)){
            $qb->andWhere('AP.startDateTime BETWEEN :startDate AND :endDate');
            $qb->setParameters(array(
                'personIdFaculty' => $personId,
                'OrgId' => $orgId,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'startDate' => $academicStartDate,
                'endDate' => $academicEndDate
            ));
        }else{
               $qb->setParameters(array(
                'personIdFaculty' => $personId,
                'OrgId' => $orgId,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime   
            ));
        }
        $qb->groupBy('appointment_id');
        $qb->orderBy('AP.startDateTime', 'asc');        
        $qb = $qb->getQuery();     
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function getStudentAppointments($studentId, $orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select(' A.id as activity_id,
                    A.createdAt as  activity_date,
                    IDENTITY(ARS.personIdFaculty) as activity_created_by_id ,
                    P.firstname as activity_created_by_first_name,
                    P.lastname as activity_created_by_last_name,
                    AC.id as activity_reason_id,
                    AC.shortName as activity_reason_text,
                    A.description  as activity_description')
            ->from(AppointmentsConstant::APP_RECEPIENT_REPO, 'ARS')
            ->LEFTJoin(AppointmentsConstant::APPOINTMENT_REPO, 'A', \Doctrine\ORM\Query\Expr\Join::WITH, 'ARS.appointments = A.id')
            ->LEFTJoin('SynapseCoreBundle:Person', 'P', \Doctrine\ORM\Query\Expr\Join::WITH, 'A.person = P.id')
            ->LEFTJoin(AppointmentsConstant::ACTIVITY_CATEGORY_REPO, 'AC', \Doctrine\ORM\Query\Expr\Join::WITH, 'A.activityCategory = AC.id')
            ->where('ARS.personIdStudent = :studentId')
            ->andWhere('A.organization = :orgId')
            ->orderBy('A.createdAt', 'desc')
            ->setParameters(array(
            'studentId' => $studentId,
            'orgId' => $orgId
        ))
            ->getQuery();
        
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function getAppointmentList($appId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('aprs')
            ->from(AppointmentsConstant::APPOINTMENT_REPO, 'ap')
            ->join(AppointmentsConstant::APP_RECEPIENT_REPO, 'aprs', \Doctrine\ORM\Query\Expr\Join::WITH, 'aprs.appointments = ap.id')
            ->where('aprs.appointments = :id')
            ->setParameters(array(
            'id' => $appId
        ))
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function createAppointment($appointments)
    {
        $em = $this->getEntityManager();
        $em->persist($appointments);
        return $appointments;
    }

    /**
     * Get the list of future appointments which has to be synced in external calendar
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param string $startDate
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getFutureAppointmentForFaculty($organizationId, $facultyId, $startDate)
    {

        $parameters = [
            'orgId' => $organizationId,
            'facultyId' => $facultyId,
            'startDate' => $startDate
        ];

        $sql = "SELECT 
					aras.appointments_id as appointment_id
				FROM
					Appointments a
						INNER JOIN
					appointment_recepient_and_status aras ON (aras.appointments_id = a.id
						AND aras.person_id_faculty = :facultyId)
				WHERE
					a.organization_id = :orgId
						AND a.start_date_time >= :startDate						
						AND a.deleted_at IS NULL
						AND aras.deleted_at IS NULL
				ORDER BY a.start_date_time";

        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }

    /**
     * Get the list of future appointments which are synced in external calendar
     *
     * @param int $organizationId
     * @param int $facultyId
     * @return Appointments[]
     * @throws SynapseDatabaseException
     */
    public function getSyncedMafAppointments($organizationId, $facultyId)
    {

        $sql = "SELECT aras.appointments_id AS appointment_id
				FROM Appointments a
				INNER JOIN appointment_recepient_and_status aras 
				    ON (aras.appointments_id = a.id	AND aras.person_id_faculty = :facultyId)
				WHERE
				    a.organization_id = :orgId
				    AND a.deleted_at IS NULL
				    AND a.google_appointment_id IS NOT NULL
				    AND aras.deleted_at IS NULL";

        $parameters = ['orgId' => $organizationId,
            'facultyId' => $facultyId
        ];
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
    
    public function getUpcomingAppointments($person)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $currentDate = new \DateTime('now');
        $qb->select('a');
        $qb->from(AppointmentsConstant::APPOINTMENT_REPO, 'a');
        // $qb->join
    
        $qb->where('a.person = :person AND a.startDateTime >= :fromdate');
        $qb->setParameters([
            'person' => $person,
            'fromdate' => $currentDate
            ]);
    
        $query = $qb->getQuery();
        return $query->getResult();
    }
    
    public function getUpcomingAppointmentsStudentCreated($person)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $currentDate = new \DateTime('now');
        $qb->select('ar');
        $qb->from(AppointmentsConstant::APP_RECEPIENT_REPO, 'ar');
        $qb->join(AppointmentsConstant::APPOINTMENT_REPO, 'a', \Doctrine\ORM\Query\Expr\Join::WITH, 'ar.appointments = a.id');
    
        $qb->where('ar.personIdFaculty = :person AND a.startDateTime >= :fromdate');
        $qb->setParameters([
            'person' => $person,
            'fromdate' => $currentDate
            ]);
    
        $query = $qb->getQuery();
        return $query->getResult();
    }

    /**
     * Get the list of future appointments which has modified in mapworks
     *
     * @param int $organizationId
     * @param int $facultyId
     * @param string $startDate
     * @param string $modifiedTime
     * @param bool $isSyncWithExistingProvider
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getFutureAppointmentsModified($organizationId, $facultyId, $startDate, $modifiedTime, $isSyncWithExistingProvider = true)
    {
        $condition = '';
        if($isSyncWithExistingProvider){
            $condition = " AND a.last_synced <= :modifiedTime 
                           AND (a.modified_at >= :modifiedTime OR aras.modified_at >= :modifiedTime) ";
            $parameters = [
                'orgId' => $organizationId,
                'facultyId' => $facultyId,
                'startDate' => $startDate,
                'modifiedTime' => $modifiedTime
            ];
        } else {
            $parameters = [
                'orgId' => $organizationId,
                'facultyId' => $facultyId,
                'startDate' => $startDate
            ];
        }
        $sql = "SELECT 
					aras.appointments_id AS appointment_id
				FROM
					Appointments a
						INNER JOIN
					appointment_recepient_and_status aras ON (aras.appointments_id = a.id
						AND aras.person_id_faculty = :facultyId)
				WHERE
					a.organization_id = :orgId
						AND a.start_date_time >= :startDate
						$condition
						AND a.deleted_at IS NULL";

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
     * Get the list of future appointments which was deleted in mapworks
     *
     * @param int $organizationId
     * @param int $personId
     * @param string $startTime
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getDeletedFutureAppointments($organizationId, $personId, $startTime)
    {
        $parameters = [
            'orgId' => $organizationId,
            'facultyId' => $personId,
            'startDate' => $startTime
        ];
        $sql = "SELECT 
					aras.appointments_id AS id,
					a.google_appointment_id as google_appointment_id
				FROM
					Appointments a
						INNER JOIN
					appointment_recepient_and_status aras ON (aras.appointments_id = a.id
						AND aras.person_id_faculty = :facultyId)
				WHERE
						a.organization_id = :orgId
						AND a.start_date_time >= :startDate					
						AND a.google_appointment_id IS NOT NULL
						AND a.deleted_at IS NOT NULL";

        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }

    /**
     * Function to validate any appointments are overlapped with the given time frame,
     * it will return true if it is overlapped otherwise false will be return.
     *
     * @param int $facultyId
     * @param int $organizationId
     * @param string $startDate
     * @param string $endDate
     * @param null|int $excludeAppointmentId
     * @return bool
     * @throws SynapseDatabaseException
     */
    public function isOverlappingAppointments($facultyId, $organizationId, $startDate, $endDate, $excludeAppointmentId = null)
    {
        $parameters = [
            'personId' => $facultyId,
            'organizationId' => $organizationId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        if (!is_null($excludeAppointmentId)) {
            $excludeCondition = ' AND a.id != :excludeAppointmentId ';
            $parameters['excludeAppointmentId'] = $excludeAppointmentId;
        } else {
            $excludeCondition = '';
        }
        $sql = "
            SELECT
                a.id
            FROM
                appointment_recepient_and_status aras
                	LEFT JOIN
                Appointments a ON aras.organization_id = a.organization_id
                	AND aras.appointments_id = a.id
                	AND a.deleted_at IS NULL
            WHERE
                aras.person_id_faculty = :personId
                AND aras.organization_id = :organizationId
                $excludeCondition
                AND (a.end_date_time > :startDate AND  a.start_date_time < :endDate)
                AND aras.deleted_at IS NULL;
        ";
        try {
            $em = $this->getEntityManager();
            $statement = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $records = $statement->fetchAll();

        if (empty($records)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * UPDATE Appointments table with external calendar sync details.
     *
     * @param array $deletedIds
     * @param bool $updateGoogleAppointmentId
     * @param int|NULL $personId
     * @return void
     * @throws SynapseDatabaseException
     */
    public function updateSyncDetailsToAppointment($deletedIds, $updateGoogleAppointmentId = false, $personId = NULL)
    {
        $parameters = [
            'id' => $deletedIds,
            'personId' => $personId
        ];
        $parameterTypes['id'] = Connection::PARAM_INT_ARRAY;

        if ($updateGoogleAppointmentId) {
            $googleAppointmentId = " google_appointment_id = CONCAT('A', id) ";
        } else {
            $googleAppointmentId = " google_appointment_id = NULL ";
        }

        $sql = "UPDATE 
                  Appointments 
                SET 
                    $googleAppointmentId,
                    last_synced = NOW(),
                    modified_by = :personId,
                    modified_at = NOW()
                WHERE
                    id IN (:id)";
        $this->executeQueryStatement($sql, $parameters, $parameterTypes);
    }
}
