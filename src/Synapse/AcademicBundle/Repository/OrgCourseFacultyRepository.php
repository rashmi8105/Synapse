<?php
namespace Synapse\AcademicBundle\Repository;

use Symfony\Component\Validator\Constraints\DateTime;
use Synapse\CoreBundle\Entity\OrgPermissionset;
use Synapse\CoreBundle\Repository\SynapseRepository;
use Synapse\AcademicBundle\Entity\OrgCourseFaculty;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\CourseConstant;

class OrgCourseFacultyRepository extends SynapseRepository
{

    const REPOSITORY_KEY = 'SynapseAcademicBundle:OrgCourseFaculty';

    public function remove(OrgCourseFaculty $OrgCourseFaculty)
    {
        $this->delete($OrgCourseFaculty);
    }

    public function getCourseFacultyForOrganization($orgId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('cf, oc.externalId as UniqueCourseSectionID,  p.permissionsetName as PermissionSet')
            ->from(CourseConstant::ORG_FACULTY_REPO, 'cf')
            ->LEFTJoin('SynapseAcademicBundle:OrgCourses', 'oc', \Doctrine\ORM\Query\Expr\Join::WITH, CourseConstant::CF_COURSE_OCID)
            ->LEFTjoin(CourseConstant::ORGPERMISSIONSET_ENTITY, 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = cf.orgPermissionset')
            ->where('cf.organization = :orgId')
            ->setParameters(array(
            'orgId' => $orgId
        ))
            ->orderBy('cf.id')
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    public function createCourseFaculty(OrgCourseFaculty $OrgCourseFaculty)
    {
        $em = $this->getEntityManager();
        $em->persist($OrgCourseFaculty);
        return $OrgCourseFaculty;
    }

    public function getFacultyPermission($orgId, $facultyId)
    {
        $em = $this->getEntityManager();
        $facultyPermissions = $em->createQueryBuilder()
            ->select('IDENTITY(ocf.orgPermissionset) as permissionSetId, p.permissionsetName')
            ->from(CourseConstant::ORG_FACULTY_REPO, 'ocf')
            ->join(CourseConstant::ORGPERMISSIONSET_ENTITY, 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = ocf.orgPermissionset')
            ->where('ocf.organization = :organization')
            ->andWhere('ocf.person = :faculty')
            ->andWhere('ocf.deletedAt IS NULL')
            ->setParameters(array(
            'organization' => $orgId,
            'faculty' => $facultyId
        ))
            ->distinct()
            ->getQuery()
            ->getResult();
        return $facultyPermissions;
    }

    public function getFacultiesForCourse($courseId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
            ->select('IDENTITY(cf.person) as facultyId', 'oc.id as courseId')
            ->from(CourseConstant::ORG_FACULTY_REPO, 'cf')
            ->Join(CourseConstant::ORGPERMISSIONSET_ENTITY, 'op', \Doctrine\ORM\Query\Expr\Join::WITH, 'op.id = cf.orgPermissionset')
            ->Join('SynapseAcademicBundle:orgCourses', 'oc', \Doctrine\ORM\Query\Expr\Join::WITH, CourseConstant::CF_COURSE_OCID)
            ->
        where('oc.id = :courseid  AND op.createViewAcademicUpdate = 1')
            ->setParameters(array(
            'courseid' => $courseId
        ))
            ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    /**
     * Get the course list for a faculty
     *
     * @param string $staff
     * @param \DateTime $currentDate
     * @return array
     */
    public function getCoursesForStaff($staff, $currentDate)
    {
        $startDate = '';
        $endDate = '';
        if (isset($currentDate)) {
            $cloneCurrentDate = clone $currentDate;
            $endDate = $cloneCurrentDate->setTime(23, 59, 59)->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
            $startDate = $currentDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT);
        }
        $parameters = [
            'facultyId' => $staff,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
        $sql = 'SELECT
                    ocf.person_id AS facultyId,
                    oc.id AS courseId
                FROM
                    org_course_faculty ocf
                        INNER JOIN
                    org_courses oc ON ocf.org_courses_id = oc.id
                        INNER JOIN
                    org_academic_year oay ON oay.id = oc.org_academic_year_id
                        INNER JOIN
                    org_academic_terms oat ON oat.id = oc.org_academic_terms_id
                WHERE
                    ocf.person_id = :facultyId
                        AND oay.start_date <= :startDate
                        AND oay.end_date >= :endDate
                        AND oat.start_date <= :startDate
                        AND oat.end_date >= :endDate
                        AND ocf.deleted_at IS NULL
                        AND oay.deleted_at IS NULL
                        AND oat.deleted_at IS NULL';
        $resultSet = $this->executeQueryFetchAll($sql, $parameters);
        return $resultSet;
    }

    /**
     * Adding faculty to a course based on $personId, $organizationId and $courseId with $permissionsetId optional
     * 
     * @param int $personId
     * @param int $organizationId
     * @param int $courseId
     * @param int|null $permissionsetId
     * @throws \Doctrine\ORM\ORMException
     */
    public function addFacultyCourseAssoc($personId, $organizationId, $courseId, $permissionsetId = null)
    {
        $em = $this->getEntityManager();
        $orgCourseFaculty = new OrgCourseFaculty();
        $orgCourseFaculty->setPerson($em->getReference(Person::class, $personId));
        $orgCourseFaculty->setCourse($em->getReference(OrgCourses::class, $courseId));
        $orgCourseFaculty->setOrganization($em->getReference(Organization::class, $organizationId));
        if (!is_null($permissionsetId) && $permissionsetId > 0) {
            $orgCourseFaculty->setOrgPermissionset($em->getReference(OrgPermissionset::class, $permissionsetId));
        }
        $em->persist($orgCourseFaculty);
        $em->flush();
    }

    /**
     * @param int $personId
     * @param int $courseId
     */
    public function removeFacultyCourseAssoc($personId, $courseId)
    {
        /** @var OrgCourseFaculty[] $courses */
        $courses = $this->findBy([
            'person' => $personId,
            'course' => $courseId,
        ]);

        foreach ($courses as $course) {
            $this->remove($course);
        }

        $this->flush();
    }
    
    public function deleteBulkFacultyEnrolledCourse($facultyId, $orgId)
    {
        $dateTime = new \DateTime('now');
        $em = $this->getEntityManager();
    
        $query = $em->createQuery('UPDATE  ' .CourseConstant::ORG_FACULTY_REPO . ' as e SET e.deletedAt = :datetime WHERE e.person = :person AND e.organization = :org AND e.deletedAt IS NULL');
        $query->setParameters([
            'datetime' => $dateTime,
            'person' => $facultyId,
            'org' => $orgId
            ]);
        $query->execute();
    }
    
    public function listFacultyCourses($person,$orgId,$currentDate,$year)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
        ->select('oc.id as course_id,oc.courseName,  p.id as staff_permissionset_id, p.permissionsetName as PermissionSet')
        ->addSelect('oc.courseSectionId as course_section_id','oc.sectionNumber as section_number')
        ->addSelect('oat.name as term_name','oat.termCode as term_code')
        ->addSelect('oay.name as academic_year')
        ->from(CourseConstant::ORG_FACULTY_REPO, 'cf')
        ->LEFTJoin('SynapseAcademicBundle:OrgCourses', 'oc', \Doctrine\ORM\Query\Expr\Join::WITH, CourseConstant::CF_COURSE_OCID)
        ->LEFTjoin(CourseConstant::ORGPERMISSIONSET_ENTITY, 'p', \Doctrine\ORM\Query\Expr\Join::WITH, 'p.id = cf.orgPermissionset')
        ->join('SynapseAcademicBundle:OrgAcademicYear', 'oay', \Doctrine\ORM\Query\Expr\Join::WITH, 'oay.id = oc.orgAcademicYear')
        ->join('SynapseAcademicBundle:OrgAcademicTerms', 'oat', \Doctrine\ORM\Query\Expr\Join::WITH, 'oat.id = oc.orgAcademicTerms')
        
        ->where('cf.organization = :orgId AND cf.person = :person')
        ->andWhere('oay.yearId = :year')
        ->andWhere("oat.endDate >= '$currentDate' OR oat.startDate >= '$currentDate' ")
        ->setParameters(array(
            'orgId' => $orgId,
            'person' => $person,
            'year' =>$year
        ))
        ->orderBy('cf.id')
        ->getQuery();
        $resultSet = $qb->getResult();
        return $resultSet;
    }

    /**
     * Get permissions by faculty course
     *
     * @param string $coursesSelected
     * @param int $facultyId
     * @param int $organizationId
     * @return array
     */
    public function getPermissionsByFacultyCourse($coursesSelected, $facultyId, $organizationId)
    {
        $parameters = [
            'coursesSelected' => $coursesSelected,
            'facultyId' => $facultyId,
            'organizationId' => $organizationId
        ];
        $sql = "
                SELECT
                    ocf.org_permissionset_id
                FROM
                    org_course_faculty ocf
                WHERE
                    ocf.org_courses_id IN ( :coursesSelected)
                        AND ocf.person_id = :facultyId
                        AND ocf.organization_id = :organizationId
                        AND ocf.deleted_at IS NULL";
        $resultSet = $this->executeQueryFetchAll($sql, $parameters);
        return $resultSet;
    }

    /**
     * Gets the count of faculty for all courses by organization
     *
     * @param integer $organizationId
     * @return integer
     */
    public function getCourseFacultyCountByOrganization($organizationId)
    {
        $parameters = [
            'organizationId' => $organizationId
        ];

        $sql = "SELECT
                COUNT(DISTINCT person_id) AS faculty_count
            FROM
                org_course_faculty
            WHERE
                organization_id = :organizationId
                AND deleted_at IS NULL";

        $result = $this->executeQueryFetch($sql, $parameters);
        return (int)$result['faculty_count'];

    }
    
}
