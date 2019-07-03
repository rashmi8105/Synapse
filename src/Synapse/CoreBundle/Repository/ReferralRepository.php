<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\Referrals;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\RestBundle\Exception\ValidationException;


class ReferralRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:Referrals';

    const COUNT_RID = 'count(r.id)';

    const FIELD_PERSON = 'person';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    private $referralEntity = "SynapseCoreBundle:Referrals";
    
    const ORG_ID = 'orgId';


    /**
     * Finds an entity by its primary key / identifier.
     * Override added to inform PhpStorm about the return type.
     *
     * @param mixed $id The identifier.
     * @param SynapseException $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                              or NULL if no specific lock mode should be used
     *                              during the search.
     * @param int|null $lockVersion The lock version.
     *
     * @return Referrals|null
     */
    public function find($id, $exception = null,  $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * Finds an entity based on the passed in criteria. If not found, throws the passed in exception.
     *
     * @param array $criteria
     * @param SynapseException|null $exception
     * @param array|null $orderBy
     * @return null|Referrals
     * @throws \Exception
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $personEntity = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($personEntity, $exception);
    }


    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param
     *
     * @return Referrals[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $objectArray = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($objectArray, $exception);
    }




    public function createReferral($referrals)
    {
        $em = $this->getEntityManager();
        $em->persist($referrals);
        return $referrals;
    }

    public function removeReferrals($referrals)
    {
        $em = $this->getEntityManager();
        $em->remove($referrals);
        return $referrals;
    }

    public function getReceivedReferralCount(Person $person)
    {
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select(self::COUNT_RID)
            ->from($this->referralEntity, 'r')
            ->where('r.personAssignedTo = :person')
            ->setParameters(array(
            self::FIELD_PERSON => $person
        ))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getReceivedOpenReferralCount(Person $person)
    {
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select(self::COUNT_RID)
            ->from($this->referralEntity, 'r')
            ->where('r.personAssignedTo = :person')
            ->andWhere('r.status = :openstatus OR r.status = :reopenstatus')
            ->setParameters(array(
            self::FIELD_PERSON => $person,
            'openstatus' => 'O',
            'reopenstatus' => 'R'
        ))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getSentReferralCount(Person $person, $academicStart=null, $academicEnd=null, $students)
    {
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select(self::COUNT_RID)
            ->from($this->referralEntity, 'r')
            ->where('r.personFaculty = :person')
            ->andWhere('r.referralDate BETWEEN :startDate AND :endDate')
            ->andWhere('r.personStudent IN (:students)')
            ->setParameters(array(
            self::FIELD_PERSON => $person,
                'startDate'=>$academicStart,
                'endDate'=>$academicEnd,
                'students'=>$students
        ))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getSentOpenReferralCount(Person $person, $academicStart=null, $academicEnd=null, $students)
    {
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select(self::COUNT_RID)
            ->from($this->referralEntity, 'r')
            ->where('r.personFaculty = :person')
            ->andWhere('r.status = :openstatus OR r.status = :reopenstatus')
            ->andWhere('r.referralDate BETWEEN :startDate AND :endDate')
            ->andWhere('r.personStudent IN (:students)')
            ->setParameters(array(
            self::FIELD_PERSON => $person,
            'openstatus' => 'O',
            'reopenstatus' => 'R',
            'startDate'=>$academicStart,
            'endDate'=>$academicEnd,
            'students'=>$students
        ))
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getStudentReferral($studentId, $orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select(' R.id as activity_id,
                R.createdAt as  activity_date,
                IDENTITY(R.personFaculty) as activity_created_by_id ,
                P.firstname as activity_created_by_first_name,
                P.lastname as activity_created_by_last_name,
                AC.id as activity_reason_id,
                AC.shortName as activity_reason_text,
                R.note as activity_description,
                R.status as activity_referral_status')
            ->from('SynapseCoreBundle:Referrals', 'R')
            ->LEFTJoin('SynapseCoreBundle:Person', 'P', \Doctrine\ORM\Query\Expr\Join::WITH, 'R.personFaculty = P.id')
            ->LEFTJoin('SynapseCoreBundle:ActivityCategory', 'AC', \Doctrine\ORM\Query\Expr\Join::WITH, 'R.activityCategory = AC.id')
            ->where('R.personStudent = :studentId')
            ->andWhere('R.organization = :orgId')
            ->orderBy('R.createdAt', 'desc')
            ->setParameters(array(
            'studentId' => $studentId,
            self::ORG_ID => $orgId
        ))
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }


    /**
     * Gets referrals assigned to the user. If only open referrals are desired, set $status='open'
     *
     * @param int $personId
     * @param int $organizationId
     * @param array $studentIds
     * @param int $orgAcademicYearId
     * @param string $status - Set to "open" for returning only open referrals, otherwise not needed
     * @param string $startDate - Date string formatted as 'yyyy-mm-dd'
     * @param string $endDate - Date string formatted as 'yyyy-mm-dd'
     * @param int|null $numberOfRecords
     * @param int|null $offset
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getReferralsAssignedToUser($personId, $organizationId, $studentIds, $orgAcademicYearId, $status = null, $startDate = null, $endDate = null, $numberOfRecords = null, $offset = null)
    {
        $parameters = [
            'person' => $personId,
            'orgId' => $organizationId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'studentIds' => $studentIds,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $parameterTypes = [
            'studentIds' => Connection::PARAM_INT_ARRAY
        ];

        if ($status == 'open') {
            $referralStatusCondition = "AND r.status = 'O'";
        } else {
            $referralStatusCondition = '';
        }

        if (is_null($startDate) && is_null($endDate)) {
            $referralDateCondition = '';
        } else {
            $referralDateCondition = ' AND r.referral_date BETWEEN :startDate AND :endDate';
        }

        if ( $numberOfRecords > 0 ) {
            $limitString = " LIMIT :numberOfRecords OFFSET :offset";
            $parameters['numberOfRecords'] = (int) $numberOfRecords;
            $parameters['offset'] = $offset;
            $parameterTypes['numberOfRecords'] = 'integer';
            $parameterTypes['offset'] = 'integer';
        } else {
            $limitString = '';
        }

        $sql = "SELECT DISTINCT
                    r.id AS referral_id,
                    pf.firstname AS referred_by_first_name,
                    pf.lastname AS referred_by_last_name,
                    pf.id AS referred_by_id,
                    r.referral_date AS referral_date,
                    ps.id AS student_id,
                    ps.firstname AS student_first_name,
                    ps.lastname AS student_last_name,
                    ac.id AS reason_id,
                    ac.short_name AS description,
                    opsy.is_active AS status
                FROM
                    referrals r
                        INNER JOIN
                    person pf
                            ON r.person_id_faculty = pf.id
                        INNER JOIN
                    person ps
                            ON r.person_id_student = ps.id
                        INNER JOIN
                    activity_category ac
                            ON r.activity_category_id = ac.id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ps.id
                        LEFT JOIN
                    organization_role orgr
                            ON r.organization_id = orgr.organization_id
                        LEFT JOIN
                    role_lang rl
                            ON orgr.role_id = rl.role_id
                WHERE
                    (
                        r.person_id_assigned_to = :person
                        OR (orgr.person_id = :person
                            AND r.person_id_assigned_to IS NULL
                            AND rl.role_name = 'Primary coordinator')
                    )
                    $referralDateCondition
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND r.person_id_student IN (:studentIds)
                    AND r.organization_id = :orgId
                    $referralStatusCondition
                    AND r.deleted_at IS NULL
                    AND pf.deleted_at IS NULL
                    AND ps.deleted_at IS NULL
                    AND ac.deleted_at IS NULL
                    AND opsy.deleted_at is NULL
                    AND orgr.deleted_at IS NULL
                    AND rl.deleted_at IS NULL
                ORDER BY r.referral_date DESC, ps.lastname ASC, ps.firstname ASC
                $limitString;";

        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $referrals = $stmt->fetchAll();
            return $referrals;

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    /**
     * Gets referrals assigned to the user. If only open referrals are desired, set $status='open'
     *
     * @param int $personId
     * @param int $organizationId
     * @param array $studentIds
     * @param int $orgAcademicYearId
     * @param string $status - Set to "open" for returning only open referrals, otherwise not needed
     * @param string $startDate - Date string formatted as 'yyyy-mm-dd'
     * @param string $endDate - Date string formatted as 'yyyy-mm-dd'
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getCountOfReferralsAssignedToUser($personId, $organizationId, $studentIds, $orgAcademicYearId, $status = null, $startDate = null, $endDate = null)
    {
        $parameters = [
            'person' => $personId,
            'orgId' => $organizationId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'studentIds' => $studentIds,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $paramType = [
            'studentIds' => Connection::PARAM_INT_ARRAY
        ];

        if ($status == 'open') {
            $referralStatusCondition = "AND r.status = 'O'";
        } else {
            $referralStatusCondition = '';
        }

        if (is_null($startDate) && is_null($endDate)) {
            $referralDateCondition = '';
        } else {
            $referralDateCondition = ' AND r.referral_date BETWEEN :startDate AND :endDate';
        }

        $sql = "SELECT
                    COUNT(DISTINCT r.id) AS total_referrals
                FROM
                    referrals r
                        INNER JOIN
                    person pf
                            ON r.person_id_faculty = pf.id
                        INNER JOIN
                    person ps
                            ON r.person_id_student = ps.id
                        INNER JOIN
                    activity_category ac
                            ON r.activity_category_id = ac.id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ps.id
                        LEFT JOIN
                    organization_role orgr
                            ON r.organization_id = orgr.organization_id
                        LEFT JOIN
                    role_lang rl
                            ON orgr.role_id = rl.role_id
                WHERE
                    (
                        r.person_id_assigned_to = :person
                        OR (orgr.person_id = :person
                            AND r.person_id_assigned_to IS NULL
                            AND rl.role_name = 'Primary coordinator')
                    )
                    $referralDateCondition
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND r.person_id_student IN (:studentIds)
                    AND r.organization_id = :orgId
                    $referralStatusCondition
                    AND r.deleted_at IS NULL
                    AND pf.deleted_at IS NULL
                    AND ps.deleted_at IS NULL
                    AND ac.deleted_at IS NULL
                    AND opsy.deleted_at is NULL
                    AND orgr.deleted_at IS NULL
                    AND rl.deleted_at IS NULL;";

        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters, $paramType);
            $referrals = $stmt->fetchAll();
            return $referrals;

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    public function getSentReferralByPerson($query)
    {
        $em = $this->getEntityManager();
        $resultSet = $em->getConnection()->fetchAll($query);
        
        return $resultSet;
    }

    public function getReceviedReferralByPerson($query)
    {
        $em = $this->getEntityManager();
        $resultSet = $em->getConnection()->fetchAll($query);
        
        return $resultSet;
    }

    public function getStudentOpenReferrals($userId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r as referral', 'orgl.organizationName as org_name', 'IDENTITY(orgl.lang) as langId');
        $qb->from($this->referralEntity, 'r');
        $qb->join("SynapseCoreBundle:Organization", 'org', \Doctrine\ORM\Query\Expr\Join::WITH, "org.id=r.organization");
        $qb->join("SynapseCoreBundle:OrganizationLang", 'orgl', \Doctrine\ORM\Query\Expr\Join::WITH, "org.id=orgl.organization");
        $qb->where('r.personStudent = :student');
        $qb->andWhere('r.status = :status');
        //$qb->andWhere('(r.notifyStudent =1 and r.accessPrivate =1) or ((r.notifyStudent is NULL or r.notifyStudent=1) and r.accessPrivate =0)');
        $qb->andWhere('r.notifyStudent =1');
        $qb->setParameters(array(
            'student' => $userId,
            'status' => 'O'
        ));        
        $qb->orderBy('r.referralDate', 'DESC');
        $db = $qb->getQuery();
 
        $resultSet = $db->getResult();
        return $resultSet;
    }

    /**
     * Written flat queries because CASE was not supporting in doctorine
     * 
     * @return multitype:
     */
    public function getFacultyStudentReferral($facultyId, $studentId, $orgId, $sharingViewAccess, $sharingViewAccessRR, $referralStatus = NULL)
    {       
        $em = $this->getEntityManager();
        $status = '';
        if ($referralStatus == 'O') {
            $status = 'AND R.status = "O"';
        }
        
        $sql = "
        select 
            R . *
        from
            referrals as R
                LEFT join
            referrals_teams as RT ON R.id = RT.referrals_id
                LEFT JOIN
            organization_role orgr ON (R.organization_id = orgr.organization_id)
        where
            R.person_id_student = $studentId
                AND R.organization_id = $orgId
                AND R.deleted_at is NULL
                " . $status . "
                AND 
        	(	
        		(CASE
        			WHEN
        				R.access_team = 1
        					AND ((" . $sharingViewAccess['team_view'] . " = 1 and R.is_reason_routed = 0)
        					OR (" . $sharingViewAccessRR['team_view'] . " = 1 and R.is_reason_routed = 1))
        			THEN
        				RT.teams_id IN (SELECT 
        						teams_id
        					FROM
        						team_members
        					WHERE
        						person_id = $facultyId
        					AND deleted_at IS NULL)
        			ELSE 
        			CASE
        				WHEN R.access_private = 1 THEN R.person_id_faculty = $facultyId
        				ELSE R.access_public = 1
        					AND ((" . $sharingViewAccess['public_view'] . " = 1 and R.is_reason_routed = 0)
        					OR (" . $sharingViewAccessRR['public_view'] . " = 1 and R.is_reason_routed = 1))
        			END
        		END
        		)
        		
                OR (R.person_id_faculty = $facultyId)
                OR (R.person_id_assigned_to = $facultyId)
        				
        		/* For central coordinator*/
                OR 
        		(
        		orgr.person_id = $facultyId
                AND R.person_id_assigned_to IS NULL
                AND orgr.role_id = 1
        		AND orgr.deleted_at IS NULL
                AND R.person_id_student = $studentId
        		)
        		
        		/* For facutly as interested party w.r.t student*/
                OR 
        		(
        		R.id IN (select 
                    rip.referrals_id
                from
                    referrals_interested_parties as rip
                        left join
                    referrals as R2 ON R2.id = rip.referrals_id
                where
                    rip.person_id = $facultyId
                        and R2.person_id_student = $studentId
                        and rip.deleted_at is null
        				and R2.deleted_at is null
                        AND R2.status = 'O')
        				
        		)
        	)
            group by R.id   
             ";   
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;        
    }
    
    /**
     * To get central coordinator
     * @param unknown $orgId
     * @return multitype:
     */
    public function getCentralCoordId($orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(orl.person) as personId');
        $qb->from('SynapseCoreBundle:OrganizationRole', 'orl');
        $qb->join('SynapseCoreBundle:Role','r', \Doctrine\ORM\Query\Expr\Join::WITH, "r.id=orl.role");
        $qb->join('SynapseCoreBundle:RoleLang','rl', \Doctrine\ORM\Query\Expr\Join::WITH, "r.id=rl.role");
        $qb->where('orl.organization = :organization AND rl.roleName = :rolename');
        $qb->setParameters(array(
            'organization' => $orgId,
            'rolename' => 'Primary coordinator'
        ));
        $db = $qb->getQuery();
        $resultSet = $db->getArrayResult();
        if(count($resultSet) > 0)
        {
            $resultSet = array_column($resultSet, 'personId');
        }else{
            $resultSet = [];
        }   
    
        return $resultSet;
    }
    
    /**
     * Get all intrested parties 
     * @param unknown $refId
     * @return multitype:
     */
    public function getInterstedPartyId($refId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(rp.person) as personId');
        $qb->from('SynapseCoreBundle:ReferralsInterestedParties', 'rp');
         
        $qb->where('rp.referrals = :referral');
        $qb->setParameters(array(
            'referral' => $refId
    
        ));
        $db = $qb->getQuery();
        $resultSet = $db->getArrayResult();
        if(count($resultSet) > 0)
        {
            $resultSet = array_column($resultSet, 'personId');
        }else{
            $resultSet = [];
        }    
    
        return $resultSet;
    }
    
    /**
     * Get all person/central coordinator/ Intrested party array 
     * @param unknown $refId
     * @return multitype:
     */
    public function getAllReferralOwnablePerson($refId)
    {
        $allowedUser = array();
        $users = array();
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r.id as referral_id', 'IDENTITY(r.personFaculty) as personIdFaculty', 'IDENTITY(r.personAssignedTo) as personIdAssignedTo', 'IDENTITY(r.organization) as orgId');
        $qb->from($this->referralEntity, 'r');         
        $qb->where('r.id = :referral');
        $qb->setParameters(array(
            'referral' => $refId    
        ));
        $db = $qb->getQuery();
        $resultSet = $db->getArrayResult();
        $result = $resultSet[0];         
        $interestedParty = $this->getInterstedPartyId($refId);        
        if($result['personIdAssignedTo']){
            $users[] = $result['personIdAssignedTo'];
            $allowedUser = array_merge($users, $interestedParty);            
        }
        else {        
            $users = $this->getCentralCoordId($result[self::ORG_ID]);
            $allowedUser = array_merge($users, $interestedParty);
        }
       
        return $allowedUser;
    }


    /**
     * get part sql to fetch participating student list for a given faculty and organization.
     *
     * @return string
     */
    private function getFacultyStudentsQuery()
    {
        $facultyStudentsQuery = "SELECT DISTINCT
                                    ofspm.student_id
                                FROM
                                    org_faculty_student_permission_map ofspm
                                        INNER JOIN
                                    org_permissionset op ON (op.id = ofspm.permissionset_id
                                        AND op.organization_id = ofspm.org_id)
                                        INNER JOIN
                                    org_person_student_year opsy ON (opsy.person_id = ofspm.student_id
                                        AND opsy.organization_id = ofspm.org_id)
                                        INNER JOIN
                                    org_academic_year oay ON (oay.id = opsy.org_academic_year_id
                                        AND oay.organization_id = ofspm.org_id)
                                WHERE
                                    op.accesslevel_ind_agg = 1
                                        AND ofspm.org_id = :organizationId
                                        AND ofspm.faculty_id = :facultyId
                                        AND (oay.start_date = :academicStartDate
                                        AND oay.end_date = :academicEndDate)
                                        AND ofspm.student_id = r.person_id_student
                                        AND op.deleted_at IS NULL
                                        AND opsy.deleted_at IS NULL
                                        AND oay.deleted_at IS NULL";

        return $facultyStudentsQuery;
    }

    /**
     * Get the sent referral details
     *
     * @param int $userId
     * @param int $organizationId
     * @param string $status
     * @param int $startPoint
     * @param int $offset
     * @param bool $studentListFlag
     * @param string $sortBy
     * @param string $academicStartDate
     * @param string $academicEndDate
     * @param string $primaryCoordinatorName
     * @param bool $isJob
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getSentReferralDetails($userId, $organizationId, $status, $startPoint, $offset, $studentListFlag = false, $sortBy = '', $academicStartDate = null, $academicEndDate = null, $primaryCoordinatorName, $isJob = FALSE)
    {
        try {
            $em = $this->getEntityManager();
            $facultyStudentsQuery = $this->getFacultyStudentsQuery();
            $selectSql = "
                SELECT 
                    SQL_CALC_FOUND_ROWS r.id,
                    r.id AS referral_id,
                    ps.id AS student_id,
                    ps.firstname AS student_first_name,
                    ps.lastname AS student_last_name,                    
                    ci.primary_email AS student_email,
                    ps.external_id AS student_external_id,
                    ac.id AS reason_id,
                    ac.short_name AS reason_text,
                    r.referral_date AS referral_date,
                    pf.firstname AS created_by_first_name,
                    pf.lastname AS created_by_last_name,                    
                    cif.primary_email AS created_by_email,
                    pf.external_id AS created_by_external_id,
                    r.status,
                    COALESCE(CONCAT(pat1.lastname, ',', pat1.firstname),
                            CONCAT(pat2.lastname, ',', pat2.firstname),
                            CONCAT(pat3.lastname, ',', pat3.firstname),
                            '$primaryCoordinatorName') AS assigned_to_name
                FROM
                    referrals r
                        LEFT JOIN
                    person pf ON r.person_id_faculty = pf.id AND r.organization_id = pf.organization_id
                        LEFT JOIN
                    person ps ON r.person_id_student = ps.id AND r.organization_id = ps.organization_id
                        LEFT JOIN
                    referrals_interested_parties rip ON (rip.referrals_id = r.id AND rip.person_id = :personId)
                        LEFT JOIN
                    referral_routing_rules rr ON (rr.activity_category_id = r.activity_category_id
                            AND rr.organization_id = r.organization_id AND rr.organization_id = :organizationId)
                        LEFT JOIN
                    activity_category ac ON rr.activity_category_id = ac.id AND ac.deleted_at IS NULL
                        LEFT JOIN
                    person pat1 ON (pat1.id = r.person_id_assigned_to) AND pat1.deleted_at IS NULL
                        LEFT JOIN
                    person pat2 ON (pat2.id = rr.person_id) AND pat2.deleted_at IS NULL
                        LEFT JOIN
                    person pat3 ON (pat3.id = rip.person_id) AND pat3.deleted_at IS NULL
                        LEFT JOIN
                    person_contact_info pcif ON (pf.id = pcif.person_id)
                        LEFT JOIN
                    contact_info cif ON cif.id = pcif.contact_id
                		LEFT JOIN
                    person_contact_info pcis ON (ps.id = pcis.person_id)
                        LEFT JOIN
                    contact_info ci ON ci.id = pcis.contact_id
                WHERE
                    ((r.person_id_faculty = :facultyId)
                        AND (r.status IN (:status))
                        AND r.organization_id = :organizationId)
                        AND (r.deleted_at IS NULL)
                        AND (pf.deleted_at IS NULL)
                        AND (ps.deleted_at IS NULL)
                        AND (rr.deleted_at IS NULL)";

            $whereClause = "";
            // Added to restrict the data to current academic year
            if (!is_null($academicStartDate) && !is_null($academicEndDate)) {
                $whereClause .= " AND r.referral_date BETWEEN :academicStartDate AND :academicEndDate ";
            }
            // End of Academic Year check

            $whereClause .= " AND   EXISTS (" . $facultyStudentsQuery . ") ";

            if ($studentListFlag) {
                $groupBy = ' GROUP BY ps.id ';
            } else {
                $groupBy = ' GROUP BY r.id ';
            }

            if (!$isJob) {
                $limitString = " LIMIT :startPoint, :offset ";
            }

            if (strtolower($status) == 'all') {
                $statusArray = ['O', 'C'];
            } else {
                $statusArray = [strtoupper($status)];
            }

            $deterministicSort = 'ps.lastname, ps.firstname, ps.id DESC, r.referral_date ASC, pat1.lastname, pat1.firstname, pat1.id DESC, pf.lastname, pf.firstname, pf.id DESC, r.id DESC  ';

            switch ($sortBy) {
                case 'student_last_name':
                case '+student_last_name':
                    $orderBy = ' ORDER BY ps.lastname, ps.firstname, ps.id DESC ';
                    break;
                case '-student_last_name':
                    $orderBy = ' ORDER BY ps.lastname DESC, ps.firstname, ps.id DESC, r.referral_date ASC, pat1.lastname, pat1.firstname, pat1.id DESC, pf.lastname, pf.firstname, pf.id DESC, r.id DESC  ';
                    break;
                case 'referral_date':
                case '+referral_date':
                    $orderBy = ' ORDER BY r.referral_date, '.$deterministicSort;
                    break;
                case '-referral_date':
                    $orderBy = ' ORDER BY r.referral_date DESC, '.$deterministicSort;
                    break;
                case 'reason_text':
                case '+reason_text':
                    $orderBy = ' ORDER BY ac.short_name, '.$deterministicSort;
                    break;
                case '-reason_text':
                    $orderBy = ' ORDER BY ac.short_name DESC, '.$deterministicSort;
                    break;
                case 'assigned_to_last_name':
                case '+assigned_to_last_name':
                    $orderBy = ' ORDER BY assigned_to_name, '.$deterministicSort;
                    break;
                case '-assigned_to_last_name':
                    $orderBy = ' ORDER BY assigned_to_name DESC, '.$deterministicSort;
                    break;
                case 'created_by_last_name':
                case '+created_by_last_name':
                    $orderBy = ' ORDER BY pf.lastname, pf.firstname, pf.id DESC, '.$deterministicSort;
                    break;
                case '-created_by_last_name':
                    $orderBy = ' ORDER BY pf.lastname DESC, pf.firstname DESC, pf.id DESC, '.$deterministicSort;
                    break;
                default:
                    $orderBy = ' ORDER BY r.referral_date DESC, '.$deterministicSort;
            }

            $sql = $selectSql . $whereClause . $groupBy . $orderBy . $limitString;
            $parameters = [
                'organizationId' => $organizationId,
                'facultyId' => $userId,
                'personId' => $userId,
                'academicStartDate' => $academicStartDate,
                'academicEndDate' => $academicEndDate,
                'status' => $statusArray,
                'startPoint' => (int)$startPoint,
                'offset' => (int)$offset
            ];

            $parameterTypes = [
                'startPoint' => \PDO::PARAM_INT,
                'offset' => \PDO::PARAM_INT,
                'status' => Connection::PARAM_STR_ARRAY
            ];

            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }

    /**
     * Get the received referral details
     *
     * @param int $userId
     * @param int $organizationId
     * @param string $status
     * @param int $startPoint
     * @param int $offset
     * @param bool $studentListFlag
     * @param string $sortBy
     * @param string $academicStartDate
     * @param string $academicEndDate
     * @param string $primaryCoordinatorName
     * @param bool $isJob
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getRecievedReferralDetails($userId, $organizationId, $status, $startPoint, $offset, $studentListFlag = false, $sortBy = '', $academicStartDate = null, $academicEndDate = null, $primaryCoordinatorName, $isJob = FALSE)
    {
        try {
            $em = $this->getEntityManager();
            $facultyStudentsQuery = $this->getFacultyStudentsQuery();
            $selectSql = "
                SELECT 
                    SQL_CALC_FOUND_ROWS r.id,
                    r.id AS referral_id,
                    ps.id AS student_id,
                    ps.firstname AS student_first_name,
                    ps.lastname AS student_last_name,                    
                    ci.primary_email AS student_email,
                    ps.external_id AS student_external_id,
                    ac.id AS reason_id,
                    ac.short_name AS reason_text,
                    r.referral_date AS referral_date,
                    pf.firstname AS created_by_first_name,
                    pf.lastname AS created_by_last_name,                    
                    cif.primary_email AS created_by_email,
                    pf.external_id AS created_by_external_id,
                    r.status,
                    COALESCE(CONCAT(pat1.lastname, ',', pat1.firstname),
                            CONCAT(pat2.lastname, ',', pat2.firstname),
                            CONCAT(pat3.lastname, ',', pat3.firstname),
                            '$primaryCoordinatorName') AS assigned_to_name
                FROM
                    referrals r
                        LEFT JOIN
                    person pf ON r.person_id_faculty = pf.id AND r.organization_id = pf.organization_id
                        LEFT JOIN
                    person ps ON r.person_id_student = ps.id AND r.organization_id = ps.organization_id
                        LEFT JOIN
                    referrals_interested_parties rip ON (rip.referrals_id = r.id AND rip.person_id = :personId )
                        LEFT JOIN
                    referral_routing_rules rr ON (rr.activity_category_id = r.activity_category_id
                        AND rr.organization_id = r.organization_id)
                        LEFT JOIN
                    activity_category ac ON rr.activity_category_id = ac.id and ac.deleted_at IS NULL
                        LEFT JOIN
                    organization_role orgr ON (r.organization_id = orgr.organization_id)
                        LEFT JOIN
                    person pat1 ON (pat1.id = r.person_id_assigned_to) AND pat1.deleted_at IS NULL
                        LEFT JOIN
                    person pat2 ON (pat2.id = rr.person_id) AND pat2.deleted_at IS NULL
                        LEFT JOIN
                    person pat3 ON (pat3.id = rip.person_id) AND pat3.deleted_at IS NULL
                        LEFT JOIN
                    person_contact_info pcif ON (pf.id = pcif.person_id)
                        LEFT JOIN
                    contact_info cif ON cif.id = pcif.contact_id
                		LEFT JOIN
                    person_contact_info pcis ON (ps.id = pcis.person_id)
                        LEFT JOIN
                    contact_info ci ON ci.id = pcis.contact_id
                WHERE
                    ((r.person_id_assigned_to = :personId
                        OR (orgr.person_id = :personId AND r.person_id_assigned_to IS NULL AND orgr.role_id = 1))
                        AND (r.status IN ( :status ))
                        AND r.organization_id = :organizationId )
                        AND (r.deleted_at IS NULL)
                        AND (pf.deleted_at IS NULL)
                        AND (ps.deleted_at IS NULL)
                        AND (rr.deleted_at IS NULL)
                        AND (orgr.deleted_at IS NULL)";

            $whereClause = "";
            // Added to restrict the data to current academic year
            if (!is_null($academicStartDate) && !is_null($academicEndDate)) {
                $whereClause .= " AND r.referral_date BETWEEN :academicStartDate AND :academicEndDate ";
            }
            // END of Academic Year check

            $whereClause .= " AND   EXISTS (" . $facultyStudentsQuery . ") ";

            if ($studentListFlag) {
                $groupBy = ' GROUP BY ps.id ';
            } else {
                $groupBy = ' GROUP BY r.id ';
            }
            if (!$isJob) {
                $limitString = ' LIMIT :startPoint, :offset ';
            }

            if (strtolower($status) == 'all') {
                $statusArray = ['O', 'C'];
            } else {
                $statusArray = [strtoupper($status)];
            }

            $deterministicSort = 'ps.lastname, ps.firstname, ps.id DESC, r.referral_date, pat1.lastname, pat1.firstname, pat1.id DESC, pf.lastname, pf.firstname, pf.id DESC, r.id DESC  ';

            switch ($sortBy) {
                case 'student_last_name':
                case '+student_last_name':
                    $orderBy = ' ORDER BY '.$deterministicSort;
                    break;
                case '-student_last_name':
                    $orderBy = ' ORDER BY ps.lastname DESC, ps.firstname, ps.id DESC, r.referral_date, pat1.lastname, pat1.firstname, pat1.id DESC, pf.lastname, pf.firstname, pf.id DESC, r.id DESC  ';
                    break;
                case 'referral_date':
                case '+referral_date':
                    $orderBy = ' ORDER BY r.referral_date, '.$deterministicSort;
                    break;
                case '-referral_date':
                    $orderBy = ' ORDER BY r.referral_date DESC, '.$deterministicSort;
                    break;
                case 'reason_text':
                case '+reason_text':
                    $orderBy = ' ORDER BY ac.short_name, '.$deterministicSort;
                    break;
                case '-reason_text':
                    $orderBy = ' ORDER BY ac.short_name DESC, '.$deterministicSort;
                    break;
                case 'assigned_to_last_name':
                case '+assigned_to_last_name':
                    $orderBy = ' ORDER BY assigned_to_name, '.$deterministicSort;
                    break;
                case '-assigned_to_last_name':
                    $orderBy = ' ORDER BY assigned_to_name DESC, '.$deterministicSort;
                    break;
                case 'created_by_last_name':
                case '+created_by_last_name':
                    $orderBy = ' ORDER BY pf.lastname, pf.firstname, pf.id DESC, '.$deterministicSort;
                    break;
                case '-created_by_last_name':
                    $orderBy = ' ORDER BY pf.lastname DESC, pf.firstname DESC, pf.id DESC, '.$deterministicSort;
                    break;
                default:
                    $orderBy = ' ORDER BY r.referral_date DESC, '.$deterministicSort;
            }

            $sql = $selectSql . $whereClause . $groupBy . $orderBy . $limitString;
            $parameters = [
                'organizationId' => $organizationId,
                'facultyId' => $userId,
                'personId' => $userId,
                'academicStartDate' => $academicStartDate,
                'academicEndDate' => $academicEndDate,
                'status' => $statusArray,
                'startPoint' => (int)$startPoint,
                'offset' => (int)$offset
            ];

            $parameterTypes = [
                'startPoint' => \PDO::PARAM_INT,
                'offset' => \PDO::PARAM_INT,
                'status' => Connection::PARAM_STR_ARRAY
            ];

            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }

    /**
     * Get the InterestedParty referral details
     *
     * @param int $userId
     * @param int $organizationId
     * @param string $status
     * @param int $startPoint
     * @param int $offset
     * @param bool $studentListFlag
     * @param string $sortBy
     * @param string $academicStartDate
     * @param string $academicEndDate
     * @param string $primaryCoordinatorName
     * @param bool $isJob
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getReferralDetailsAsInterestedParty($userId, $organizationId, $status, $startPoint, $offset, $studentListFlag = false, $sortBy = '', $academicStartDate = null, $academicEndDate = null, $primaryCoordinatorName, $isJob = FALSE)
    {
        try {
            $em = $this->getEntityManager();
            $facultyStudentsQuery = $this->getFacultyStudentsQuery();
            $selectSql = "
                SELECT 
                    SQL_CALC_FOUND_ROWS r.id,
                    r.id AS referral_id,
                    ps.id AS student_id,
                    ps.firstname AS student_first_name,
                    ps.lastname AS student_last_name,                    
                    ci.primary_email AS student_email,
                    ps.external_id AS student_external_id,
                    ac.id AS reason_id,
                    ac.short_name AS reason_text,
                    r.referral_date AS referral_date,
                    pf.firstname AS created_by_first_name,
                    pf.lastname AS created_by_last_name,                    
                    cif.primary_email AS created_by_email,
                    pf.external_id AS created_by_external_id,
                    r.status,
                    COALESCE(CONCAT(pat1.lastname, ',', pat1.firstname),
                            CONCAT(pat2.lastname, ',', pat2.firstname),
                            CONCAT(pat3.lastname, ',', pat3.firstname),
                            '$primaryCoordinatorName') AS assigned_to_name
                FROM
                    referrals r
                        LEFT JOIN
                    person pf ON r.person_id_faculty = pf.id AND r.organization_id = pf.organization_id
                        LEFT JOIN
                    person ps ON r.person_id_student = ps.id AND r.organization_id = ps.organization_id
                        LEFT JOIN
                    referrals_interested_parties rip ON (rip.referrals_id = r.id AND rip.person_id = :personId )
                        LEFT JOIN
                    referral_routing_rules rr ON (rr.activity_category_id = r.activity_category_id
                        AND rr.organization_id = r.organization_id)
                        LEFT JOIN
                    activity_category ac ON rr.activity_category_id = ac.id AND ac.deleted_at IS NULL
                        LEFT JOIN
                    organization_role orgr ON (r.organization_id = orgr.organization_id AND orgr.organization_id = :organizationId AND orgr.role_id = 1 )
                        LEFT JOIN
                    person pat1 ON (pat1.id = r.person_id_assigned_to) AND pat1.deleted_at IS NULL
                        LEFT JOIN
                    person pat2 ON (pat2.id = rr.person_id) AND pat2.deleted_at IS NULL
                        LEFT JOIN
                    person pat3 ON (pat3.id = rip.person_id) AND pat3.deleted_at IS NULL
                        LEFT JOIN
                    person_contact_info pcif ON (pf.id = pcif.person_id)
                        LEFT JOIN
                    contact_info cif ON cif.id = pcif.contact_id
                		LEFT JOIN
                    person_contact_info pcis ON (ps.id = pcis.person_id)
                        LEFT JOIN
                    contact_info ci ON ci.id = pcis.contact_id           

                WHERE
                    ((  rip.person_id = :personId )
                        AND (r.status IN ( :status ))
                        AND r.organization_id = :organizationId )
                        AND (r.deleted_at IS NULL)
                        AND (pf.deleted_at IS NULL)
                        AND (ps.deleted_at IS NULL)
                        AND (rip.deleted_at IS NULL)
                        AND (rr.deleted_at IS NULL)
                        AND (orgr.deleted_at IS NULL)";

            $whereClause = "";
            // Added to restrict the data to current academic year
            if (!is_null($academicStartDate) && !is_null($academicEndDate)) {
                $whereClause .= " AND r.referral_date BETWEEN :academicStartDate AND :academicEndDate ";
            }
            // END of Academic Year check

            $whereClause .= " AND   EXISTS (" . $facultyStudentsQuery . ") ";

            if ($studentListFlag) {
                $groupBy = ' GROUP BY ps.id ';
            } else {
                $groupBy = ' GROUP BY r.id ';
            }
            if (!$isJob) {
                $limitString = ' LIMIT :startPoint, :offset ';
            }

            if (strtolower($status) == 'all') {
                $statusArray = ['O', 'C'];
            } else {
                $statusArray = [strtoupper($status)];
            }

            $deterministicSort = 'ps.lastname, ps.firstname, ps.id DESC, r.referral_date ASC, pat1.lastname, pat1.firstname, pat1.id DESC, pf.lastname, pf.firstname, pf.id DESC, r.id DESC  ';

            switch ($sortBy) {
                case 'student_last_name':
                case '+student_last_name':
                    $orderBy = ' ORDER BY '.$deterministicSort;
                    break;
                case '-student_last_name':
                    $orderBy = ' ORDER BY ps.lastname DESC, ps.firstname, ps.id DESC, r.referral_date ASC, pat1.lastname, pat1.firstname, pat1.id DESC, pf.lastname, pf.firstname, pf.id DESC, r.id DESC  ';
                    break;
                case 'referral_date':
                case '+referral_date':
                    $orderBy = ' ORDER BY r.referral_date, '.$deterministicSort;
                    break;
                case '-referral_date':
                    $orderBy = ' ORDER BY r.referral_date DESC, '.$deterministicSort;
                    break;
                case 'reason_text':
                case '+reason_text':
                    $orderBy = ' ORDER BY ac.short_name, '.$deterministicSort;
                    break;
                case '-reason_text':
                    $orderBy = ' ORDER BY ac.short_name DESC, '.$deterministicSort;
                    break;
                case 'assigned_to_last_name':
                case '+assigned_to_last_name':
                    $orderBy = ' ORDER BY assigned_to_name, '.$deterministicSort;
                    break;
                case '-assigned_to_last_name':
                    $orderBy = ' ORDER BY assigned_to_name DESC, '.$deterministicSort;
                    break;
                case 'created_by_last_name':
                case '+created_by_last_name':
                    $orderBy = ' ORDER BY pf.lastname, pf.firstname, pf.id DESC, '.$deterministicSort;
                    break;
                case '-created_by_last_name':
                    $orderBy = ' ORDER BY pf.lastname DESC, pf.firstname DESC, pf.id DESC, '.$deterministicSort;
                    break;
                default:
                    $orderBy = ' ORDER BY r.referral_date DESC, '.$deterministicSort;
            }

            $sql = $selectSql . $whereClause . $groupBy . $orderBy . $limitString;
            $parameters = [
                'organizationId' => $organizationId,
                'facultyId' => $userId,
                'personId' => $userId,
                'academicStartDate' => $academicStartDate,
                'academicEndDate' => $academicEndDate,
                'status' => $statusArray,
                'startPoint' => (int)$startPoint,
                'offset' => (int)$offset
            ];

            $parameterTypes = [
                'startPoint' => \PDO::PARAM_INT,
                'offset' => \PDO::PARAM_INT,
                'status' => Connection::PARAM_STR_ARRAY
            ];

            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }

    /**
     * Get the all referral details
     *
     * @param int $userId
     * @param int $organizationId
     * @param string $status
     * @param int $startPoint
     * @param int $offset
     * @param bool $studentListFlag
     * @param string $sortBy
     * @param string $academicStartDate
     * @param string $academicEndDate
     * @param string $primaryCoordinatorName
     * @param bool $isJob
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getAllReferralDetails($userId, $organizationId, $status, $startPoint, $offset, $studentListFlag = false, $sortBy = '', $academicStartDate = null, $academicEndDate = null, $primaryCoordinatorName, $isJob = false)
    {
        try {
            $em = $this->getEntityManager();
            $selectSql = "
                SELECT 
                    SQL_CALC_FOUND_ROWS r.id AS referral_id,
                    student.id AS student_id,
                    student.firstname AS student_first_name,
                    student.lastname AS student_last_name,                    
                    student.username AS student_email,
                    student.external_id AS student_external_id,
                    ac.id AS reason_id,
                    ac.short_name AS reason_text,
                    r.referral_date,
                    faculty.firstname AS created_by_first_name,
                    faculty.lastname AS created_by_last_name,                    
                    faculty.username AS created_by_email,
                    faculty.external_id AS created_by_external_id,
                    r.status,
                    COALESCE(CONCAT(assignee.lastname, ',', assignee.firstname),
                            CONCAT(routed_to.lastname, ',', routed_to.firstname),
                            CONCAT(interested_party.lastname, ',', interested_party.firstname),
                            :primaryCoordinator ) AS assigned_to_name
                FROM
                    referrals r
                        INNER JOIN
                    org_faculty_student_permission_map ofspm ON ofspm.org_id = :organizationId
                          AND ofspm.faculty_id = :facultyId 
                          AND ofspm.student_id = r.person_id_student
                        INNER JOIN
                    org_permissionset op ON op.id = ofspm.permissionset_id 
                          AND op.accesslevel_ind_agg = 1
                        INNER JOIN
                    org_person_student_year opsy ON opsy.person_id = ofspm.student_id
                        INNER JOIN
                    org_academic_year oay ON oay.id = opsy.org_academic_year_id 
                          AND oay.organization_id = ofspm.org_id 
                          AND oay.start_date = :academicStartDate AND oay.end_date = :academicEndDate
                        LEFT JOIN
                    person faculty ON r.person_id_faculty = faculty.id 
                          AND r.organization_id = faculty.organization_id  
                          AND faculty.deleted_at IS NULL
                        INNER JOIN
                    person student ON r.person_id_student = student.id 
                          AND r.organization_id = student.organization_id
                        LEFT JOIN
                    referrals_interested_parties rip ON rip.referrals_id = r.id 
                          AND rip.person_id = :facultyId 
                          AND rip.deleted_at IS NULL
                        LEFT JOIN
                    referral_routing_rules rrr ON rrr.activity_category_id = r.activity_category_id 
                          AND rrr.organization_id = r.organization_id
                          AND rrr.deleted_at IS NULL
                        LEFT JOIN
                    activity_category ac ON rrr.activity_category_id = ac.id 
                          AND ac.deleted_at IS NULL
                        LEFT JOIN
                    organization_role orgr ON r.organization_id = orgr.organization_id 
                          AND orgr.person_id = :facultyId
                          AND orgr.deleted_at IS NULL
                        LEFT JOIN
                    person assignee ON assignee.id = r.person_id_assigned_to 
                          AND assignee.deleted_at IS NULL
                        LEFT JOIN
                    person routed_to ON routed_to.id = rrr.person_id 
                          AND routed_to.deleted_at IS NULL
                        LEFT JOIN
                    person interested_party ON interested_party.id = rip.person_id 
                          AND interested_party.deleted_at IS NULL
                WHERE
                    (
                        (
                            r.person_id_assigned_to = :facultyId
                            OR r.person_id_faculty = :facultyId
                            OR rip.person_id = :facultyId
                            OR (orgr.person_id = :facultyId
                            AND r.person_id_assigned_to IS NULL
                            AND orgr.role_id = 1)
                        )
                        AND r.status IN ( :status ) 
                        AND r.organization_id = :organizationId 
                    )
                    AND r.deleted_at IS NULL
                    AND student.deleted_at IS NULL
                    AND op.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL
                    AND oay.deleted_at IS NULL";

            $whereClause = "";
            // Added to restrict the data to current academic year
            if (!is_null($academicStartDate) && !is_null($academicEndDate)) {
                $whereClause .= " AND r.referral_date BETWEEN :academicStartDate AND :academicEndDate ";
            }
            // END of Academic Year check

            if ($studentListFlag) {
                $groupBy = ' GROUP BY student.id ';
            } else {
                $groupBy = ' GROUP BY r.id ';
            }
            if (!$isJob) {
                $limitString = ' LIMIT :startPoint, :offset ';
            }

            if (strtolower($status) == 'all') {
                $statusArray = ['O', 'C'];
            } else {
                $statusArray = [strtoupper($status)];
            }

            $deterministicSort = 'student.lastname, student.firstname, student.id DESC, r.referral_date ASC, assignee.lastname, assignee.firstname, assignee.id DESC, faculty.lastname, faculty.firstname, faculty.id DESC, r.id DESC  ';

            switch ($sortBy) {
                case 'student_last_name':
                case '+student_last_name':
                    $orderBy = ' ORDER BY '.$deterministicSort;
                    break;
                case '-student_last_name':
                    $orderBy = ' ORDER BY student.lastname DESC, student.firstname, student.id DESC, r.referral_date ASC, assignee.lastname, assignee.firstname, assignee.id DESC, pf.lastname, pf.firstname, pf.id DESC, r.id DESC  ';
                    break;
                case 'referral_date':
                case '+referral_date':
                    $orderBy = ' ORDER BY r.referral_date, '.$deterministicSort;
                    break;
                case '-referral_date':
                    $orderBy = ' ORDER BY r.referral_date DESC, '.$deterministicSort;
                    break;
                case 'reason_text':
                case '+reason_text':
                    $orderBy = ' ORDER BY ac.short_name, '.$deterministicSort;
                    break;
                case '-reason_text':
                    $orderBy = ' ORDER BY ac.short_name DESC, '.$deterministicSort;
                    break;
                case 'assigned_to_last_name':
                case '+assigned_to_last_name':
                    $orderBy = ' ORDER BY assigned_to_name, '.$deterministicSort;
                    break;
                case '-assigned_to_last_name':
                    $orderBy = ' ORDER BY assigned_to_name DESC, '.$deterministicSort;
                    break;
                case 'created_by_last_name':
                case '+created_by_last_name':
                    $orderBy = ' ORDER BY faculty.lastname, faculty.firstname, faculty.id DESC, '.$deterministicSort;
                    break;
                case '-created_by_last_name':
                    $orderBy = ' ORDER BY faculty.lastname DESC, faculty.firstname DESC, faculty.id DESC, '.$deterministicSort;
                    break;
                default:
                    $orderBy = ' ORDER BY r.referral_date DESC, '.$deterministicSort;
            }

            $sql = $selectSql . $whereClause . $groupBy . $orderBy . $limitString;
            $parameters = [
                'organizationId' => $organizationId,
                'primaryCoordinator' => $primaryCoordinatorName,
                'facultyId' => $userId,
                'academicStartDate' => $academicStartDate,
                'academicEndDate' => $academicEndDate,
                'status' => $statusArray,
                'startPoint' => (int)$startPoint,
                'offset' => (int)$offset
            ];

            $parameterTypes = [
                'startPoint' => \PDO::PARAM_INT,
                'offset' => \PDO::PARAM_INT,
                'status' => Connection::PARAM_STR_ARRAY
            ];



            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }
    
    public function getCountOfReferrals($countQuery)
    {
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($countQuery);
            $stmt->execute();
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return $stmt->fetchAll();
    }
    
    public function getOpenReferral($facultyId, $studentId, $isPrimaryCooordinator = FALSE)
    {
       $em = $this->getEntityManager();
        $assignedTo = "AND (person_id_assigned_to = $facultyId)";
        if($isPrimaryCooordinator){
            $assignedTo = "AND (person_id_assigned_to = $facultyId OR person_id_assigned_to IS NULL)";
        }
           
        $sql = 'select * from referrals where person_id_student = '.$studentId.' '.$assignedTo.' AND status = "O" AND deleted_at is null';
    
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;      
    }

    /**
     * Get all faculty that can be assigned a referral per a specific studentId, and return the id(s), firstname(s), lastname(s),
     * title(s), and group invisibility of the faculty in an associative array.
     *
     * @param int $studentId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getPossibleReferralAssigneesByStudent($studentId)
    {
        $parameters = [
            'studentId' => (int)$studentId
        ];

        /*
         * The view org_faculty_student_permission_map is not optimized for this situation.
         * TODO: Create a view that optimizes this logic and start using it
         */
        $sql = "
                SELECT DISTINCT
                    *
                FROM
                    (
                        SELECT
                            p.id AS person_id,
                            p.firstname AS first_name,
                            p.lastname AS last_name,
                            p.title,
                            CONCAT('CC-', p.id) AS user_key
                        FROM
                            person p
                                INNER JOIN
                            org_group_faculty ogf
                                    ON ogf.person_id = p.id
                                    AND ogf.organization_id = p.organization_id
                                INNER JOIN
                            org_group_tree ogt
                                    ON ogt.ancestor_group_id = ogf.org_group_id
                                INNER JOIN
                            org_group_students ogs
                                    ON ogs.org_group_id = ogt.descendant_group_id
                                    AND ogs.organization_id = p.organization_id
                                INNER JOIN
                            org_permissionset op
                                    ON op.id = ogf.org_permissionset_id
                                    AND op.organization_id = p.organization_id
                                INNER JOIN
                            org_permissionset_features opf
                                    ON opf.org_permissionset_id = ogf.org_permissionset_id
                                    AND opf.organization_id = p.organization_id
                                INNER JOIN
                            feature_master_lang fml
                                    ON fml.feature_master_id = opf.feature_id
                        WHERE
                            ogs.person_id = :studentId
                            AND opf.receive_referral = 1
                            AND op.accesslevel_ind_agg = 1
                            AND fml.feature_name = 'Referrals'
                            AND (ogf.is_invisible IS NULL OR ogf.is_invisible = 0)
                            AND p.deleted_at IS NULL
                            AND ogf.deleted_at IS NULL
                            AND ogt.deleted_at IS NULL
                            AND ogs.deleted_at IS NULL
                            AND op.deleted_at IS NULL
                            AND opf.deleted_at IS NULL
                            AND fml.deleted_at IS NULL

                    UNION

                        SELECT
                            p.id AS person_id,
                            p.firstname as first_name,
                            p.lastname as last_name,
                            p.title,
                            CONCAT('CC-', p.id) AS user_key
                        FROM
                            person p
                                INNER JOIN
                            org_course_faculty ocf
                                    ON ocf.person_id = p.id
                                    AND ocf.organization_id = p.organization_id
                                INNER JOIN
                            org_courses oc
                                    ON oc.id = ocf.org_courses_id
                                    AND oc.organization_id = p.organization_id
                                INNER JOIN
                            org_course_student ocs
                                    ON ocs.org_courses_id = oc.id
                                    AND ocs.organization_id = p.organization_id
                                INNER JOIN
                            org_academic_terms oat
                                    ON oat.id = oc.org_academic_terms_id
                                    AND oat.organization_id = p.organization_id
                                INNER JOIN
                            org_permissionset op
                                    ON op.id = ocf.org_permissionset_id
                                    AND op.organization_id = p.organization_id
                                INNER JOIN
                            org_permissionset_features opf
                                    ON opf.org_permissionset_id = ocf.org_permissionset_id
                                    AND opf.organization_id = p.organization_id
                                INNER JOIN
                            feature_master_lang fml
                                    ON fml.feature_master_id = opf.feature_id
                        WHERE
                            ocs.person_id = :studentId
                            AND opf.receive_referral = 1
                            AND op.accesslevel_ind_agg = 1
                            AND fml.feature_name = 'Referrals'
                            AND CURDATE() BETWEEN oat.start_date AND oat.end_date
                            AND p.deleted_at IS NULL
                            AND ocf.deleted_at IS NULL
                            AND oc.deleted_at IS NULL
                            AND ocs.deleted_at IS NULL
                            AND oat.deleted_at IS NULL
                            AND op.deleted_at IS NULL
                            AND opf.deleted_at IS NULL
                            AND fml.deleted_at IS NULL
                    ) AS person_info
                ORDER BY person_info.last_name, person_info.first_name, person_info.person_id;";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $resultSet = $stmt->fetchAll();
        return $resultSet;
    }

}
