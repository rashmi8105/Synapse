<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\Person; 
use Synapse\CoreBundle\Entity\Teams;
use Synapse\CoreBundle\Entity\TeamMembers;
use Synapse\CoreBundle\Util\Constants\TeamsConstant;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Doctrine\DBAL\Connection;

class TeamMembersRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:TeamMembers';

    public function createTeamMembers(TeamMembers $newTeamMember)
    {
        $em = $this->getEntityManager();
        
        $em->persist($newTeamMember);
        
        return $newTeamMember;
    }

    public function getTeamInfo(Teams $teamInstance)
    {
        $em = $this->getEntityManager();
        $qb1 = $em->createQueryBuilder()
            ->select('distinct(p.id) person_id,p.firstname first_name,p.lastname last_name,tm.isTeamLeader is_leader,0 action')
            ->from(TeamsConstant::TEAM_REPO, 't')
            ->LEFTJoin(TeamsConstant::TEAM_MEMBER_REPO, 'tm', \Doctrine\ORM\Query\Expr\Join::WITH, 'tm.teamId = tm.teamId')
            ->LEFTJoin('SynapseCoreBundle:Person', 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = tm.person')
            ->where('tm.teamId = :team')
            ->andWhere('tm.deletedBy IS NULL ')
            ->setParameters(array(
            'team' => $teamInstance
        ))
            ->getQuery();
        $resultSet = $qb1->getResult();
        
        return $resultSet;
    }

    public function deleteMember($teamMembers)
    {
        $em = $this->getEntityManager();
        
        $em->remove($teamMembers);
        
        return $teamMembers;
    }

    public function getTeamById($personId, $teamId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('tm.id')
            ->from(TeamsConstant::TEAM_MEMBER_REPO, 'tm')
            ->where('tm.person = :personid')
            ->andWhere('tm.teamId = :team')
            ->setParameters(array(
            'personid' => $personId,
            'team' => $teamId
        ))
            ->getQuery();
        $resultSet = $qb->getResult();
        
        return $resultSet;
    }

    public function updateTeamMembers(TeamMembers $teamMember)
    {
        $em = $this->getEntityManager();
        
        $em->merge($teamMember);
        
        return $teamMember;
    }

    public function getOrganizationTeamByUserId($id, $personid)
    {
        $em = $this->getEntityManager();
        $qb1 = $em->createQueryBuilder()
            ->select(TeamsConstant::FIELD_SELECT_COLS)
            ->addSelect('tm.isTeamLeader as role')
            ->from(TeamsConstant::TEAM_MEMBER_REPO, 'tm')
            ->LEFTJoin(TeamsConstant::TEAM_REPO, 't', \Doctrine\ORM\Query\Expr\Join::WITH, TeamsConstant::TEAM_ID_COM)
            ->where(TeamsConstant::TEAM_PERSON_ID_COM)
            ->andWhere('tm.organization = :oid')
            ->setParameters(array(
            'pid' => $personid,
            'oid' => $id
        ))
            ->getQuery();
        $resultSet = $qb1->getResult();
        return $resultSet;
    }

    public function getMyTeams($personId, $organizationId)
    {
        $em = $this->getEntityManager();
        $qb1 = $em->createQueryBuilder()
            ->select(TeamsConstant::FIELD_SELECT_COLS)
            ->from(TeamsConstant::TEAM_MEMBER_REPO, 'tm')
            ->LEFTJoin(TeamsConstant::TEAM_REPO, 't', \Doctrine\ORM\Query\Expr\Join::WITH, TeamsConstant::TEAM_ID_COM)
            ->where(TeamsConstant::TEAM_PERSON_ID_COM)
            ->andWhere('tm.organization = :oid')
            ->andWhere('tm.isTeamLeader = :isLeader')
            ->setParameters(array(
            'pid' => $personId,
            'oid' => $organizationId,
            'isLeader' => true
        ))
            ->getQuery();
        $resultSet = $qb1->getResult();
        return $resultSet;
    }

    public function getTeams($personid)
    {
        $em = $this->getEntityManager();
        $qb1 = $em->createQueryBuilder()
            ->select(TeamsConstant::FIELD_SELECT_COLS)
            ->from(TeamsConstant::TEAM_MEMBER_REPO, 'tm')
            ->LEFTJoin(TeamsConstant::TEAM_REPO, 't', \Doctrine\ORM\Query\Expr\Join::WITH, TeamsConstant::TEAM_ID_COM)
            ->where(TeamsConstant::TEAM_PERSON_ID_COM)
            ->setParameters(array(
            'pid' => $personid
        ))
            ->getQuery();
        $resultSet = $qb1->getResult();
        return $resultSet;
    }

    public function getTeamMembersByPerson($teamId, $organizationId)
    {
        $em = $this->getEntityManager();
        $sql = "select tm.id, tm.person_id as person, p.firstname as first_name,p.lastname as last_name, p.username as primaryEmail
                from team_members tm join person p on p.id = tm.person_id 
                where tm.organization_id = '" . $organizationId . "' and tm.teams_id = '" . $teamId . "' and p.deleted_at IS NULL and tm.deleted_at IS NULL
                order by p.lastname ASC, p.firstname ASC, p.username ASC";
        $resultSet = $em->getConnection()->fetchAll($sql);
        return $resultSet;
    }


    /**
     * Retrieves Count of provided Activities by activity code per team where the loggedInPerson is team leader
     *
     * @param string $activityType - 'interaction'|'open-referral'|'login'
     * @param array $activityCodes - ['N', 'A', 'R', 'C']|['L']|['R']
     * @param string $fromDate - 'yyyy-dd-mm hh:mm:ss'
     * @param string $toDate - 'yyyy-dd-mm hh:mm:ss'
     * @param int $personId
     * @param int $organizationId
     * @param string $academicYearStartDate - 'yyyy-dd-mm hh:mm:ss'
     * @param string $academicYearEndDate - 'yyyy-dd-mm hh:mm:ss'
     * @param int $teamId
     * @param int $currentAcademicYearId
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getActivityCountsOfMyTeamByActivityType($activityType, $activityCodes, $fromDate, $toDate, $personId, $organizationId, $academicYearStartDate, $academicYearEndDate, $teamId = null, $currentAcademicYearId = null)
    {
        $parameters = [
            'activityType' => $activityType,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'organizationId' => $organizationId,
            'academicStartDate' => $academicYearStartDate,
            'academicEndDate' => $academicYearEndDate,
            'activityCodes' => $activityCodes,
            'orgAcademicYearId' => $currentAcademicYearId
        ];

        $parameterTypes = ['activityCodes' => Connection::PARAM_STR_ARRAY];


        if ($activityType === 'login') {
            $permissionsJoin = "";
        } else {

            $permissionsJoin = "INNER JOIN org_faculty_student_permission_map AS ofspm
                                    ON al.person_id_student = ofspm.student_id
                                    AND al.organization_id = ofspm.org_id
                                    AND ofspm.faculty_id = :personId
                                INNER JOIN org_person_student_year opsy 
                                    ON opsy.person_id = ofspm.student_id
                                    AND opsy.org_academic_year_id = :orgAcademicYearId
                                    AND opsy.deleted_at IS NULL
                                INNER JOIN org_permissionset op ON ofspm.permissionset_id = op.id
                                    AND op.accesslevel_ind_agg = 1
                                    AND op.deleted_at IS NULL";
        }

        if ($activityType === 'open-referral') {
            $referralStatus = "AND r.status = 'O' ";
            $referralJoin = "INNER JOIN referrals r ON r.id = al.referrals_id ";
            $referralDelete = "AND r.deleted_at IS NULL ";
        } else {
            $referralStatus = "";
            $referralJoin = "";
            $referralDelete = "";
        }


        if ($teamId === null) {
            $teamList = " SELECT
              teams_id
            FROM
              team_members
            WHERE
              is_team_leader = 1
              AND person_id = :personId
              AND deleted_at IS NULL ";
        } else {
            $parameters['teamId'] = $teamId;
            $teamList = " :teamId ";
        }

        if ($activityType !== 'login' || $teamId === null) {
            $parameters['personId'] = $personId;
        }

        $sql = "SELECT
                    tm.teams_id AS team_id,
                    t.team_name,
                    COUNT(DISTINCT al.id) AS team_activities_count
                FROM
                    Teams t
                INNER JOIN team_members tm ON tm.teams_id = t.id
                    AND tm.organization_id = t.organization_id
                INNER JOIN activity_log al ON al.organization_id = tm.organization_id
                    AND al.person_id_faculty = tm.person_id
                $permissionsJoin
                $referralJoin
                WHERE
                  al.activity_type IN (:activityCodes)
                    AND tm.organization_id = :organizationId
                    $referralStatus
                    AND al.activity_date BETWEEN :fromDate AND :toDate
                    AND al.activity_date BETWEEN :academicStartDate AND :academicEndDate
                    AND tm.teams_id IN ($teamList)
                    AND t.deleted_at IS NULL
                    AND tm.deleted_at IS NULL
                    AND al.deleted_at IS NULL
                    $referralDelete
                GROUP BY t.id";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $results = $stmt->fetchAll();
        return $results;
    }


    /**
     * Get Activity Table for the specified Person given all the criteria
     *
     * @param string $activityType - 'interaction'|'open-referral'|'login'
     * @param array $activityCodes - ['N', 'A', 'R', 'C'] | ['L'] | ['R']
     * @param int $personId
     * @param int $organizationId
     * @param string $fromDate - 'yyyy-dd-mm hh:mm:ss'
     * @param string $toDate - 'yyyy-dd-mm hh:mm:ss'
     * @param string $academicYearStartDate - 'yyyy-dd-mm hh:mm:ss'
     * @param string $academicYearEndDate - 'yyyy-dd-mm hh:mm:ss'
     * @param array $teamMemberIds
     * @param int $pageNumber
     * @param int $recordsPerPage
     * @param string $sortBy
     * @param int $currentAcademicYearId
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getActivityDetailsOfMyTeam($activityType, $activityCodes, $personId, $organizationId, $fromDate, $toDate, $academicYearStartDate, $academicYearEndDate, $teamMemberIds, $pageNumber = null, $recordsPerPage = null, $sortBy = '', $currentAcademicYearId = null)
    {
        $parameters = [
            'activityType' => $activityType,
            'activityCodes' => $activityCodes,
            'organizationId' => $organizationId,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'academicStartDate' => $academicYearStartDate,
            'academicEndDate' => $academicYearEndDate,
            'teamMemberIds' => $teamMemberIds,
            'orgAcademicYearId' => $currentAcademicYearId
        ];

        $parameterTypes = ['teamMemberIds' => Connection::PARAM_INT_ARRAY, 'activityCodes' => Connection::PARAM_STR_ARRAY];

        if ($pageNumber == null && $recordsPerPage == null) {
            $limitString = '';
        } else {
            $startPoint = ($pageNumber * $recordsPerPage) - $recordsPerPage;
            $limitString = " LIMIT :recordsPerPage OFFSET :startPoint";
            $parameters['recordsPerPage'] = (int)$recordsPerPage;
            $parameters['startPoint'] = $startPoint;
            $parameterTypes['recordsPerPage'] = 'integer';
            $parameterTypes['startPoint'] = 'integer';
        }

        if (isset($sortBy) && trim($sortBy) != "") {
            if ($sortBy[0] == '-') {
                $sortOrder = "DESC";
            } else {
                $sortOrder = "ASC";
            }
            $sortBy = substr($sortBy, 1, strlen($sortBy));
            switch ($sortBy) {
                case 'team_member_name' :
                    $sortBy = " team_member_lastname $sortOrder, team_member_firstname $sortOrder, activity_date DESC, al.id ASC  ";
                    break;
                case 'date' :
                    $sortBy = " activity_date $sortOrder, al.id ";
                    break;
                case 'activity_type' :
                    $sortBy = " activity_type $sortOrder, activity_date DESC, al.id ASC ";
                    break;
                default:
                    $sortBy = " activity_date DESC, al.id ASC ";
                    break;
            }
        } else {
            $sortBy = " activity_date DESC, al.id ASC ";
        }


        if ($activityType === 'login') {
            $permissionsSelect = "";
            $permissionsJoin = "";
        } else {
            $permissionsSelect = "GROUP_CONCAT(DISTINCT ofspm.permissionset_id) AS org_permissionset_ids,";

            $permissionsJoin = "INNER JOIN org_faculty_student_permission_map AS ofspm
                                    ON al.person_id_student = ofspm.student_id
                                    AND al.organization_id = ofspm.org_id
                                    AND ofspm.faculty_id = :personId
                                INNER JOIN org_person_student_year opsy 
                                    ON opsy.person_id = ofspm.student_id
                                    AND opsy.org_academic_year_id = :orgAcademicYearId
                                    AND opsy.deleted_at IS NULL
                                INNER JOIN org_permissionset op ON ofspm.permissionset_id = op.id
                                    AND op.accesslevel_ind_agg = 1
                                    AND op.deleted_at IS NULL";
            $parameters['personId'] = $personId;
        }

        if ($activityType === 'open-referral') {
            $referralStatus = "AND r.status = 'O' ";
            $referralStatusColumn = "r.status,";
            $referralJoin = "INNER JOIN referrals r ON r.id = al.referrals_id ";
            $referralDelete = "AND r.deleted_at IS NULL ";

        } else {
            $referralStatus = "";
            $referralStatusColumn = "";
            $referralJoin = "";
            $referralDelete = "";
        }


        $sql = "
            SELECT
                al.activity_date AS activity_date,
                pfaculty.external_id AS team_member_external_id,
                al.person_id_faculty AS team_member_id,
                pfaculty.firstname AS team_member_firstname,
                pfaculty.lastname AS team_member_lastname,
                pfaculty.username AS primary_email,
                al.person_id_student AS student_id,
                pstudent.firstname as student_firstname,
                pstudent.lastname as student_lastname,
                pstudent.external_id AS student_external_id,
                pstudent.username AS student_email,
                al.activity_type AS activity_code,
                al.referrals_id,
                al.appointments_id,
                al.note_id,
                al.contacts_id,
                $referralStatusColumn
                $permissionsSelect
                al.reason AS reason_text
            FROM
                activity_log al
                $permissionsJoin
                $referralJoin
                INNER JOIN person pfaculty ON pfaculty.id = al.person_id_faculty
                LEFT JOIN person pstudent ON pstudent.id = al.person_id_student
                    AND pstudent.deleted_at IS NULL
            WHERE
                al.activity_type IN (:activityCodes)
                $referralStatus
                AND al.organization_id = :organizationId
                AND al.activity_date BETWEEN :fromDate AND :toDate
                AND al.activity_date BETWEEN :academicStartDate AND :academicEndDate
                AND al.person_id_faculty IN (:teamMemberIds)
                AND al.deleted_at IS NULL
                AND pfaculty.deleted_at IS NULL
                $referralDelete
            GROUP BY al.id
            ORDER BY $sortBy
            $limitString
            ";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $results = $stmt->fetchAll();
        return $results;
    }

    /**
     * @param int $personId
     * @param int $teamId
     * @param string $role
     * @throws \Doctrine\ORM\ORMException
     */
    public function addPersonTeamAssoc($personId, $teamId, $org, $role = null)
    {
        $em = $this->getEntityManager();

        $teamMember = new TeamMembers();
        $teamMember->setPerson($em->getReference(Person::class, $personId));
        $teamMember->setTeamId($em->getReference(Teams::class, $teamId));
        $teamMember->setOrganization($org);

        if ($role == "1") {
            $teamMember->setIsTeamLeader(true);
        }

        $this->persist($teamMember);
        $this->flush();
    }

    /**
     * @param int $personId
     * @param int $teamId
     */
    public function removePersonTeamAssoc($personId, $teamId)
    {
        /** @var TeamMembers[] $teamMembers */
        $teamMembers = $this->findBy([
            'person' => $personId,
            'teamId' => $teamId,
        ]);

        foreach ($teamMembers as $teamMember) {
            $this->delete($teamMember);
        }

        $this->flush();
    }
    
    public function getFacultyTeamMembers($orgId , $teamIds){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(tm.person) as faculty_id ');
        $qb->from(TeamsConstant::TEAM_MEMBER_REPO, 'tm');
        
        $qb->where('tm.organization = :organization');
        if(!is_null($teamIds)){
        
            $qb->andwhere( 'tm.teamId IN ( :teams )');
            $qb->setParameters(array(
                'organization' => $orgId,
                'teams' => $teamIds
            ));
        
        }else{
        
            $qb->setParameters(array(
               'organization' => $orgId,
            ));
        }
        
        $query = $qb->getQuery();
        $results = $query->getArrayResult();
        
        return $results;
        
    }
    
}
