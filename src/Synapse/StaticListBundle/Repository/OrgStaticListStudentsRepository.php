<?php
namespace Synapse\StaticListBundle\Repository;

use Doctrine\DBAL\Connection;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\CoreBundle\Util\Helper;
use Synapse\StaticListBundle\Entity\OrgStaticListStudents;
use Synapse\StaticListBundle\Util\Constants\StaticListConstant;


class OrgStaticListStudentsRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseStaticListBundle:OrgStaticListStudents';

    /**
     * Override function for PHP Typing
     *
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return null|OrgStaticListStudents
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion); 
    }

    /**
     * Override function for PHP Typing
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return OrgStaticListStudents[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset); 
    }

    /**
     * Override function for PHP Typing
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @return null|OrgStaticListStudents
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy); 
    }


    public function addStudentToStaticList(OrgStaticListStudents $OrgStaticListStudents)
    {
        $OrgStaticListStudents = $this->persist($OrgStaticListStudents);
        $orgStaticListId = $OrgStaticListStudents->getId();
        return $orgStaticListId;
    }

    /**
     * Get the list of participant students for the static List id for a faculty
     *
     * @param integer $orgStaticListId
     * @param integer $faultyId
     * @param integer $orgAcademicYearId
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStaticListStudents($orgStaticListId, $faultyId, $orgAcademicYearId)
    {
        $parameters = [
            'orgStaticListId' => $orgStaticListId,
            'facultyId' => $faultyId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $sql = "SELECT 
                    DISTINCT ofspm.student_id
                FROM 
                    org_static_list_students osls
                        INNER JOIN
                    org_person_student_year opsy
                            ON osls.organization_id = opsy.organization_id
                            AND osls.person_id = opsy.person_id
                        INNER JOIN
                    org_faculty_student_permission_map ofspm
                            ON osls.organization_id = opsy.organization_id
                            AND osls.person_id = ofspm.student_id 
                WHERE 
                    osls.deleted_at IS NULL
                    AND opsy.deleted_at IS NULL
                    AND ofspm.faculty_id = :facultyId
                    AND opsy.org_academic_year_id = :orgAcademicYearId
                    AND osls.org_static_list_id = :orgStaticListId
    ";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
            $results = $stmt->fetchAll();
            return $results;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
    }

    public function checkPermissionSet($loggedUserId = null, $studentId = null)
    {
        if (! is_null($loggedUserId)) {
            $em = $this->getEntityManager();
            $whereUserIdClause = " AND p.id = " . $studentId;
            $base_query = Helper::BASEQUERY_SELECT . Helper::BASEQUERY_CONST1 . $loggedUserId . Helper::BASEQUERY_CONST2 . Helper::BASEQUERY_CONST3 . $whereUserIdClause;
            $resultSet = $em->getConnection()->fetchAll($base_query);
            return $resultSet;
        }
        
        return false;
    }

    /**
     * Get the list of participant students for the static List id for current year
     *
     * @param int $organizationId
     * @param int $staticListId
     * @param int $currentAcademicYear
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStaticListStudentsByOrg($organizationId, $staticListId, $currentAcademicYear)
    {

        $parameters = [
            'organizationId' => $organizationId,
            'staticListId' => $staticListId,
            'currentAcademicYear' => $currentAcademicYear
        ];
        $sql = "SELECT
                    osl.name,
                    p.external_id,
                    p.firstname,
                    p.lastname,
                    c.primary_email
                FROM
                    org_static_list_students osls
                        INNER JOIN
                    org_person_student_year opsy ON osls.organization_id = opsy.organization_id AND osls.person_id = opsy.person_id
                        INNER JOIN
                    person p ON osls.person_id = p.id AND p.organization_id = opsy.organization_id
                        INNER JOIN
                    org_static_list osl ON osls.org_static_list_id = osl.id
                        LEFT JOIN
                    person_contact_info pci ON pci.person_id = p.id AND pci.deleted_at IS NULL
                        LEFT JOIN
                    contact_info c ON c.id = pci.contact_id AND c.deleted_at IS NULL
                WHERE
                    osls.org_static_list_id = :staticListId
                        AND osls.organization_id = :organizationId
                        AND opsy.org_academic_year_id = :currentAcademicYear
                        AND osls.deleted_at IS NULL
                        AND opsy.deleted_at IS NULL
                        AND p.deleted_at IS NULL
                ORDER BY p.lastname, p.firstname";
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        try {
            $stmt = $connection->executeQuery($sql, $parameters);
            return $stmt->fetchAll();
        } catch (\Exception $exception) {
            throw new SynapseDatabaseException($exception->getTraceAsString());
        }
    }

    public function fetchAll($orgStaticList, $organization)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('IDENTITY(osls.person) as studentId, IDENTITY(osls.orgStaticList) as orgStaticList')
            ->from(StaticListConstant::STATICLIST_STUDENTS_REPO, 'osls')
            ->where('osls.organization = :orgId')
            ->andWhere('osls.orgStaticList = :orgStaticList')
            ->setParameters(array(
            'orgId' => $organization,
            'orgStaticList' => $orgStaticList
        ))
            ->getQuery();
        
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function findClassLeveByStudentID($studentId)
    {
        $key = 'ClassLevel';
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('em.id as ebi_metadata_id','pem.metadataValue as list_value')
            ->from('SynapseCoreBundle:EbiMetadata', 'em')
            ->LEFTJoin('SynapseCoreBundle:PersonEbiMetadata', 'pem', \Doctrine\ORM\Query\Expr\Join::WITH, 'pem.ebiMetadata = em.id')
            ->where('pem.person = :studentId AND em.key = :key')
            ->setParameters(array(
            'studentId' => $studentId,
            'key' => $key
        ))
            ->getQuery();
        
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    /**
     * Get count of static list students based on $staticListId and $currentAcademicYear
     *
     * @param int $staticListId
     * @param int $currentAcademicYear
     * @return array
     * @throws SynapseDatabaseException
     */
    public function getStaticListStudentsCount($staticListId, $currentAcademicYear)
    {
        $parameters = [
            'staticListId' => $staticListId,
            'currentAcademicYear' => $currentAcademicYear
        ];

        $sql = "SELECT
                    COUNT(osls.person_id) as totalStudents
                FROM
                    org_static_list_students osls
                        LEFT JOIN
                    org_static_list osl ON osls.org_static_list_id = osl.id AND osl.deleted_at IS NULL
                        INNER JOIN
                        org_person_student_year opsy ON osls.person_id = opsy.person_id AND osls.organization_id = opsy.organization_id
                WHERE
                    osls.org_static_list_id = :staticListId
                        AND opsy.org_academic_year_id = :currentAcademicYear
                        AND osls.deleted_at IS NULL
                        AND opsy.deleted_at IS NULL";
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters);
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        $results = $stmt->fetchAll();
        return $results;
    }
    
    public function findClassLevelForStudent($ebiMetadataId,$listValue){
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
        ->select('emv.listName as class_level')
        ->from('SynapseCoreBundle:EbiMetadataListValues', 'emv')
        ->where('emv.ebiMetadata = :ebiMetadataId AND emv.listValue = :listValue')
        ->setParameters(array(
        'ebiMetadataId' => $ebiMetadataId,
        'listValue' => $listValue
        ))
        ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    /**
     * Get students by static list
     *
     * @param array $staticList
     * @param int $organizationId
     * @param int $orgAcademicYearId
     * @return array
     */
    public function getStudentsByList($staticList, $organizationId, $orgAcademicYearId)
    {
        $parameters = [
            'staticlist' => $staticList,
            'organizationId' => $organizationId,
            'orgAcademicYearId' => $orgAcademicYearId
        ];

        $parameterTypes = ['staticlist' => Connection::PARAM_INT_ARRAY];

        $sql = "SELECT 
                    DISTINCT osls.person_id AS person_id
                FROM
                    org_static_list_students osls
	            INNER JOIN
	                org_person_student_year opsy ON osls.person_id = opsy.person_id
	                    AND osls.organization_id = opsy.organization_id
                WHERE
                    (osls.organization_id = :organizationId
		                AND opsy.org_academic_year_id = :orgAcademicYearId
                        AND osls.org_static_list_id IN (:staticlist))
                        AND (osls.deleted_at IS NULL)";

        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->executeQuery($sql, $parameters, $parameterTypes);
            $results = $stmt->fetchAll();
            return $results;
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }

    }
}
