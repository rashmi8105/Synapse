<?php
namespace Synapse\CoreBundle\Repository;

use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\RestBundle\Entity\Response;
use Synapse\RestBundle\Entity\Error;
use Synapse\CoreBundle\Entity\Teams;
use Synapse\CoreBundle\Entity\TeamMembers;
use Synapse\CoreBundle\Util\Constants\TeamsConstant;

class TeamsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:Teams';

    public function createNewTeam(Teams $teams)
    {
        $em = $this->getEntityManager();
        
        $em->persist($teams);
        
        return $teams;
    }

    /**
     * Get teams with details for an organization
     *
     * @param int $organizationId
     * @return array|null
     */
    public function getTeams($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];
        $sql = "
            SELECT
                t.id AS team_id,
                t.team_name,
                t.modified_at,
                SUM(CASE
                    WHEN tm.is_team_leader = 1 THEN 1
                    ELSE 0
                END) AS team_no_leaders,
                SUM(CASE
                    WHEN
                        (tm.is_team_leader != 1 OR 
                        (tm.is_team_leader IS NULL AND tm.id IS NOT NULL))
                    THEN 1
                    ELSE 0
                END) AS team_no_members
            FROM
                Teams t
                    LEFT JOIN
				team_members tm ON t.id = tm.teams_id
                    AND tm.deleted_at IS NULL
            WHERE
                t.organization_id = :organizationId
                    AND t.deleted_by IS NULL
                    AND t.deleted_at IS NULL
            GROUP BY team_id";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters);
        return $resultSet;
    }

    public function getfacultyTeams($orgId,$personId){
        
        
        $em = $this->getEntityManager();
        
        $qb = $em->createQueryBuilder()
        ->select('t.id team_id,t.teamName team_name,t.modifiedAt modified_at,SUM(CASE WHEN m.isTeamLeader =1 then 1 ELSE 0 END) team_no_leaders,SUM(CASE WHEN m.isTeamLeader =0 then 1 ELSE 0 END) team_no_members')
        ->from(TeamsConstant::TEAM_REPO, 't')
        ->leftJoin(TeamsConstant::TEAM_MEMBER_REPO, 'm', \Doctrine\ORM\Query\Expr\Join::WITH, 't.id = m.teamId')
        ->where('t.organization = :organization')
        ->andWhere('m.person = :personId')
        ->andWhere('m.isTeamLeader = :teamLeader ')
        ->andWhere('t.deletedBy IS NULL  group by t.id')
        
        ->setParameters(array(
            'organization' => $orgId,
            'personId' => $personId,
            'teamLeader' => 1
        ))
        ->getQuery();
        $resultSet = $qb->getResult();
        
        return $resultSet;
    }
    
    
    public function deleteTeam(Teams $teamInstance)
    {
        $em = $this->getEntityManager();
        
        $em->remove($teamInstance);
        
        return $teamInstance;
    }

    public function updateTeams(Teams $team)
    {
        $em = $this->getEntityManager();
        
        $em->merge($team);
        
        return $team;
    }

    /**
     * Gets team members for a team
     *
     * @param int $teamId
     * @return array
     */
    public function getTeamMembers($teamId)
    {
        $parameters = [
            'teamId' => $teamId
        ];

        $sql = "
            SELECT DISTINCT
                p.id AS person_id,
                p.firstname AS first_name,
                p.lastname AS last_name,
                (CASE
                    WHEN tm.is_team_leader = 1 THEN 1
                    ELSE 0
                END) AS is_leader,
                0 AS action
            FROM
                Teams t
                    LEFT JOIN
                team_members tm ON tm.teams_id = tm.teams_id
                    AND tm.deleted_at IS NULL
                    LEFT JOIN
                person p ON p.id = tm.person_id
                    AND p.deleted_at IS NULL
            WHERE
                tm.teams_id = :teamId
                    AND tm.deleted_by IS NULL
                    AND t.deleted_at IS NULL";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters);
        return $resultSet;
    }

    /**
     * Gets Team details for a team
     *
     * @param $teamId
     * @return array|null
     */
    public function getTeamDetails($teamId)
    {
        $parameters = [
            'teamId' => $teamId
        ];
        $sql = "
            SELECT
                t.id AS team_id, t.team_name, t.modified_at
            FROM
                Teams t
                    LEFT JOIN
                team_members tm ON t.id = tm.teams_id
                    AND tm.deleted_at IS NULL
                    LEFT JOIN
                person p ON p.id = tm.person_id
                    AND p.deleted_at IS NULL
            WHERE
                t.id = :teamId AND tm.deleted_by IS NULL
                    AND t.deleted_at IS NULL
            LIMIT 1";
        $resultSet = $this->executeQueryFetch($sql, $parameters);
        return $resultSet;
    }

    /**
     *
     * @param BaseEntity $asset            
     * @return array
     */
    public function getTeamsByAsset(BaseEntity $asset)
    {
        $em = $this->getEntityManager();
        
        // Get the name of the Entity
        
        $assetName = $em->getClassMetadata(get_class($asset))->getName();
        if ($assetName === 'Synapse\CoreBundle\Entity\Note') {
            $assetTeam = "NoteTeams";
            $assetKey = 'noteId';
            $joinWith = 'teamsId'; 
        } elseif ($assetName === 'Synapse\CoreBundle\Entity\Contacts') {
            $assetTeam = "ContactsTeams";
            $assetKey = "contactsId";
            $joinWith = 'teamsId';
        } elseif(($assetName === 'Synapse\CoreBundle\Entity\Referrals')) {
            $assetTeam = "ReferralsTeams";
            $assetKey = "referrals";
            $joinWith = 'teams';
        }
        elseif(($assetName === 'Synapse\CoreBundle\Entity\Appointments')) {
            $assetTeam = "AppointmentsTeams";
            $assetKey = "appointmentsId";
            $joinWith = 'teamsId';
        }
        elseif(($assetName === 'Synapse\CoreBundle\Entity\Email')) {
            $assetTeam = "EmailTeams";
            $assetKey = "emailId";
            $joinWith = 'teamsId';
        }
        else{
            throw new \LogicException("No logic for asset type '$assetName'.");
        }
        
        $query = $em->createQueryBuilder()
            ->select('t.id team_id, t.teamName team_name')
            ->from(TeamsConstant::TEAM_REPO, 't', 't.id')
            ->join("SynapseCoreBundle:$assetTeam", 'at', \Doctrine\ORM\Query\Expr\Join::WITH, "at.$joinWith=t.id")
                ->where('at.'.$assetKey.' = ' . $asset->getId())
                ->getQuery();

        $teams = $query->getResult();
        
        return $teams;
    }
}
