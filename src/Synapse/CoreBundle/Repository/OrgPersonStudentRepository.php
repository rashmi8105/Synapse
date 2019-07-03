<?php
namespace Synapse\CoreBundle\Repository;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Entity\OrgPersonStudent;
use Synapse\CoreBundle\Exception\SynapseDatabaseException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\CoreBundle\Util\Constants\SearchConstant;
use Synapse\RestBundle\Exception\ValidationException;

class OrgPersonStudentRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseCoreBundle:OrgPersonStudent';

    /**
     * @param mixed $id
     * @param null $lockMode
     * @param null $lockVersion
     * @return OrgPersonStudent|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion);
    }


    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array $criteria
     * @param SynapseException $exception
     * @param array|null $orderBy
     * @param \Exception $exception
     *
     * @return OrgPersonStudent|null
     */
    public function findOneBy(array $criteria, $exception = null, array $orderBy = null)
    {
        $orgPersonStudentEntity = parent::findOneBy($criteria, $orderBy);
        return $this->doesObjectExist($orgPersonStudentEntity, $exception);
    }


//Optimized query
    public function getStudentsByOrganizationCourseOptimized($orgId, $currentDate)
    {
        $em = $this->getEntityManager();
        $sql = "SELECT DISTINCT ocs.org_courses_id  as courseId, ocs.organization_id as org, ocs.person_id as student_id
FROM org_course_student ocs
JOIN org_academic_terms oat on oat.organization_id=ocs.organization_id
JOIN org_academic_year oay on oay.organization_id=ocs.organization_id
LEFT JOIN org_courses oc on ocs.org_courses_id=oc.id
WHERE ocs.organization_id=$orgId
AND CURDATE() BETWEEN oat.start_date AND oat.end_date
AND CURDATE() BETWEEN oay.start_date AND oay.end_date
";

        $resultSet = $em->getConnection()->fetchAll($sql);

        return $resultSet;
    }

    public function remove(OrgPersonStudent $student)
    {
        $em = $this->getEntityManager();
        $em->remove($student);
    }

    public function updatePrimaryConnection($orgId, $studentId, $facultyId)
    {
        try {
            $em = $this->getEntityManager();

            $qb = $em->createQuery("update SynapseCoreBundle:OrgPersonStudent ops
            set ops.personIdPrimaryConnect=" . $facultyId . " where ops.organization=".$orgId." and ops.person=" . $studentId . "
            and ops.deletedAt is null");
            $qb->execute();
        } catch (\Exception $e) {
            throw new ValidationException([
                SearchConstant::QUERY_ERROR
            ], $e->getMessage(), SearchConstant::QUERY_ERROR);
        }
        return true;
    }

    /**
     * Generic function is used to get count of any table
     *
     * @param tablename $tname
     * @param Organization_id $orgId
     * @return \Doctrine\ORM\mixed int
     */
    public function getStudentCount($orgId)
    {
        $em = $this->getEntityManager();
        $count = $em->createQueryBuilder()
            ->select('count(t.id)')
            ->from(PersonConstant::ORG_PERSON_STUDENT, 't')
            ->where('t.organization =:org')
            ->setParameters(array(
                'org' => $orgId
            ))
            ->getQuery()
            ->getSingleScalarResult();

        return $count;
    }


    public function getPersonStudentByExternalId($externalId, $org)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p');
        $qb->from('SynapseCoreBundle:Person', 'p');

        $qb->Join(PersonConstant::ORG_PERSON_STUDENT, 'opf', \Doctrine\ORM\Query\Expr\Join::WITH, 'opf.person = p.id');
        // $qb->where('opf.organization = :orgId AND (p.isHomeCampus = 1 OR p.isHomeCampus IS NULL)');
        $qb->where('opf.organization = :orgId AND (opf.isHomeCampus = 1 OR opf.isHomeCampus IS NULL)');
        $qb->andWhere('p.organization = :orgId');
        $qb->andWhere('p.externalId = :externalId');
        $qb->setParameters(array(
            PersonConstant::ORG_ID => $org,
            'externalId' => $externalId
        ));
        $query = $qb->getQuery();
        $resultSet = $query->getOneOrNullResult();
        return $resultSet;
    }

    /**
     * Get students campus detail
     *
     * @param int $studentId
     * @return array
     */
    public function getCampusDetails($studentId)
    {
        $sql = "SELECT 
					ops.person_id,
					ops.organization_id,
					org.campus_id,
					lang.organization_name
				FROM
					org_person_student ops
						INNER JOIN
					organization org ON (org.id = ops.organization_id
						AND ops.person_id = :studentId)
						INNER JOIN
					organization_lang lang ON (org.id = lang.organization_id)
				WHERE
					ops.deleted_at IS NULL
						AND org.deleted_at IS NULL
						AND lang.deleted_at IS NULL";
        $parameters = ['studentId' => $studentId];
        try {
            $em = $this->getEntityManager();
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->execute($parameters);
            $results = $stmt->fetchAll();
        } catch (\Exception $e) {
            throw new SynapseDatabaseException($e->getMessage() . ": " . $e->getTraceAsString());
        }
        return $results;
    }
}


