<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\ORM\Mapping\ClassMetadata as ORM_Mapping;
use Synapse\CoreBundle\Entity\OrganizationRole;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\PersonConstant;

class OrganizationRoleRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrganizationRole';

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    private $OrgRoleEntity = 'SynapseCoreBundle:OrganizationRole';

    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param mixed $id The identifier.
     * @param \Exception $exception
     * @param int|null $lockMode One of the \Doctrine\DBAL\LockMode::* constants
     *                           or NULL if no specific lock mode should be used
     *                           during the search.
     * @param int|null $lockVersion The lock version.
     * @return OrganizationRole|null
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
     * @return OrganizationRole|null
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
     * @return OrganizationRole|null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null){

        $object = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($object, $exception);
    }


    public function createCoordinator($orgRole)
    {
        $em = $this->getEntityManager();
        $em->persist($orgRole);
        return $orgRole;
    }

    public function remove(OrganizationRole $orgRole)
    {
        $em = $this->getEntityManager();
        $em->remove($orgRole);
    }

    public function role($role)
    {
        $em = $this->getEntityManager();
        if ($role) {
            $orgRole = $em->createQueryBuilder()
                ->select('t.id')
                ->from('SynapseCoreBundle:Role', 't')
                ->where('t.id = :id')
                ->andWhere('t.deletedAt IS NULL')
                ->setParameters(array(
                'id' => $role->getRole()
            ))
                ->getQuery()
                ->getSingleScalarResult();
            return $orgRole;
        }
    }

    /**
     * Gets one organization role object for a given person ID, if it exists
     *
     * TODO: Remove this code, it's just calling a repository findOneBy
     *
     * @param $organizationId
     * @param $personId
     * @return OrganizationRole|null
     * @deprecated Use findOneBy instead
     */
    public function getUserCoordinatorRole($organizationId, $personId)
    {
        $coordinator = $this->findOneBy([
            'organization' => $organizationId,
            'person' => $personId
        ]);
        return $coordinator;
    }



    /**
     * Finds the list of coordinators for an organization in Array format
     *
     * @param integer $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getCoordinatorsArray($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];
	    
        $sql = "SELECT 
            p.id,
            p.welcome_email_sent_date,
            ci.primary_mobile,
            ci.home_phone,
            p.firstname,
            p.lastname,
            p.username AS email,
            p.external_id AS externalid,
            p.title,
            orgr.role_id AS roleid,
            role_name AS role,
	    orgr.modified_at 
        FROM
            organization_role orgr
                INNER JOIN
            person p ON orgr.person_id = p.id
                INNER JOIN
            role_lang rl 
	    	ON rl.id = orgr.role_id
                LEFT JOIN
            person_contact_info pci 
		ON p.id = pci.person_id 
		AND pci.deleted_at IS NULL
                LEFT JOIN
            contact_info ci 
		ON ci.id = pci.contact_id 
		AND ci.deleted_at IS NULL
        WHERE
            orgr.organization_id = :organizationId
                AND rl.role_name IN ('Primary coordinator', 'Technical coordinator','Non Technical coordinator')
                AND orgr.deleted_at IS NULL
                AND p.deleted_at IS NULL	
                AND rl.deleted_at IS NULL";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $resultSet = $stmt->fetchAll();
        return $resultSet;

    }

    /**
     * Gets all the Service accounts for the organization
     *
     * @param integer $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getServiceAccountsForOrganization($organizationId){


        $parameters = [
            'organizationId' => $organizationId,
            'serviceAccountRoleName' => SynapseConstant::SERVICE_ACCOUNT_ROLE_NAME
        ];
	    
        $sql = "SELECT 
            p.id,
            p.lastname,
            orgr.role_id AS roleid,
            role_name AS role,
            CONCAT(c.id , '_',c.random_id) AS client_id,
            c.secret AS client_secret,
            ac.modified_at
        FROM
            organization_role orgr
                INNER JOIN
            person p ON orgr.person_id = p.id
                INNER JOIN
            role_lang rl ON rl.id = orgr.role_id
                INNER JOIN
            Client c  ON p.id = c.person_id
                INNER JOIN 
            AuthCode ac ON p.id = ac.user_id
        WHERE
            orgr.organization_id = :organizationId
                AND rl.role_name = :serviceAccountRoleName
                AND orgr.deleted_at IS NULL
                AND p.deleted_at IS NULL	
                AND rl.deleted_at IS NULL
                AND c.deleted_at IS NULL
                AND ac.deleted_at IS NULL";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $resultSet = $stmt->fetchAll();
        return $resultSet;

    }


    public function getPrimaryTierCoordinators($organizationId, $filter = '')
    {
        $coordinatorRoleIds = $this->getCoordinatorRoleID();
        if ($filter == '') {
            $dql = <<<DQL
SELECT orgr, p, ci, r, rl.roleName
FROM SynapseCoreBundle:OrganizationRole orgr
JOIN orgr.person p
LEFT JOIN p.contacts ci
INNER JOIN orgr.role r
INNER JOIN SynapseCoreBundle:RoleLang rl WITH rl.role=r
WHERE r.id IN (:coordinatorRoleIds)
AND orgr.organization IN (:organizationId)
AND orgr.deletedAt IS NULL
AND r.deletedAt Is NULL
ORDER BY p.firstname, p.lastname
DQL;
        } else {
            $dql = <<<DQL
SELECT orgr, p, ci, r, rl.roleName
FROM SynapseCoreBundle:OrganizationRole orgr
JOIN orgr.person p
LEFT JOIN p.contacts ci
INNER JOIN orgr.role r
INNER JOIN SynapseCoreBundle:RoleLang rl WITH rl.role=r
WHERE r.id IN (:coordinatorRoleIds)
AND orgr.organization IN (:organizationId)
AND orgr.deletedAt IS NULL
AND r.deletedAt Is NULL
AND (p.firstname LIKE :filter OR p.lastname LIKE :filter OR ci.primaryEmail LIKE :filter OR p.externalId LIKE :filter)
ORDER BY p.firstname, p.lastname
DQL;
        }
        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setFetchMode(self::REPOSITORY_KEY, 'role', ORM_Mapping::FETCH_EAGER)
            ->setFetchMode(self::REPOSITORY_KEY, PersonConstant::PERSON, ORM_Mapping::FETCH_EAGER)
            ->setFetchMode('SynapseCoreBundle:Person', 'contacts', ORM_Mapping::FETCH_EAGER)
            ->setParameter('coordinatorRoleIds', $coordinatorRoleIds)
            ->setParameter('organizationId', $organizationId);
        if ($filter != '') {
            $query = $query->setParameter('filter', '%' . $filter . '%');
        }
        $returnSet = array();
        $data = $query->getResult();
        foreach ($data as $idx => $row) {
            /**
             *
             * @var OrganizationRole $coord
             */
            $coord = $row[0];
            $coord->getRole()->setName($row['roleName']);
            $returnSet[] = $coord;
        }
        return $returnSet;
    }

    /**
     * Gets the person IDs of coordinator users who have not logged in to Mapworks
     *
     * @param int $organizationId
     * @return array
     */
    public function getNonLoggedInCoordinator($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "
                SELECT 	
                    DISTINCT orl.person_id
                FROM 
                    organization_role orl 
                        JOIN 
                    org_person_faculty opf 
                            ON orl.organization_id = opf.organization_id
                            AND orl.person_id = opf.person_id
                        LEFT JOIN
                    access_log al 
                            ON al.organization_id = :organizationId
                            AND al.person_id = orl.person_id 
                            AND al.event = 'Login'
                            AND al.deleted_at IS NULL 
                WHERE 
                    orl.deleted_at IS NULL 
                    AND opf.deleted_at IS NULL
                    AND al.id IS NULL
                    AND orl.organization_id = :organizationId
                    AND (opf.status = 1 OR opf.status IS NULL);
        ";

        $result = $this->executeQueryFetchAll($sql, $parameters);
        return $result;
    }


    /**
     * Gets the count of non-logged in coordinator users for the specified organization
     *
     * @param int $organizationId
     * @return int
     */
    public function getNonLoggedInCoordinatorCount($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "
                SELECT 	
                    COUNT(DISTINCT orl.person_id) AS count 
                FROM 
                    organization_role orl 
                        JOIN 
                    org_person_faculty opf 
                            ON orl.organization_id = opf.organization_id
                            AND orl.person_id = opf.person_id
                        LEFT JOIN
                    access_log al 
                            ON al.organization_id = :organizationId
                            AND al.person_id = orl.person_id 
                            AND al.event = 'Login'
                            AND al.deleted_at IS NULL 
                WHERE 
                    orl.deleted_at IS NULL 
                    AND opf.deleted_at IS NULL 
                    AND al.id IS NULL
                    AND orl.organization_id = :organizationId
                    AND (opf.status = 1 OR opf.status IS NULL);
        ";

        $result = $this->executeQueryFetchAll($sql, $parameters);
        return $result[0]['count'];
    }

    /**
     * Gets the ID numbers for coordinator roles.
     *
     * @deprecated
     * @return array Nested array of coordinator role IDs.
     */
    public function getCoordinatorRoleID()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $queryBuilder = $qb->select('rolelang.id')
            ->from('SynapseCoreBundle:RoleLang', 'rolelang')
            ->where("rolelang.roleName IN('Primary coordinator', 'Technical coordinator','Non Technical coordinator')")
            ->getQuery();
        $resultSet = $queryBuilder->getResult();
        return $resultSet;
    }

    public function getCoordinatorRolesByLangId($langId)
	{
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$queryBuilder = $qb->select('rl.id', 'rl.roleName')
			->from('SynapseCoreBundle:RoleLang', 'rl')
			->where('rl.roleName LIKE :roleType')
			->andWhere('rl.lang = :lang')
			->setParameters(array(
			'roleType' => '%coordinator',
			'lang' => $langId
		))
			->getQuery();
		return $queryBuilder->getResult();
	}

    public function getAllAdminUsers(){

	    $em = $this->getEntityManager();

	    $sql = "select p.id as admin_id,p.firstname,p.lastname,p.username as email,rlang.role_name
                ,max(al.date_time) as last_login ,ci.primary_email,ci.home_phone
                from person p
                left join person_contact_info pci on p.id =  pci.person_id
                left join contact_info ci on pci.contact_id = ci.id
                inner join organization_role orl on p.id = orl.person_id
                inner join role_lang rlang on orl.role_id = rlang.role_id and rlang.role_name in('Mapworks Admin','Skyfactor Admin')
                left join access_log al on p.id = al.person_id
	            where p.deleted_at is null
                group by p.id order by p.id desc";

	    $resultSet = $em->getConnection()->fetchAll($sql);
	    return $resultSet;
	}


    /**
     * Get single primary coordinator id based on organization id and order by firstname, lastname, username, and id
     *
     * @param int $organizationId
     * @return array
     */
    public function findFirstPrimaryCoordinatorIdAlphabetically($organizationId)
    {
        $parameters = ['organizationId' => $organizationId];

        $sql = "SELECT
                    o_r.person_id
                FROM
                    role_lang rl
                        INNER JOIN
                    organization_role o_r ON rl.role_id = o_r.role_id
                        INNER JOIN
                    person p ON p.id = o_r.person_id
                WHERE
                    o_r.organization_id =:organizationId
                    AND rl.role_name = 'Primary coordinator'
                    AND o_r.deleted_at IS NULL
                    AND rl.deleted_at IS NULL
                    AND p.deleted_at IS NULL
                ORDER BY p.lastname, p.firstname, p.username, p.id
                LIMIT 1";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $resultSet = $stmt->fetch();
        if ($resultSet) {
            $coordinatorId = $resultSet['person_id'];
        } else {
            $coordinatorId = null;
        }
        return $coordinatorId;
    }

    /**
     * Gets the list of coordinators in objects using doctrine. This method has been updated for using plain SQL,
     * but as this method was being used widely through out the application removing was breaking multiple things
     * As we move ahead with development the usage of the below method should be replaced with getCoordinatorsArray method
     *
     * @deprecated
     * @param $organizationId
     * @return OrganizationRole[]
     */
    public function getCoordinators($organizationId)
    {
        $coordinatorRoleIds = $this->getCoordinatorRoleID();
        $dql = <<<DQL
SELECT orgr, p, ci, r, rl.roleName
FROM SynapseCoreBundle:OrganizationRole orgr
JOIN orgr.person p
LEFT JOIN p.contacts ci
INNER JOIN orgr.role r
INNER JOIN SynapseCoreBundle:RoleLang rl WITH rl.role=r
WHERE r.id IN (:coordinatorRoleIds)
AND orgr.organization = :organizationId
AND p.organization != -1
AND orgr.deletedAt IS NULL
AND r.deletedAt Is NULL
ORDER BY p.firstname, p.lastname
DQL;
        $query = $this->getEntityManager()
            ->createQuery($dql)
            ->setFetchMode(self::REPOSITORY_KEY, 'role', ORM_Mapping::FETCH_EAGER)
            ->setFetchMode(self::REPOSITORY_KEY, PersonConstant::PERSON, ORM_Mapping::FETCH_EAGER)
            ->setFetchMode('SynapseCoreBundle:Person', 'contacts', ORM_Mapping::FETCH_EAGER)
            ->setParameter('coordinatorRoleIds', $coordinatorRoleIds)
            ->setParameter('organizationId', $organizationId);
        $returnSet = array();
        $data = $query->getResult();
        foreach ($data as $idx => $row) {
            /**
             *
             * @var OrganizationRole $coord
             */
            $coord = $row[0];
            $coord->getRole()->setName($row['roleName']);
            $returnSet[] = $coord;
        }

        return $returnSet;
    }


}
