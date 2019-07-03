<?php
namespace Synapse\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\RestBundle\Entity\Error;

class PersonRepository extends SynapseRepository
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:Person';

    /**
     * @DI\Inject("logger")
     */
    private $logger;

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
     * @return Person|null
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
     * @return null|Person
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
     * @return Person[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $exception = null)
    {
        $objectArray = parent::findBy($criteria, $orderBy, $limit, $offset);
        return $this->doObjectsExist($objectArray, $exception);
    }


    public function createPerson(Person $person)
    {
        $em = $this->getEntityManager();
        $em->persist($person);
        return $person;
    }

    public function remove(Person $person)
    {
        $em = $this->getEntityManager();
        $em->remove($person);
    }

    public function sendPersonEmailActivation($personID)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder()
            ->select('partial s.{id, activationToken}', 'p')
            ->from('SynapseCoreBundle:PersonContactInfo', 'p')
            ->leftJoin('p.person', 's')
            ->where('p.person = :personid')
            ->setParameters(array(
            'personid' => $personID
        ))
            ->getQuery();
        return $qb->getResult();
    }


    public function getRoleById($id)
    {
        $em = $this->getEntityManager();
        $role = $em->getRepository('SynapseCoreBundle:Role')->findOneBy(array(
            'id' => $id
        ));
        if (! isset($role)) {
            return new Error("validation_error", "Role Not Found");
        }

        return $role;
    }

    public function findOneByExternalId($externalId)
    {
        $em = $this->getEntityManager();
        $person = $em->getRepository(PersonConstant::PERSON_REPO)->findOneBy(array(
            PersonConstant::EXT_ID => $externalId
        ));
        return $person;
    }

    public function findByExternalId($externalIds)
    {
        $em = $this->getEntityManager();
        $persons = $em->getRepository(PersonConstant::PERSON_REPO)->findBy(array(
            PersonConstant::EXT_ID => $externalIds
        ));
        return $persons;
    }

    public function getStaffSearchByFirstName($organization, $searchText)
    {
        $faculty = $this->getFacultyId();
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('p.id', PersonConstant::PERSON_FIRSTNAME, PersonConstant::PERSON_LASTNAME);
        $qb->from(PersonConstant::PERSON_REPO, 'p');
        $qb->leftJoin(PersonConstant::PERSON_ENTITIES, 'e');
        $qb->leftJoin(PersonConstant::PERSON_ORG, 'o');
        $qb->where(PersonConstant::ID_EQUAL_ENTITY, PersonConstant::ID_EQUAL_ORG, 'p.firstname LIKE :firstname');

        $qb->setParameters(array(
            PersonConstant::FIELD_ORGANIZATION => $organization,
            'firstname' => "%" . $searchText . "%",
            PersonConstant::ENTITY => $faculty
        ));
        $query = $qb->getQuery();

        $result = $query->getArrayResult();
        return $result;
    }

    public function getStaffSearchByLastName($organization, $searchText)
    {
        $faculty = $this->getFacultyId();
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('p.id', PersonConstant::PERSON_FIRSTNAME, PersonConstant::PERSON_LASTNAME);
        $qb->from(PersonConstant::PERSON_REPO, 'p');
        $qb->leftJoin(PersonConstant::PERSON_ENTITIES, 'e');
        $qb->leftJoin(PersonConstant::PERSON_ORG, 'o');
        $qb->where(PersonConstant::ID_EQUAL_ENTITY, PersonConstant::ID_EQUAL_ORG, 'p.lastname LIKE :lastname');

        $qb->setParameters(array(
            PersonConstant::FIELD_ORGANIZATION => $organization,
            'lastname' => "%" . $searchText . "%",
            PersonConstant::ENTITY => $faculty
        ));
        $query = $qb->getQuery();

        $result = $query->getArrayResult();
        return $result;
    }

    public function getStaffSearchByPrimaryEmail($orgId, $searchText)
    {
        $faculty = $this->getFacultyId();
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('p.id', PersonConstant::PERSON_FIRSTNAME, PersonConstant::PERSON_LASTNAME);
        $qb->from(PersonConstant::PERSON_REPO, 'p');
        $qb->leftJoin(PersonConstant::PERSON_ENTITIES, 'e');
        $qb->leftJoin(PersonConstant::PERSON_ORG, 'o');
        $qb->leftJoin(PersonConstant::PERSON_CONTACTS, 'c');
        $qb->where(PersonConstant::ID_EQUAL_ENTITY, PersonConstant::ID_EQUAL_ORG, 'c.primaryEmail LIKE :email');

        $qb->setParameters(array(
            PersonConstant::FIELD_ORGANIZATION => $orgId,
            'email' => "%" . $searchText . "%",
            PersonConstant::ENTITY => $faculty
        ));
        $query = $qb->getQuery();

        $result = $query->getArrayResult();
        return $result;
    }

    /**
     * Returns the person ID of the user associated with a given email address
     *
     * @param $email
     * @return int|null
     */
    public function findPersonIdForGivenPrimaryEmailAddress($email)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('p', 'c');
        $qb->from(PersonConstant::PERSON_REPO, 'p');
        $qb->leftJoin(PersonConstant::PERSON_CONTACTS, 'c');
        $qb->where('c.primaryEmail = :email');

        $qb->setParameters(array(
            'email' => $email
        ));
        $query = $qb->getQuery();   

        $result = $query->getArrayResult();

        if ($result) {
            return $result[0]['id'];
        } else {
            return null;
        }
    }

    public function getPersonDetails($person)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('p', 'c', 'e');
        $qb->from(PersonConstant::PERSON_REPO, 'p');
        $qb->leftJoin(PersonConstant::PERSON_CONTACTS, 'c');
        $qb->leftJoin(PersonConstant::PERSON_ENTITIES, 'e');
        $qb->where('p.id =:id');

        $qb->setParameters(array(
            'id' => $person->getId()
        ));
        $query = $qb->getQuery();

        $result = $query->getArrayResult();

        return $result;
    }

    public function getDumpByOrganizationByType($organization, $entity, $coordinatorArr)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        if (trim(strtolower($entity)) == "faculty") {
            $qb->select('op, p, c');
            $qb->from(PersonConstant::ORG_PERSON_FACULTY, 'op');
        } else {
            $qb->select('op, p, c,pc');
            $qb->from(PersonConstant::ORG_PERSON_STUDENT, 'op');
        }
        $qb->leftJoin('op.person', 'p');
        $qb->leftJoin(PersonConstant::PERSON_CONTACTS, 'c');
        $qb->leftJoin(PersonConstant::PERSON_ORG, 'o');
        if (trim(strtolower($entity)) != "faculty") {
            $qb->leftJoin('op.personIdPrimaryConnect', 'pc');
        }

        $qb->where(PersonConstant::PERSON_EQUAL_ID, PersonConstant::ID_EQUAL_ORG);
        $qb->andWhere(PersonConstant::PERSON_ORG . '!= -1');
        $qb->setParameters(array(
            PersonConstant::FIELD_ORGANIZATION => $organization
        ));
        if((count($coordinatorArr)>0) && (trim(strtolower($entity)) == "faculty")){
            $coordinators = implode(',', $coordinatorArr);
            $qb->andWhere('op.person NOT IN (' . $coordinators . ')');
        }
        $qb->orderBy(trim(PersonConstant::PERSON_LASTNAME), 'ASC');
        $qb->orderBy(trim(PersonConstant::PERSON_FIRSTNAME), 'ASC');
        $qb->groupBy('op.person');
        $query = $qb->getQuery();

        return $query->getArrayResult();
    }

    /**
     * Generate the paginated dump file data for the specified organization, and the specified file type
     *
     * @param int $orgId
     * @param string $entity
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getDumpByOrganizationByTypePaged($orgId, $entity, $limit = 15, $offset = 0)
    {
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare('CALL '.ucfirst($entity).'_Data_Dump(:orgId, :limit, :offset)');
        $stmt->execute([
            'orgId' => $orgId,
            'limit' => $limit,
            'offset' => $offset
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Fetching list of all students of an organization for a given search text
     *
     * @param int $organizationId
     * @param int $orgAcademicYearId
     * @param null|array $personIdsToExclude
     * @param null|string $searchText
     * @param null|string $participantFilter - (all, participants, non-participants)
     * @param null|string $sortBy - (external_id, student_name, email, participating, active)
     * @param null|int $limit
     * @param null|int $offset
     * @throws SynapseDatabaseException
     * @return array
     */
    public function getOrganizationStudentsBySearchText($organizationId, $orgAcademicYearId, $personIdsToExclude = NULL, $searchText = null, $participantFilter = null, $sortBy = null, $limit = null, $offset = null)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'offset' => $offset,
            'limit' => $limit,
            'orgAcademicYearId' => $orgAcademicYearId
        ];
        $parameterTypes = [
            'offset' => \PDO::PARAM_INT,
            'limit' => \PDO::PARAM_INT
        ];

        $excludeClause = '';
        if (!empty($personIdsToExclude) && $participantFilter == 'non-participants') {
            $parameters['personIdsToExclude'] = $personIdsToExclude;
            $parameterTypes['personIdsToExclude'] = Connection::PARAM_INT_ARRAY;
            $excludeClause = ' AND p.id NOT IN (:personIdsToExclude)';
        }

        $searchClause = '';
        if ($searchText) {
            $parameters['searchText'] = "%$searchText%";
            $searchClause = '
                AND (p.username LIKE :searchText
                OR p.firstname LIKE :searchText
                OR p.lastname LIKE :searchText
                OR p.external_id LIKE :searchText)';
        }

        $whereConditionCheck = '';
        if ($participantFilter == 'participants') {
            $selectParticipantStatus = ' 1 AS participant,';
            $joinOrgPersonStudentYear = " 
                        INNER JOIN org_person_student_year opsy 
                            ON opsy.person_id = ops.person_id
                            AND opsy.org_academic_year_id = :orgAcademicYearId 
                            AND opsy.deleted_at IS NULL ";
        } else if ($participantFilter == 'non-participants') {
            $selectParticipantStatus = ' 0 AS participant,';
            $joinOrgPersonStudentYear = " 
                            LEFT  JOIN org_person_student_year opsy 
                            ON opsy.person_id = ops.person_id
                            AND opsy.org_academic_year_id = :orgAcademicYearId 
                            AND opsy.deleted_at IS NULL";
            $whereConditionCheck = ' AND opsy.id IS NULL ';
        } else {
            $selectParticipantStatus = ' IF(opsy.id, 1, 0) AS participant, ';
            $joinOrgPersonStudentYear = " 
                            LEFT  JOIN org_person_student_year opsy 
                            ON opsy.person_id = ops.person_id
                            AND opsy.org_academic_year_id = :orgAcademicYearId 
                            AND opsy.deleted_at IS NULL";
        }

        $deterministicSort = ' p.lastname ASC, p.firstname ASC, p.username ASC, p.id DESC  ';
        switch ($sortBy) {
            case 'external_id':
            case '+external_id':
                $orderBy = ' ORDER BY p.external_id ASC, ' . $deterministicSort;
                break;
            case '-external_id':
                $orderBy = ' ORDER BY p.external_id DESC, ' . $deterministicSort;
                break;
            case 'student_name':
            case '+student_name':
                $orderBy = ' ORDER BY ' . $deterministicSort;
                break;
            case '-student_name':
                $orderBy = ' ORDER BY  p.lastname DESC, p.firstname ASC, p.username ASC, p.id DESC ';
                break;
            case 'email':
            case '+email':
                $orderBy = ' ORDER BY p.username ASC, p.lastname ASC, p.firstname ASC, p.id DESC ';
                break;
            case '-email':
                $orderBy = ' ORDER BY p.username DESC, p.lastname ASC, p.firstname ASC, p.id DESC ';
                break;
            case 'participating':
            case '+participating':
                $orderBy = ' ORDER BY participant ASC, p.lastname ASC, p.firstname ASC, p.username ASC, p.id DESC ';
                break;
            case '-participating':
                $orderBy = ' ORDER BY participant DESC, p.lastname ASC, p.firstname ASC, p.username ASC, p.id DESC ';
                break;
            case 'active':
            case '+active':
                $orderBy = ' ORDER BY opsy.is_active ASC, ' . $deterministicSort;
                break;
            case '-active':
                $orderBy = ' ORDER BY opsy.is_active DESC, ' . $deterministicSort;
                break;
            default:
                $orderBy = ' ORDER BY ' . $deterministicSort;
        }

        $sql = "SELECT
                    ops.modified_at,
                    p.id,
                    p.welcome_email_sent_date,
                    p.title,
                    ci.primary_mobile,
                    ci.home_phone,
                    p.external_id,
                    p.firstname,
                    p.lastname,
                    p.username AS primary_email,
                    $selectParticipantStatus                    
                    IF(opsy.id AND opsy.deleted_at IS NULL ,is_active, 0) AS status
                FROM
                    org_person_student ops
                        INNER JOIN
                    person p ON ops.person_id = p.id
                        AND ops.organization_id = p.organization_id
                        LEFT JOIN
                    person_contact_info pci ON p.id = pci.person_id
                        LEFT JOIN
                    contact_info ci ON ci.id = pci.contact_id
                 
                        $joinOrgPersonStudentYear
                WHERE
                    ops.organization_id = :organizationId
                    $excludeClause
                    $searchClause         
                    $whereConditionCheck
                    AND ops.deleted_at IS NULL
                    AND p.deleted_at IS NULL
                    AND ci.deleted_at IS NULL                
                $orderBy
                LIMIT :limit OFFSET :offset";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet;
    }

    /**
     * Fetching list of all faculty of an organization for a given search text
     *
     * @param int $organizationId
     * @param string $searchText
     * @param array $personIdsToExclude
     * @param string $activeFilter
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getOrganizationFacultiesBySearchText($organizationId, $searchText, $personIdsToExclude, $activeFilter, $offset, $limit)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'offset' => $offset,
            'limit' => $limit
        ];

        $parameterTypes = [
            'offset' => \PDO::PARAM_INT,
            'limit' => \PDO::PARAM_INT
        ];

        if (!empty($personIdsToExclude)) {
            $parameters['personIdsToExclude'] = $personIdsToExclude;
            $parameterTypes['personIdsToExclude'] = Connection::PARAM_INT_ARRAY;

            $excludeClause = 'AND p.id NOT IN (:personIdsToExclude)';
        } else {
            $excludeClause = '';
        }

        if ($searchText) {
            $parameters['searchText'] = "%$searchText%";

            $searchClause = '
                AND (p.username LIKE :searchText
                OR p.firstname LIKE :searchText
                OR p.lastname LIKE :searchText
                OR p.external_id LIKE :searchText)';
        } else {
            $searchClause = '';
        }

        $activeFilterCondition = "" ;
        if($activeFilter == "active"){
            $activeFilterCondition = " AND (opf.status = 1 OR opf.status IS NULL) " ;
        }else if($activeFilter == "inactive"){
            $activeFilterCondition = " AND opf.status = 0 ";
        }

        $sql = "SELECT
                    opf.modified_at,
                    opf.status,
                    p.id,
                    p.welcome_email_sent_date,
                    p.title,
                    ci.primary_mobile,
                    ci.home_phone,
                    p.external_id,
                    p.firstname,
                    p.lastname,
                    p.username AS primary_email
                FROM
                    org_person_faculty opf
                        INNER JOIN
                    person p ON opf.person_id = p.id
                        AND opf.organization_id = p.organization_id
                        LEFT JOIN
                    person_contact_info pci ON p.id = pci.person_id
                        LEFT JOIN
                    contact_info ci ON ci.id = pci.contact_id
                WHERE
                    opf.organization_id = :organizationId
                        $excludeClause
                        $searchClause
                        $activeFilterCondition
                        AND opf.deleted_at IS NULL
                        AND p.deleted_at IS NULL
                        AND ci.deleted_at IS NULL
                ORDER BY p.lastname ASC , p.firstname ASC
                LIMIT :limit OFFSET :offset";

        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result;

    }

    /**
     * Get count of Faculty of an organization for a given search text
     *
     * @param int $organizationId
     * @param string $searchText
     * @param array $personIdsToExclude
     * @param string $activeFilter
     * @return int
     * @throws SynapseDatabaseException
     */
    public function getOrganizationFacultyCountBySearchText($organizationId, $searchText, $personIdsToExclude, $activeFilter)
    {
        $parameters = ['organizationId' => $organizationId];
        $parameterTypes = [];

        if (!empty($personIdsToExclude)) {
            $parameters['personIdsToExclude'] = $personIdsToExclude;
            $parameterTypes['personIdsToExclude'] = Connection::PARAM_INT_ARRAY;

            $excludeClause = 'AND p.id NOT IN (:personIdsToExclude)';
        } else {
            $excludeClause = '';
        }

        if ($searchText) {
            $searchClause = '
                AND (p.username LIKE :searchText
                OR p.firstname LIKE :searchText
                OR p.lastname LIKE :searchText
                OR p.external_id LIKE :searchText)';

            $parameters['searchText'] = "%$searchText%";
        } else {
            $searchClause = '';
        }

        $activeFilterCondition = "" ;
        if($activeFilter == "active"){
            $activeFilterCondition = " AND (opf.status = 1 OR opf.status IS NULL) " ;
        }else if($activeFilter == "inactive"){
            $activeFilterCondition = " AND opf.status = 0";
        }

        $sql = "SELECT
                    COUNT(DISTINCT opf.person_id) AS total_count
                FROM
                    org_person_faculty opf
                INNER JOIN
                    person p ON opf.person_id = p.id
                        AND opf.organization_id = p.organization_id
                WHERE
                    opf.organization_id = :organizationId
                    $excludeClause
                    $searchClause
                    $activeFilterCondition
                    AND opf.deleted_at IS NULL
                    AND p.deleted_at IS NULL
                    ";
        
        $result = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $result[0]['total_count'];
    }

    /**
     * Get count of students of an organization for a given search text
     *
     * @param int $organizationId
     * @param int $orgAcademicYearId
     * @param array $personIdsToExclude
     * @param null|string $searchText
     * @param null|string $participantFilter - (all, participants, non-participants)
     * @throws SynapseDatabaseException
     * @return int
     */
    public function getOrganizationStudentCountBySearchText($organizationId, $orgAcademicYearId, $personIdsToExclude, $searchText = null, $participantFilter = null)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];
        $parameterTypes = [];
        $excludeClause = '';
        if (!empty($personIdsToExclude) && $participantFilter == 'non-participants') {
            $parameters['personIdsToExclude'] = $personIdsToExclude;
            $parameterTypes['personIdsToExclude'] = Connection::PARAM_INT_ARRAY;
            $excludeClause = ' AND p.id NOT IN (:personIdsToExclude)';
        }

        $searchClause = '';
        if ($searchText) {
            $searchClause = '
                AND (p.username LIKE :searchText
                OR p.firstname LIKE :searchText
                OR p.lastname LIKE :searchText
                OR p.external_id LIKE :searchText)';
            $parameters['searchText'] = "%$searchText%";
        }

        $whereConditionCheck  = '';
        if ($participantFilter == 'participants') {
            $joinOrgPersonStudentYear = " 
                            INNER JOIN org_person_student_year opsy 
                            ON opsy.person_id = ops.person_id
                            AND opsy.org_academic_year_id = :orgAcademicYearId 
                            AND opsy.deleted_at IS NULL ";
        } else if ($participantFilter == 'non-participants') {
            $joinOrgPersonStudentYear = " 
                            LEFT  JOIN org_person_student_year opsy 
                            ON opsy.person_id = ops.person_id
                            AND opsy.org_academic_year_id = :orgAcademicYearId 
                            AND opsy.deleted_at IS NULL";
            $whereConditionCheck = ' AND opsy.id IS NULL ';
        } else {
            $joinOrgPersonStudentYear = "
                            LEFT  JOIN org_person_student_year opsy 
                            ON opsy.person_id = ops.person_id
                            AND opsy.org_academic_year_id = :orgAcademicYearId 
                            AND opsy.deleted_at IS NULL ";
        }

        $sql = "SELECT
                        COUNT(DISTINCT ops.person_id) AS total_count
                    FROM
                        org_person_student ops
                            INNER JOIN
                        person p ON ops.person_id = p.id
                            AND ops.organization_id = p.organization_id
                            $joinOrgPersonStudentYear
                    WHERE
                        ops.organization_id = :organizationId
                            $excludeClause
                            $searchClause
                            $whereConditionCheck                            
                            AND ops.deleted_at IS NULL
                            AND p.deleted_at IS NULL
                            ";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $resultSet[0]['total_count'];
    }

    public function getOrganizationUsersByTypeCount ($organization, $type)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(op.id) as totalCount');

        if (trim(strtolower($type)) == "faculty") {
            $qb->from(PersonConstant::ORG_PERSON_FACULTY, 'op');
        } else {
            $qb->from(PersonConstant::ORG_PERSON_STUDENT, 'op');
        }

        $qb->where('op.organization = :organization');
        $qb->setParameters(array(
            PersonConstant::FIELD_ORGANIZATION => $organization));

        $query = $qb->getQuery();

        return $query->getSingleScalarResult();
    }

    public function getUsersByUserIds($userIds)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->select('p.id as user_id', 'p.username as username', 'p.firstname as user_firstname', 'p.lastname as user_lastname', 'IDENTITY(p.organization) as organization_id',
            'p.externalId as student_id', 'p.welcomeEmailSentDate as email_sent_date', 'p.title as title', 'c.primaryEmail as user_email',
            'c.primaryMobile as primary_mobile', 'c.homePhone as home_phone', 'p.authUsername auth_username');
        $qb->from(PersonConstant::PERSON_REPO, 'p');
        $qb->leftJoin(PersonConstant::PERSON_CONTACTS, 'c');
        $qb->where('p.id IN (:userIds)');
        $qb->setParameters(array(
            'userIds' => $userIds
        ));
        $qb->orderBy(trim(PersonConstant::PERSON_LASTNAME), 'ASC');
        $qb->orderBy(trim(PersonConstant::PERSON_FIRSTNAME), 'ASC');
        $query = $qb->getQuery();
        return $query->getArrayResult();
    }


    /**
     * Note: This method does not work with Gray Risk.
     * Gets the count of high priority students a given faculty member has individual & risk-level access to in the given academic year.
     *
     * @param int $facultyId
     * @param int $orgId
     * @param int $orgAcademicYearId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getMyHighPriorityStudentsCount($facultyId, $orgId, $orgAcademicYearId)
    {
        $riskLevels = [1, 2];
        $parameters = [
            'facultyId' => $facultyId,
            'orgId' => $orgId,
            'riskLevels' => $riskLevels,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $parameterTypes = ['riskLevels' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT
                    COUNT(DISTINCT (ofspm.student_id)) AS count
                FROM
                    org_faculty_student_permission_map AS ofspm
                        INNER JOIN
                    org_permissionset op ON op.id = ofspm.permissionset_id
                        INNER JOIN
                    org_person_student_year opsy
                            ON opsy.person_id = ofspm.student_id
                            AND opsy.organization_id = ofspm.org_id
                        INNER JOIN
                    person AS p ON ofspm.student_id = p.id
                WHERE
                    (p.last_contact_date <= p.risk_update_date
                        OR p.last_contact_date IS NULL)
                    AND p.risk_level IN (:riskLevels)
                    AND op.risk_indicator = 1
                    AND op.accesslevel_ind_agg = 1
                    AND ofspm.faculty_id = :facultyId
                    AND ofspm.org_id = :orgId
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND opsy.is_active = 1
                    AND op.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL
                    AND p.deleted_at IS NULL;";

        try {
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            return $results[0]['count'];

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    /**
     * Returns count of students by risk level for a given academic year for a given faculty member.
     *
     * @param int $personId
     * @param int $organizationId
     * @param int $orgAcademicYearId
     * @return array
     */
    public function getStudentCountByRiskLevel($personId, $organizationId, $orgAcademicYearId)
    {
        $parameters = [
            'personId' => $personId,
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $sql = "SELECT
                    rl.id AS risk_level,
                    COUNT(DISTINCT pwrid.person_id) AS count,
                    rl.risk_text,
                    rl.image_name,
                    rl.color_hex
                FROM
                    org_faculty_student_permission_map ofspm
                        INNER JOIN
                    org_permissionset op ON ofspm.permissionset_id = op.id
                        INNER JOIN
                    org_person_student_year opsy ON opsy.person_id = ofspm.student_id
                        AND opsy.organization_id = ofspm.org_id
                        INNER JOIN
                    person_with_risk_intent_denullifier pwrid ON ofspm.student_id = pwrid.person_id
                        AND ofspm.org_id = pwrid.organization_id
                        INNER JOIN
                    risk_level rl ON rl.id = pwrid.risk_level
                WHERE
                    ofspm.org_id = :organizationId
                        AND ofspm.faculty_id = :personId
                        AND op.accesslevel_ind_agg = 1
                        AND op.risk_indicator = 1
                        AND opsy.org_academic_year_id = :orgAcademicYearId
                        AND opsy.is_active = 1
                        AND op.deleted_at IS NULL
                        AND opsy.deleted_at IS NULL
                        AND rl.deleted_at IS NULL
                GROUP BY rl.id";

        $resultSet = $this->executeQueryFetchAll($sql, $parameters);
        return $resultSet;
    }


    public function getUserIdFromRefreshToken($token)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('IDENTITY(r.user)');
        $qb->from('SynapseCoreBundle:RefreshToken', 'r');
        $qb->where('r.token =:token');
        $qb->setParameters(array(
            'token' => $token
        ));
        $query = $qb->getQuery();
        $result = $query->getArrayResult();
        return $result;
    }

    public function getConflictPersons($sourceOrganization, $destinationOrganization)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('person.id as source, person2.id as destination')
            ->from(PersonConstant::PERSON_REPO, PersonConstant::PERSON)
            ->INNERJoin(PersonConstant::PERSON_REPO, 'person2', \Doctrine\ORM\Query\Expr\Join::WITH, 'person.externalId = person2.externalId')
            ->where('person.organization =:sourceOrganization')
            ->andWhere('person2.organization IN (:destinationOrganization)')
            ->setParameters(array(
            'sourceOrganization' => $sourceOrganization,
            'destinationOrganization' => $destinationOrganization
        ));
        $resultSet = $queryBuilder->getQuery()->getResult();
        return $resultSet;
    }

    public function getConflictPersonsByRole($person)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('faculty.id as facultyId, person.id as personId, student.id as studentId , IDENTITY(faculty.organization) as facultyOrg, IDENTITY(student.organization) as studentOrg')
            ->from(PersonConstant::PERSON_REPO, PersonConstant::PERSON)
            ->LeftJoin(PersonConstant::ORG_PERSON_STUDENT, 'student', \Doctrine\ORM\Query\Expr\Join::WITH, 'student.person = person.id')
            ->LeftJoin(PersonConstant::ORG_PERSON_FACULTY, 'faculty', \Doctrine\ORM\Query\Expr\Join::WITH, 'faculty.person = person.id')
            ->where('person.id IN (:person)')
            ->setParameters(array(
            PersonConstant::PERSON => $person
        ));

        $resultSet = $queryBuilder->getQuery()->getResult();
        return $resultSet;
    }


    public function findExternalIdByCampusIds($externalId, $campusIds)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('p.id as personId, p.username as username, p.externalId as externalId')
            ->from(PersonConstant::PERSON_REPO, 'p')
            ->where('p.externalId =:externalId')
            ->andWhere('p.organization IN (:campusIds)')
            ->setParameters(array(
            PersonConstant::EXT_ID => $externalId,
            'campusIds' => $campusIds
        ));
        $query = $qb->getQuery();
        $result = $query->getResult();
        return $result;
    }

    public function validateEntityExtId($extId, $orgId)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('student.id as userId')
            ->from(PersonConstant::ORG_PERSON_STUDENT, 'student')
            ->LeftJoin(PersonConstant::PERSON_REPO, PersonConstant::PERSON, \Doctrine\ORM\Query\Expr\Join::WITH, 'student.person = person.id')
            ->where('person.externalId = :person AND person.organization = :organization')
            ->setParameters(array(
            PersonConstant::PERSON => $extId,
            PersonConstant::FIELD_ORGANIZATION => $orgId
        ));

        $resultSet = $queryBuilder->getQuery()->getArrayResult();
        return $resultSet;
    }

    public function getPredefinedProfile()
    {
        $preDefineProfiles = [
            'ExternalId',
            'Firstname',
            'Lastname',
            'Title',
            'Dateofbirth',
            'Address1',
            'Address2',
            'City',
            'Zip',
            'State',
            'Country',
            'PrimaryMobile',
            'AlternateMobile',
            'HomePhone',
            'OfficePhone',
            'PrimaryEmail',
            'AlternateEmail',
            'PrimaryMobileProvider',
            'AlternateMobileProvider',

            'StudentPhoto',
            'IsActive',
            'SurveyCohort',
            'ReceiveSurvey',
            'YearId',
            'TermId',
            'PrimaryConnect',

            'RiskGroupId'
        ];

        $fileds = array_map('strtolower', $preDefineProfiles);
        return $fileds;
    }


    public function getStudentRisk($person)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('Identity(r.riskGroup) as RiskGroupID')
            ->from('SynapseRiskBundle:RiskGroupPersonHistory', 'r')
            ->where('r.person = :person')
            ->orderBy('r.assignmentDate', 'DESC')
            ->setMaxResults(1)
            ->setParameters(array(
            PersonConstant::PERSON => $person
        ));

        $resultSet = $queryBuilder->getQuery()->getArrayResult();
        return $resultSet;
    }

	public function getPermissionsByUserIds($personIds, $organization)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()
            ->select('op.id as permission_id', 'op.permissionsetName as permission_name', 'identity(ogf.person) as group_person_id', 'identity(course.person) as course_person_id')
            ->from('SynapseCoreBundle:OrgPermissionset', 'op')
            ->LeftJoin('SynapseAcademicBundle:OrgCourseFaculty', 'course', \Doctrine\ORM\Query\Expr\Join::WITH, 'op.id = course.orgPermissionset')
            ->LeftJoin('SynapseCoreBundle:OrgGroupFaculty', 'ogf', \Doctrine\ORM\Query\Expr\Join::WITH, 'ogf.orgPermissionset = op.id')
            ->where('op.organization = :organization')
            ->andWhere('course.person IN (:persons) or ogf.person IN (:persons)')
            ->setParameters(array(
            'persons' => $personIds,
            'organization' => $organization
        ));

        return $queryBuilder->getQuery()->getArrayResult();
    }


	public function getDumpByOrganizationByPersonIds($organization, $personIds)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

		$qb->select('op, p,r,c,pc');
		$qb->from(PersonConstant::ORG_PERSON_STUDENT, 'op');
        $qb->leftJoin('op.person', 'p');
        $qb->leftJoin('p.riskLevel', 'r');
        $qb->leftJoin(PersonConstant::PERSON_CONTACTS, 'c');
        $qb->leftJoin(PersonConstant::PERSON_ORG, 'o');
        $qb->leftJoin('op.personIdPrimaryConnect', 'pc');

        $qb->where(PersonConstant::PERSON_EQUAL_ID, PersonConstant::ID_EQUAL_ORG);
        $qb->andWhere(PersonConstant::PERSON_ORG . '!= -1');
		$qb->andWhere('op.person IN (:person)');
        $qb->setParameters(array(
            PersonConstant::FIELD_ORGANIZATION => $organization,
			PersonConstant::PERSON => $personIds
        ));
        $qb->orderBy(trim(PersonConstant::PERSON_LASTNAME), 'ASC');
        $qb->orderBy(trim(PersonConstant::PERSON_FIRSTNAME), 'ASC');
        $query = $qb->getQuery();

        return $query->getArrayResult();
    }

    public function checkExistingEmail($email,$userId,$addFlag)
    {
    	$em = $this->getEntityManager();
    	$qb = $em->createQueryBuilder();
    	$qb->select('p.id');
    	$qb->from('SynapseCoreBundle:Person', 'p');
        $qb->innerJoin('SynapseCoreBundle:OrganizationRole', 'orgr', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = orgr.person');
        $qb->innerJoin('SynapseCoreBundle:Role', 'r', \Doctrine\ORM\Query\Expr\Join::WITH, 'orgr.role = r.id');
        $qb->innerJoin('SynapseCoreBundle:RoleLang', 'rl', \Doctrine\ORM\Query\Expr\Join::WITH, 'r.id = rl.role');
        $qb->where('rl.roleName IN(:roleName)');
        if(!$addFlag){
        	$qb->andWhere('p.username = :email AND p.id !=:userid');
        	$qb->setParameters(array(
        			"email" => $email,
        			"userid" => $userId,
        	        "roleName"=>['Skyfactor Admin','Mapworks Admin']
        	));
        }
        else{
            $qb->andWhere('p.username = :email');
            $qb->setParameters(array(
            		"email" => $email,
            		"roleName"=>['Skyfactor Admin','Mapworks Admin']
            ));
        }
    	$query = $qb->getQuery();
    	$result = $query->getArrayResult();
    	return $result;
    }

    /**
     * Returns the ids and first and last names and username of the people with ids in the given array.
     * If $orderBy is set to "name", orders them alphabetically by lastname, then by firstname.
     *
     * @param array $personIds
     * @param string|null $orderBy - currently only handles the string "name"
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getPersonNames($personIds, $orderBy = null)
    {
        $parameters = ['personIds' => $personIds];

        $parameterTypes = ['personIds' => Connection::PARAM_INT_ARRAY];

        if ($orderBy == 'name') {
            $orderSQLsubstring = "ORDER BY lastname, firstname";
        } else {
            $orderSQLsubstring = "";
        }

        $sql = "SELECT 
                    id AS person_id, firstname, lastname, username
                FROM 
                    person
                WHERE 
                    id IN (:personIds)
                    AND deleted_at IS NULL
                $orderSQLsubstring;";

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
     * Gets the valid external ID's for an organization
     *
     * @param int $organizationId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getExternalIdsByOrgId($organizationId)
    {
        $parameters = ['organizationId' => $organizationId];

        $sql = "SELECT external_id
                FROM person
                WHERE organization_id = :organizationId
                AND deleted_at IS NULL;";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        // PDO:FETCH_COLUMN in fetchAll tells doctrine to un-nest the column of results so it only returns an array of values
        // Like this: [1,2,3]
        // Instead of this: [[1],[2],[3]]
        $results = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        return $results;
    }


    /**
     * get risk level count for the student ids passed
     *
     * @param array $studentIds
     * @param int $facultyId
     * @return array
     */
    public function getAggregateRiskCountsWithPermissionCheck($studentIds, $facultyId)
    {
        $parameters = [
            'studentIds' => $studentIds,
            'facultyId' => $facultyId
        ];

        $parameterType = ['studentIds' => Connection::PARAM_INT_ARRAY];
        $sql = "SELECT 
                    risk_text, 
                    COUNT(DISTINCT pwrid.person_id) AS color_count
                FROM
                    person_with_risk_intent_denullifier pwrid
                        INNER JOIN
                    org_faculty_student_permission_map ofspm ON ofspm.faculty_id = :facultyId
                        AND ofspm.student_id = pwrid.person_id
                        AND ofspm.org_id = pwrid.organization_id
                        INNER JOIN
                    org_permissionset op ON op.id = ofspm.permissionset_id
                        AND op.risk_indicator = 1
                        INNER JOIN
                    risk_level rl ON rl.id = pwrid.risk_level
                WHERE
                    pwrid.person_id IN (:studentIds)
                GROUP BY pwrid.risk_level;";

        $results = $this->executeQueryFetchAll($sql, $parameters, $parameterType);

        return $results;
    }

    /**
     * Get All personIds Using User Names
     *
     * @param string $usernames
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getPersonIdsUsingUsernames($usernames)
    {
        $sql = "SELECT 
                    id
                FROM
                    person
                WHERE
                    username IN (:usernames)
                    AND deleted_at IS NULL";

        $parameters = [
            'usernames' => $usernames
        ];

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            $results = array_column($results, 'id');
            return $results;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    /**
     * Returns the users (faculty, coordinators, participating students in the current year) based on the search text.
     * If no academic year ID is passed in, only the faculty and coordinators are returned.
     *
     * @param int $organizationId
     * @param int|null $orgAcademicYearId
     * @param string|null $searchText
     * @param int|null $offset
     * @param int|null $recordsPerPage
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getUsersBySearchText($organizationId, $orgAcademicYearId = null, $searchText = null, $offset = null, $recordsPerPage = null)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $parameterTypes = [];

        if ($orgAcademicYearId) {
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
            $studentJoin = "
                    LEFT JOIN
                org_person_student ops
                        ON ops.person_id = p.id
                        AND ops.deleted_at IS NULL
                    LEFT JOIN
                org_academic_year oay
                        ON p.organization_id = oay.organization_id
                        AND oay.id = :orgAcademicYearId
                        AND oay.deleted_at IS NULL
                    LEFT JOIN
                org_person_student_year opsy
                        ON opsy.person_id = ops.person_id
                        AND opsy.org_academic_year_id = oay.id
                        AND opsy.deleted_at IS NULL

            ";
            $facultyJoin = " LEFT JOIN ";
            //This condition will exclude non-participants if the record belongs to a student. Otherwise, it includes everyone.
            $participantCondition = " AND IF(ops.id IS NOT NULL, opsy.id IS NOT NULL, 1) ";
        } else {
            $studentJoin = "";
            $facultyJoin = " JOIN ";
            $participantCondition = "";
        }

        if ($searchText) {
            $searchCondition = "
                AND (ps.firstname LIKE :searchText
                OR ps.lastname LIKE :searchText
                OR ps.username LIKE :searchText
                OR ps.first_and_last_name LIKE :searchText
                OR ps.last_and_first_name LIKE :searchText
                OR ps.external_id LIKE :searchText)
            ";
            $parameters['searchText'] = "%$searchText%";
            $parameterTypes['searchText'] = 'string';
        } else {
            $searchCondition = '';
        }

        if (is_numeric($offset) && is_numeric($recordsPerPage)) {
            $parameters['recordsPerPage'] = $recordsPerPage;
            $parameters['offset'] = $offset;
            $parameterTypes['recordsPerPage'] = 'integer';
            $parameterTypes['offset'] = 'integer';
            $limitCondition = 'LIMIT :recordsPerPage OFFSET :offset';
        } else {
            $limitCondition = '';
        }

        $sql = "
            SELECT
                p.id AS user_id,
                p.organization_id AS campus_id,
                p.external_id,
                p.firstname AS first_name,
                p.lastname AS last_name,
                p.title,
                p.username AS email,
                CASE WHEN orl.id IS NOT NULL THEN 'coordinator'
                    WHEN opf.id IS NOT NULL THEN 'Staff/Faculty'
                    ELSE 'student' END AS user_type,
                rl.role_name AS role
            FROM
                person p
                    JOIN
                person_search ps
                        ON ps.person_id = p.id
                    $studentJoin
                    $facultyJoin
                org_person_faculty opf
                        ON opf.person_id = p.id
                        AND opf.deleted_at IS NULL
                    LEFT JOIN
                organization_role orl
                        ON orl.person_id = p.id
                        AND orl.deleted_at IS NULL
                    LEFT JOIN
                role_lang rl
                        ON rl.role_id = orl.role_id
                        AND rl.deleted_at IS NULL
            WHERE
                p.deleted_at IS NULL
                AND p.organization_id = :organizationId
                $searchCondition
                $participantCondition
            ORDER BY p.lastname ASC, p.firstname ASC, p.username ASC, p.id ASC
            $limitCondition;
        ";


        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);

        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

        $records = $stmt->fetchAll();

        return $records;
    }


    /**
     * Gets student list with current risk and intent to leave information based, optionally, on search text, risk group, and cohort
     * Optional Pagination
     *
     * @param int $organizationId
     * @param int $currentOrgAcademicYearId
     * @param string|null $searchText
     * @param int|null $cohort
     * @param int|null $riskGroupId
     * @param int|null $recordsPerPage
     * @param int|null $offset
     * @return array
     */
    public function getPersonsCurrentRiskAndIntentToLeaveFilteredByStudentCriteria($organizationId, $currentOrgAcademicYearId, $searchText = null, $cohort = null, $riskGroupId = null, $recordsPerPage = null, $offset = null)
    {
        $parameters = [
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $currentOrgAcademicYearId
        ];
        $parameterTypes = [];
        $cohortFilter = '';
        if (is_numeric($currentOrgAcademicYearId) && is_numeric($cohort)) {
            $cohortFilter = " AND opsc.cohort = :currentCohort";
            $parameters['currentCohort'] = $cohort;
        }

        $riskGroupFilter = '';
        if (is_numeric($riskGroupId)) {
            $riskGroupFilter = ' AND rgph.risk_group_id = :riskGroupId';
            $parameters['riskGroupId'] = $riskGroupId;
        }

        $searchFilter = '';
        $personSearchTableJoin = '';
        if ($searchText != null) {
            $searchFilter = " AND (pwrid.firstname LIKE :searchText
            OR pwrid.lastname LIKE :searchText
            OR pwrid.username LIKE :searchText
            OR pwrid.external_id LIKE :searchText
            OR first_and_last_name LIKE :searchText
            OR last_and_first_name LIKE :searchText)";
            $parameters['searchText'] = "%$searchText%";
            $parameterTypes['searchText'] = 'string';

            $personSearchTableJoin = ' INNER JOIN person_search ps ON ps.person_id = pwrid.person_id ';
        }

        $limitCondition = '';
        if (is_numeric($recordsPerPage)) {
            $limitCondition = ' LIMIT :recordsPerPage OFFSET :offset';
            $parameters['recordsPerPage'] = $recordsPerPage;
            $parameters['offset'] = $offset;

            $parameterTypes['recordsPerPage'] = \PDO::PARAM_INT;
            $parameterTypes['offset'] = \PDO::PARAM_INT;
        }

        $sql = "SELECT 
                    pwrid.external_id,
                    pwrid.person_id AS mapworks_internal_id,
                    pwrid.firstname,
                    pwrid.lastname,
                    pwrid.organization_id,
                    pwrid.username AS primary_email,
                    rgph.risk_group_id,
                    rgl.name as risk_group_name,
                    pwrid.risk_level,
                    pwrid.risk_updated_date,
                    rl.risk_text AS risk_color_text,
                    rl.color_hex AS risk_color_hex,
                    opsc.cohort AS current_cohort,
                    pwrid.intent_to_leave,
                    pwrid.intent_to_leave_updated_date,
                    itl.text AS intent_to_leave_color_text,
                    itl.color_hex as intent_to_leave_color_hex
                FROM
                    person_with_risk_intent_denullifier pwrid
                    $personSearchTableJoin
                        INNER JOIN
                    org_person_student ops ON ops.person_id = pwrid.person_id
                        AND ops.deleted_at IS NULL
                        LEFT JOIN
                    risk_level rl ON rl.id = pwrid.risk_level
                        LEFT JOIN
                    intent_to_leave itl ON itl.id = pwrid.intent_to_leave
                        LEFT JOIN
                    risk_group_person_history rgph ON rgph.person_id = pwrid.person_id
                        LEFT JOIN
                    risk_group_lang rgl ON rgl.risk_group_id = rgph.risk_group_id
                        LEFT JOIN
                    org_person_student_cohort opsc ON opsc.person_id = pwrid.person_id
                        AND opsc.org_academic_year_id = :orgAcademicYearId
                        AND opsc.deleted_at IS NULL
                WHERE
                    pwrid.organization_id = :organizationId
                    $cohortFilter
                    $riskGroupFilter
                    $searchFilter
                ORDER BY pwrid.lastname, pwrid.firstname, pwrid.person_id
                $limitCondition";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $records;
    }

    /**
     * Gets the student users' person IDs within Mapworks for an organization
     * Optional Search Filtering by cohort or risk group
     *
     * @param int $organizationId
     * @param int|null $searchText
     * @param int|null $riskGroupId
     * @param int|null $cohort
     * @param int|null $orgAcademicYearId
     * @return array
     */
    public function getMapworksStudents($organizationId, $searchText = null, $riskGroupId = null, $cohort = null, $orgAcademicYearId = null)
    {
        $parameters['organizationId'] = $organizationId;
        $parameterTypes = [];

        $academicYearCohortFilter = '';
        $academicYearCohortFilterTableJoin = '';
        if (is_numeric($orgAcademicYearId) && is_numeric($cohort)) {
            $academicYearCohortFilterTableJoin = ' LEFT JOIN org_person_student_cohort opsc ON opsc.person_id = ps.person_id AND opsc.deleted_at IS NULL';
            $academicYearCohortFilter = " AND opsc.org_academic_year_id = :orgAcademicYearId
            AND opsc.cohort = :currentCohort";
            $parameters['orgAcademicYearId'] = $orgAcademicYearId;
            $parameters['currentCohort'] = $cohort;
        }

        $riskGroupFilter = '';
        $riskGroupFilterTableJoin = '';
        if (is_numeric($riskGroupId)) {
            $riskGroupFilterTableJoin = ' LEFT JOIN risk_group_person_history rgph ON rgph.person_id = ps.person_id';
            $riskGroupFilter = ' AND rgph.risk_group_id = :riskGroupId';
            $parameters['riskGroupId'] = $riskGroupId;
        }

        if ($searchText != null) {

            $filterSearchText =  "  AND (ps.firstname LIKE :searchText
            OR ps.lastname LIKE :searchText
            OR ps.username LIKE :searchText
            OR ps.external_id LIKE :searchText
            OR ps.first_and_last_name LIKE :searchText
            OR ps.last_and_first_name LIKE :searchText)";

            $parameters['searchText'] = "%$searchText%";
            $parameterTypes['searchText'] = 'string';
        } else {
            $filterSearchText = '';
        }

        $sql = "SELECT
                    ps.person_id
                FROM
                    person_search ps
                        JOIN
                    org_person_student ops ON ops.person_id = ps.person_id
                    $riskGroupFilterTableJoin
                    $academicYearCohortFilterTableJoin
                WHERE
                    ops.deleted_at IS NULL
                    AND ops.organization_id = :organizationId
                    $academicYearCohortFilter
                    $riskGroupFilter
                    $filterSearchText";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        $studentIds = [];
        if (!empty($records)) {
            $studentIds = array_column($records, 'person_id');
        }
        return $studentIds;
    }


    /**
     * Gets the faculty users person IDs within Mapworks for an organization.
     *
     * @param int $organizationId
     * @param int|null $searchText
     * @return array
     */
    public function getMapworksFaculty($organizationId, $searchText = null)
    {
        $parameters['organizationId'] = $organizationId;
        $parameterTypes = [];

        if (!is_null($searchText)) {

            $filterSearchText =  "  AND (ps.firstname LIKE :searchText
            OR ps.lastname LIKE :searchText
            OR ps.username LIKE :searchText
            OR ps.external_id LIKE :searchText
            OR ps.first_and_last_name LIKE :searchText
            OR ps.last_and_first_name LIKE :searchText)";

            $parameters['searchText'] = "%$searchText%";
            $parameterTypes['searchText'] = 'string';
        } else {
            $filterSearchText = '';
        }


        $sql = "SELECT
                    ps.person_id
                FROM
                    person_search ps
                        JOIN
                    org_person_faculty opf ON opf.person_id = ps.person_id
                WHERE
                    opf.deleted_at IS NULL
                    AND opf.organization_id = :organizationId
                    $filterSearchText";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        $facultyIds = [];
        if (!empty($records)) {
            $facultyIds = array_column($records, 'person_id');
        }
        return $facultyIds;
    }

    /**
     * Gets the orphan users' (users that are neither a faculty nor a student) person IDs within Mapworks for an organization.
     *
     * @param int $organizationId
     * @param int|null $searchText
     * @return array
     */
    public function getMapworksOrphanUsers($organizationId, $searchText = null)
    {

        $parameters['organizationId'] = $organizationId;
        $parameterTypes = [];

        if (!is_null($searchText)) {

            $filterSearchText =  "  AND (ps.firstname LIKE :searchText
            OR ps.lastname LIKE :searchText
            OR ps.username LIKE :searchText
            OR ps.external_id LIKE :searchText
            OR ps.first_and_last_name LIKE :searchText
            OR ps.last_and_first_name LIKE :searchText)";

            $parameters['searchText'] = "%$searchText%";
            $parameterTypes['searchText'] = 'string';
        } else {
            $filterSearchText = '';
        }

        $sql = "SELECT
                    ps.person_id
                FROM
                    person_search ps
                        JOIN
                    person p ON p.id = ps.person_id
                        LEFT JOIN
                    org_person_student ops ON ops.person_id = ps.person_id
                            AND ops.organization_id = :organizationId
                            AND ops.deleted_at IS NULL
                        LEFT JOIN
                    org_person_faculty opf ON opf.person_id = ps.person_id
                            AND opf.organization_id = :organizationId
                            AND opf.deleted_at IS NULL
                WHERE
                    p.organization_id = :organizationId
                    AND p.deleted_at IS NULL
                    AND opf.id IS NULL
                    AND ops.id IS NULL
                    AND p.external_id IS NOT NULL 
                    $filterSearchText";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        $orphanUserIds = [];
        if (!empty($records)) {
            $orphanUserIds = array_column($records, 'person_id');
        }
        return $orphanUserIds;

    }

    /**
     * Gets the users' person IDs within Mapworks for an organization.
     *
     * @param integer $organizationId
     * @param integer|null $searchText
     * @return array
     */
    public function getMapworksPersons($organizationId, $searchText = null)
    {
        $parameters['organizationId'] = $organizationId;
        $parameterTypes = [];

        if (!is_null($searchText)) {

            $filterSearchText =  " AND (firstname LIKE :searchText
            OR lastname LIKE :searchText
            OR username LIKE :searchText
            OR external_id LIKE :searchText
            OR first_and_last_name LIKE :searchText
            OR last_and_first_name LIKE :searchText)";

            $parameters['searchText'] = "%$searchText%";
            $parameterTypes['searchText'] = 'string';
        } else {
            $filterSearchText = '';
        }

        $sql = "SELECT
                    person_id
                FROM
                    person_search
                WHERE
                    organization_id = :organizationId
                    AND external_id IS NOT NULL 
                    $filterSearchText";

        $records = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        $personIds = [];
        if (!empty($records)) {
            $personIds = array_column($records, 'person_id');
        }
        return $personIds;
    }

    /**
     * Gets user data for the passed in list of person IDs.
     *
     * @param int $organizationId
     * @param array $personIds
     * @param int|null $offset
     * @param int|null $recordsPerPage
     * @return array
     */
    public function getMapworksPersonData($organizationId, $personIds, $offset = null, $recordsPerPage = null)
    {

        $parameters = [
            'organizationId' => $organizationId,
            'personIds' => $personIds,
            'offset' => $offset,
            'recordsPerPage' => $recordsPerPage
        ];

        $parameterTypes = [
            'personIds' => Connection::PARAM_STR_ARRAY,
            'offset' => \PDO::PARAM_INT,
            'recordsPerPage' => \PDO::PARAM_INT
        ];

        $sql = "SELECT
                    p.external_id,
                    p.id AS mapworks_internal_id,
                    p.auth_username ,
                    p.firstname,
                    p.lastname,
                    p.username AS primary_email,
                    ops.photo_url,
                    CASE WHEN ops.id IS NOT NULL THEN 1 ELSE 0 END AS is_student,
                    CASE WHEN opf.id IS NOT NULL THEN 1 ELSE 0 END AS is_faculty,
                    faculty.external_id AS primary_connection_person_id,
                    rgl.risk_group_id,
                    rgl.description AS risk_group_description
                FROM
                    person p
                        LEFT JOIN
                    org_person_student ops ON ops.person_id = p.id
                            AND ops.organization_id = :organizationId
                            AND ops.deleted_at IS NULL
                        LEFT JOIN
                    org_person_faculty opf ON opf.person_id = p.id
                            AND opf.organization_id = :organizationId
                            AND opf.deleted_at IS NULL
                        LEFT JOIN 
                    org_person_faculty faculty_primary_connect 
                            ON ops.person_id_primary_connect = faculty_primary_connect.person_id 
                            AND  faculty_primary_connect.deleted_at IS NULL
                        LEFT JOIN
                    person faculty ON faculty_primary_connect.person_id = faculty.id
                          AND faculty.organization_id = :organizationId
                          AND faculty.deleted_at IS NULL
                        LEFT JOIN
                    risk_group_person_history rgph ON p.id = rgph.person_id
                        LEFT JOIN
                    risk_group_lang rgl ON rgph.risk_group_id = rgl.risk_group_id
                WHERE
                    p.deleted_at IS NULL
                    AND p.organization_id = :organizationId
                    AND p.id IN (:personIds)
                ORDER BY p.lastname ASC, p.firstname ASC, p.username ASC, p.id DESC
                LIMIT :offset, :recordsPerPage ";

        $personDataArray = $this->executeQueryFetchAll($sql, $parameters, $parameterTypes);
        return $personDataArray;

    }

}