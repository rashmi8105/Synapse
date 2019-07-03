<?php
namespace Synapse\ReportsBundle\Repository;

use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\ReportsBundle\Entity\ReportsRunningStatus;
use Synapse\CoreBundle\Repository\SynapseRepository;

class ReportsRunningStatusRepository extends SynapseRepository
{

	const REPOSITORY_KEY = 'SynapseReportsBundle:ReportsRunningStatus';


	/**
	 * Finds an entity by its primary key / identifier.
	 * Override added to inform PhpStorm about the return type.
	 *
	 * @param mixed $id The identifier.
	 * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
	 *                              or NULL if no specific lock mode should be used
	 *                              during the search.
	 * @param int|null $lockVersion The lock version.
	 *
	 * @return ReportsRunningStatus
	 */
	public function find($id, $lockMode = null, $lockVersion = null)
	{
		return parent::find($id, $lockMode, $lockVersion);
	}


	/**
     *
     * @param Reports $Reports            
     * @return Reports
     */
	public function getReportsForStudent($organization, $person, $limit, $offset)
	{
		$em = $this->getEntityManager();
        $qb = $em->createQueryBuilder(); 
		$qb->select('rs.id as id, rs.createdAt as createdDate, IDENTITY(rs.organization) as orgId, IDENTITY(rs.person) as person, rs.isViewed as is_viewed, rs.reportCustomTitle as title, rs.status as reportStatus, IDENTITY(rs.reports) as report_id, rp.shortCode as short_code');
        $qb->from('SynapseReportsBundle:ReportsRunningStatus', 'rs'); 
		$qb->LEFTJoin('SynapseReportsBundle:Reports', 'rp', \Doctrine\ORM\Query\Expr\Join::WITH, 'rs.reports = rp.id');
		$qb->where('rs.organization = :organization');
		$qb->andWhere('rs.person = :person');
		$qb->setParameters(array(
            'organization' => $organization,
            'person' =>  $person   
        ));  
		$qb->orderBy('rs.createdAt', 'DESC');
		$qb->setMaxResults($limit);
        $qb->setFirstResult($offset);
		$query = $qb->getQuery();        
        return $query->getArrayResult();
	}
	
	public function create(ReportsRunningStatus $reportsRunningStatus)
    {
        $em = $this->getEntityManager();
        $em->persist($reportsRunningStatus);
        return $reportsRunningStatus;
    }

    /**
     * Get filtered students
     *
     * TODO:: This pattern of passing in a query to a repository function should not be repeated in future development.
     *
     * @param string $saveSearch
     * @param integer $organizationId
     * @return array
     */
    public function getFilteredStudents($saveSearch, $organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "SELECT DISTINCT
                    id AS person_id
                FROM
                    person p
                WHERE
                    p.deleted_at IS NULL
                    AND p.organization_id = :organizationId
                    AND $saveSearch";

        $resultSet = $this->executeQueryFetchAll($sql, $parameters);
        return $resultSet;
    }

    /**
     * Get Report data with last run date
     *
     * @param int $organizationId
     * @param int $reportId
     * @param int $personId
     * @return array|null
     * @throws SynapseDatabaseException
     */
    public function getLastRunDateForMyReport($organizationId, $reportId, $personId)
    {
        $parameters = ['organizationId' => $organizationId, 'reportId' => $reportId, 'personId' => $personId];
        $sql = "SELECT
                    rrs.modified_at
                FROM
                    reports_running_status AS rrs
                WHERE
                    rrs.org_id = :organizationId
                    AND rrs.report_id = :reportId
                    AND rrs.person_id = :personId
                    AND rrs.deleted_at IS NULL
                ORDER BY
                    rrs.modified_at DESC
                LIMIT 1";
        $resultSet = $this->executeQueryFetch($sql, $parameters);
        return $resultSet;
    }
}
