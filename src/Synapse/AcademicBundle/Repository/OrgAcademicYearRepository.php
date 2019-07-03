<?php
namespace Synapse\AcademicBundle\Repository;

use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;

class OrgAcademicYearRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseAcademicBundle:OrgAcademicYear';

    /**
     * How many programmers does it take to change a light bulb?
     * None, that's a hardware problem.
     *
     * @deprecated - Use Repository::REPOSITORY_KEY
     */
    const ORG_ACADEMIC_YEAR_REPO = 'SynapseAcademicBundle:OrgAcademicYear';

    const ORG = "organization";

    const STARTDATE_CURDATE = 'oay.startDate <= :currDate';

    const ENDDATE_CURDATE = 'oay.endDate >= :currDate';

    const CURDATE = 'currDate';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param \Exception $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                           or NULL if no specific lock mode should be used
     *                           during the search.
     * @param int|null $lockVersion The lock version.
     * @return OrgAcademicYear|null
     * @throws \Exception
     */
    public function find($id, $exception = null, $lockMode = null, $lockVersion = null)
    {
        $object = parent::find($id, $lockMode, $lockVersion);
        return $this->doesObjectExist($object, $exception);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param \Exception $exception
     * @return OrgAcademicYear[]|null
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $object = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($object, $exception);
    }

    /**
     *  Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param \Exception $exception
     * @param array|null $orderBy
     * @return OrgAcademicYear|null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null){
        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }

    public function deleteAcademicYear($academicYear)
    {
        $em = $this->getEntityManager();
        $em->remove($academicYear);
        return $academicYear;
    }

    public function getOrgAcademicYearsDetails($organization, $id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('oay.id,oay.startDate,oay.endDate');
        $qb->from(self::ORG_ACADEMIC_YEAR_REPO, 'oay');
        $qb->where('oay.organization = :organization AND oay.id !=:id');
        $qb->setParameters(array(
            self::ORG => $organization,
            "id" => $id
        ));
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        return $result;
    }

    /**
     * Get current year id for the organization
     *
     * @param string $currentDate
     * @param int $organizationId
     * @param null|int $organizationAcademicYearId
     * @return array
     */
    public function getCurrentYearId($currentDate, $organizationId, $organizationAcademicYearId = null)
    {
        $organizationAcademicYearId = trim($organizationAcademicYearId);
        $parameters = [
            'organizationId' => $organizationId,
            'currentDate' => $currentDate,
            'organizationAcademicYearId' => $organizationAcademicYearId
        ];

        $queryCondition = '';
        if (empty($organizationAcademicYearId)) {
            $queryCondition .= ' AND oay.start_date <= :currentDate AND oay.end_date >= :currentDate';
        } else {
            $queryCondition .= ' AND oay.id = :organizationAcademicYearId';
        }

        $sql = "SELECT
                    oay.id,
                    oay.year_id AS yearId
                FROM
                    org_academic_year oay
                WHERE
                    oay.organization_id = :organizationId AND oay.deleted_at IS NULL $queryCondition";

        $resultSet = $this->executeQueryFetchAll($sql, $parameters);
        return $resultSet;
    }


    /**
     * Returns data about the current academic year for the given organization.
     * Returns null if there is no current academic year.
     *
     * @param int $organizationId
     * @return array
     */
    public function getCurrentAcademicYear($organizationId)
    {
        $parameters = ['organizationId' => $organizationId];

        $sql = "SELECT
                    id AS org_academic_year_id,
                    year_id,
                    name AS year_name,
                    start_date,
                    end_date
                FROM
                    org_academic_year
                WHERE
                    organization_id = :organizationId
                    AND NOW() BETWEEN start_date AND end_date
                    AND deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            if ($results) {
                $results = $results[0];
            }

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        return $results;
    }


    public function getCurrentAcademicDetails($currentDate, $organization)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('oay.id as id', 'IDENTITY(oay.yearId) as yearId,max(oay.startDate) AS startDate', 'max(oay.endDate) as endDate', 'oay.name as year_name');
        $qb->from(self::ORG_ACADEMIC_YEAR_REPO, 'oay');
        $qb->where('oay.organization = :organization');
        $qb->andWhere(self::STARTDATE_CURDATE);
        $qb->andWhere(self::ENDDATE_CURDATE);
        $qb->setParameters(array(
            self::CURDATE => $currentDate,
            self::ORG => $organization
        ));
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        return $result;
    }

    public function getCountCurrentAcademic($currentDate, $organization)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(oay.id) as oayCount');
        $qb->from(self::ORG_ACADEMIC_YEAR_REPO, 'oay');
        $qb->where('oay.organization = :organization');
        $qb->andWhere(self::STARTDATE_CURDATE);
        $qb->andWhere(self::ENDDATE_CURDATE);
        $qb->setParameters(array(
            self::CURDATE => $currentDate,
            self::ORG => $organization
        ));
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }
    
    
    public function findFutureYears($orgId,$startDate,$limit = 3){
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('oay.id');
        $qb->from(self::ORG_ACADEMIC_YEAR_REPO, 'oay');
        $qb->where('oay.organization = :organization');
        $qb->andWhere('oay.startDate >= :startDate ');
        $qb->orderBy('oay.startDate', 'ASC');
        $qb->setMaxResults($limit);
        $qb->setParameters(array(
            'startDate' => $startDate,
            self::ORG => $orgId
        ));
        $query = $qb->getQuery();
        $result = $query->getResult();

        $finalRes =  array();
        foreach($result as $res){
            $finalRes[] =  $res['id'];
        }
        return $finalRes;
        
    }


    /**
     * Determines whether the given $orgAcademicYearId represents a past, current, or future academic year on the given $date.
     * If no date is passed in, uses the current date (in UTC).
     *
     * @param int $organizationAcademicYearId
     * @param string $date - 'yyyy-mm-dd' format
     * @return string - 'past' or 'current' or 'future'
     */
    public function determinePastCurrentOrFutureYear($organizationAcademicYearId, $date = null)
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $parameters = [
            'date' => $date,
            'organizationAcademicYearId' => $organizationAcademicYearId
        ];

        $sql = "SELECT
                    CASE
                        WHEN :date > end_date THEN 'past'
                        WHEN :date BETWEEN start_date AND end_date THEN 'current'
                        WHEN :date < start_date THEN 'future'
                    END AS tense
                FROM
                    org_academic_year
                WHERE
                    id = :organizationAcademicYearId
                    AND deleted_at IS NULL";

        $resultSet = $this->executeQueryFetchAll($sql, $parameters);
        $result = '';
        if ($resultSet) {
            $result = $resultSet[0]['tense'];
        }
        return $result;
    }


    /**
     * Uses the current date to retrieve the current academic year or the most recent previous
     * academic year
     *
     * @param string $currentDateTimeString Ex: 'YYYY-MM-DD hh:mm:ss'
     * @param integer $organizationId
     * @return array
     */
    public function getCurrentOrPreviousAcademicYearUsingCurrentDate($currentDateTimeString, $organizationId)
    {
        $parameters = ['currentDateTime' => $currentDateTimeString, 'organizationId' => $organizationId];

        $sql = "SELECT
                  id AS org_academic_year_id,
                  year_id AS year_id,
                  start_date,
                  end_date,
                  name AS year_name
                FROM
                  org_academic_year
                WHERE
                  organization_id = :organizationId
                  AND start_date <= DATE(:currentDateTime)
                  AND deleted_at IS NULL
                ORDER BY end_date DESC
                LIMIT 1;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $results = $stmt->fetchAll();
        return $results;

    }

    /**
     * Get list of Academic Years for an organization within a date range
     *
     * @param int $organizationId
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getAcademicYearsWithinSpecificDateRange($organizationId, $startDate, $endDate)
    {
        $parameters = ['organizationId' => $organizationId, 'startDate' => $startDate, 'endDate' => $endDate];
        $sql = "SELECT
                  id AS org_academic_year_id
                FROM
                  org_academic_year
                WHERE
                  organization_id = :organizationId
                  AND start_date <= DATE(:startDate)
                  AND end_date >= DATE(:endDate)
                  AND deleted_at IS NULL
                ORDER BY end_date DESC;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $results = $stmt->fetchAll();
        return $results;

    }


    /**
     * Get all the academic year ids for an organization
     *
     * @param integer $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getAllAcademicYearsForOrganization($organizationId)
    {
        $parameters = ['organizationId' => $organizationId];
        $sql = "SELECT 
                  year_id 
                FROM 
                  org_academic_year 
                WHERE  
                  organization_id = :organizationId
                  AND deleted_at IS NULL";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $resultArray =  $stmt->fetchAll();
            $result = array_column($resultArray, 'year_id');
            return $result;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }


    /**
     * Gets the past and the current academic years for an organization (excludes future years)
     *
     * @param integer $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getPastAndCurrentAcademicYearNames($organizationId)
    {

        $parameters = ['organizationId' => $organizationId];

        $sql = "SELECT 
                  `name` 
                FROM 
                  org_academic_year 
                WHERE 
                  organization_id = :organizationId 
                  AND start_date <= NOW()  
                  AND deleted_at IS NULL";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $resultArray = $stmt->fetchAll();
            $result = array_column($resultArray, 'name');
            return $result;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    /**
     * Gets all the academic years and their respective terms for an organization.
     *
     * @param int $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getAllAcademicYearsWithTerms($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "SELECT
                    oay.id,
                    oay.`name` AS year_name,
                    oay.year_id,
                    oay.start_date AS year_start_date,
                    oay.end_date AS year_end_date,
                    CASE
                        WHEN DATE(NOW()) > oay.end_date THEN 'past'
                        WHEN DATE(NOW()) BETWEEN oay.start_date AND oay.end_date THEN 'current'
                        WHEN DATE(NOW()) < oay.start_date THEN 'future'
                    END AS year_status,
                    oat.id AS term_id,
                    oat.`name` AS term_name,
                    oat.start_date AS term_start_date,
                    oat.end_date AS term_end_date,
                    oat.term_code
                FROM
                    org_academic_year oay
                        LEFT JOIN
                    org_academic_terms oat ON oat.org_academic_year_id = oay.id
                        AND oat.organization_id = oay.organization_id
                        AND oat.deleted_at IS NULL
                WHERE
                    oay.organization_id = :organizationId
                        AND oay.deleted_at IS NULL
                    ORDER BY oay.start_date DESC, oay.id DESC, oat.start_date DESC, oat.id DESC";

        $results = $this->executeQueryFetchAll($sql, $parameters);
        return $results;
    }
}