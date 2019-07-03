<?php
namespace Synapse\CoreBundle\Repository;

/**
 * AppointmentsRepository
 */
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Entity\AppointmentRecepientAndStatus;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;
use JMS\Serializer\Tests\Fixtures\Publisher;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;

class AppointmentRecepientAndStatusRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:AppointmentRecepientAndStatus';


    /**
     * Finds entities by a set of criteria.
     * Overriding for editing return type
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return AppointmentRecepientAndStatus[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }


    public function remove(AppointmentRecepientAndStatus $appointmentRASEntity)
    {
        $this->getEntityManager()->remove($appointmentRASEntity);
    }

    public function createAppointmentsRAStatus(AppointmentRecepientAndStatus $appointmentsRAStatus)
    {
        $em = $this->getEntityManager();
        $em->persist($appointmentsRAStatus);
        return $appointmentsRAStatus;
    }

    /**
     * Get participant attendees for an appointment Id provided
     * 
     * @param int $organizationId
     * @param int $personId
     * @param int $appointmentId
     * @param int $orgAcademicYearId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getParticipantAttendeesForAppointment($organizationId, $personId, $appointmentId, $orgAcademicYearId)
    {
        $parameters = [
            'appointmentId' => $appointmentId,
            'facultyId' => $personId,
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $sql ="SELECT 
                    ARS.person_id_student AS student_id,
                    P.firstname AS student_first_name,
                    P.lastname AS student_last_name,
                    ARS.has_attended AS is_attended
                FROM
                    appointment_recepient_and_status AS ARS
                        INNER JOIN
                    org_person_student_year AS OPSY ON ARS.person_id_student = OPSY.person_id
                        INNER JOIN
                    person AS P ON ARS.person_id_student = P.id
                WHERE
                    ARS.appointments_id = :appointmentId
                    AND ARS.person_id_faculty = :facultyId
                    AND ARS.organization_id = :organizationId
                    AND OPSY.organization_id = :organizationId
                    AND OPSY.org_academic_year_id = :orgAcademicYearId
                    AND OPSY.deleted_at IS NULL
                    AND ARS.deleted_at IS NULL
                    AND P.deleted_at IS NULL";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $stmt->fetchAll();
    }

    public function getTotalAppointments($studentId, $orgId, $fromDate, $toDate)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder()
            ->select('IDENTITY(ars.appointments) as id,a.startDateTime as startDate,a.endDateTime as endDate')
            ->from(AppointmentsConstant::APP_RECEPIENT_REPO, 'ars')
            ->LEFTJoin('SynapseCoreBundle:Appointments', 'a', \Doctrine\ORM\Query\Expr\Join::WITH, 'ars.appointments = a.id')
            ->where(AppointmentsConstant::PERSON_ID_STUDENT_EQUALS_STUDENT_ID)
            ->andWhere('ars.organization = :orgId')
            ->andWhere('a.startDateTime >= :fromDate')
            ->andWhere('a.startDateTime <= :toDate')
            ->setParameters(array(
            AppointmentsConstant::STUDENT_ID => $studentId,
            AppointmentsConstant::ORG_ID => $orgId,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ))
        ->orderBy('a.startDateTime', 'ASC')
        ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    /**
     * Method is used to return count of upcoming appointments
     * @param $studentId
     * @param $orgId
     * @param $startDateTime
     * @param $staffId
     * @param $sharingViewAccess
     * @return int $resultCount
     */
    public function getTotalUpcomingAppointments($studentId, $orgId, $startDateTime, $staffId, $sharingViewAccess)
    {
        $resultCount = 0;
        $parameters = ['studentId' => $studentId,
                        'orgId' => $orgId,
                        'startDateTime' => $startDateTime,
                        'staffId' => $staffId,
                        'sharingViewAccessTeam' => $sharingViewAccess['team_view'],
                        'sharingViewAccessPublic' => $sharingViewAccess['public_view'],

        ];
       $sql =  'SELECT 
    COUNT(DISTINCT ars.appointments_id) AS appointmentCount
FROM
    appointment_recepient_and_status ars
        LEFT JOIN
    Appointments a ON (ars.appointments_id = a.id)
        AND (a.deleted_at IS NULL)
		LEFT JOIN
    appointments_teams AS at ON a.id = at.appointments_id
WHERE
    (ars.person_id_student = :studentId        
        AND ars.organization_id = :orgId
        AND a.start_date_time >= :startDateTime)
        AND (ars.deleted_at IS NULL)
		AND (CASE
        WHEN
            a.access_team = 1 AND :sharingViewAccessTeam = 1
        THEN
            at.teams_id IN (SELECT 
                    teams_id
                FROM
                    team_members
                WHERE
                    person_id = :staffId)
        ELSE CASE
            WHEN a.access_private = 1 
              THEN ars.person_id_faculty = :staffId
            ELSE a.access_public = 1 AND :sharingViewAccessPublic = 1
            OR ars.person_id_faculty = :staffId
        END
    END OR ars.person_id_faculty = :staffId)';

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $resultSet = $stmt->fetchAll();

        if(count($resultSet) > 0) {
            $resultCount = $resultSet[0]['appointmentCount'];
        }       
        
        return $resultCount;
    }


    public function getStudentsUpcomingAppointments($studentId, $currentDate)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $param[AppointmentsConstant::STUDENT_ID] = $studentId;
        $param['curDate'] = $currentDate;
        $qb->select('a.id as appointment_id, oh.id as officeHoursId, IDENTITY(a.organization) as organizationId, a.startDateTime, a.endDateTime, a.location, a.description, a.title as reason, IDENTITY(a.activityCategory) as reasonId, org.tier, orglang.organizationName, IDENTITY(ars.personIdFaculty) as personId, p.firstname, p.lastname, p.title');
        $qb->from(AppointmentsConstant::APP_RECEPIENT_REPO, 'ars');
        $qb->Join(AppointmentsConstant::APPOINTMENT_REPO, 'a', \Doctrine\ORM\Query\Expr\Join::WITH, 'a.id = ars.appointments');
        $qb->LEFTJoin(AppointmentsConstant::OFFICEHOURS_REPO, 'oh', \Doctrine\ORM\Query\Expr\Join::WITH, 'oh.appointments = a.id');
        $qb->LEFTJoin('SynapseCoreBundle:Organization', 'org', \Doctrine\ORM\Query\Expr\Join::WITH, 'org.id = a.organization');
        $qb->LEFTJoin('SynapseCoreBundle:OrganizationLang', 'orglang', \Doctrine\ORM\Query\Expr\Join::WITH, 'orglang.organization = org.id');
        $qb->LEFTJoin('SynapseCoreBundle:Person', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = ars.personIdFaculty');
        $qb->where(AppointmentsConstant::PERSON_ID_STUDENT_EQUALS_STUDENT_ID);
        $qb->andWhere('a.startDateTime >= :curDate');
        $qb->setParameters($param);
        $qb->orderBy('a.startDateTime', 'asc');
        $query = $qb->getQuery();
        $resultSet = $query->getArrayResult();
        return $resultSet;
    }


    public function getAttendeesWithHistory($appointmentId)
    {
        /*
         * Since in the Alert notification will may have history of softdelted appointment notifications, 
         * this Repo function is plain SQL to get the soft deleted appointment recepients 
         */
        $em = $this->getEntityManager();
        $sql = "select ars.id as arsId, ars.person_id_student, ars.person_id_faculty, pf.firstname as facultyFirstname, pf.lastname as facultyLastname, ps.firstname, ps.lastname from appointment_recepient_and_status ars 
            join person pf on pf.id = ars.person_id_faculty join person ps on ps.id = ars.person_id_student where ars.appointments_id = ".$appointmentId;
        
        $resultSet = $em->getConnection()->fetchAll($sql);
        
        return $resultSet;
    }
    
    public function getAppointmentFaculty($appointmentId){
        $em = $this->getEntityManager();
        $faculty = null;
        $qb = $em->createQueryBuilder()
        ->select('IDENTITY(m.personIdStudent) as student_id', 'IDENTITY(m.personIdFaculty) as faculty_id')
        ->from(AppointmentsConstant::APP_RECEPIENT_REPO, 'm')        
        ->where('m.appointments = :id')        
        ->setParameters(array(
            'id' => $appointmentId
        ))
        ->getQuery();
        $resultSet = $qb->getResult();
        if($resultSet){
            $faculty = $resultSet[0]['faculty_id'];
        }        
        return $faculty;
        
    }

    /**
     * Checks if appointments overlap with the startDate to endDate timeframe. Returns true if an appointment exists within that timeframe, and false if not.
     *
     * @param int $organizationId
     * @param int $userId - The user that is being booked. If a faculty is creating the appointment, this will be the student, and vice versa.
     * @param string $startDate - 'yyyy-mm-dd HH:mm:ss'
     * @param string $endDate - 'yyyy-mm-dd HH:mm:ss'
     * @param boolean $appointmentCreatorIsFaculty - True if user booking appointment is a faculty, false if user is a student.
     * @param int|null $appointmentIdToExclude - appointment ID to exclude from the overlap check
     * @return boolean
     * @throws SynapseDatabaseException
     */
    public function doAppointmentsExistWithinTimeframe($organizationId, $userId, $startDate, $endDate, $appointmentCreatorIsFaculty = false, $appointmentIdToExclude = null)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'userId' => $userId,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        if ($appointmentCreatorIsFaculty) {
            $userTypeCondition = " AND aras.person_id_student = :userId ";
        } else {
            $userTypeCondition = " AND aras.person_id_faculty = :userId ";
        }

        if ($appointmentIdToExclude) {
            $excludeAppointmentIdCondition = " AND a.id <> :appointmentId ";
            $parameters['appointmentId'] = $appointmentIdToExclude;
        } else {
            $excludeAppointmentIdCondition = "";
        }

        $sql = "
            SELECT
                a.id AS appointments_id
            FROM
                Appointments a
                  JOIN
                appointment_recepient_and_status aras
                    ON a.organization_id = aras.organization_id
                    AND a.id = aras.appointments_id
            WHERE
                a.deleted_at IS NULL
                AND aras.deleted_at IS NULL
                AND a.organization_id = :organizationId
                $userTypeCondition
                $excludeAppointmentIdCondition
                AND (a.end_date_time > :startDate AND a.start_date_time < :endDate);
        ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
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
}