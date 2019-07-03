<?php
namespace Synapse\AcademicBundle\Service\Impl;

use Monolog\Logger;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\AcademicBundle\EntityDto\CoordinatorCourseDto;
use Synapse\AcademicBundle\EntityDto\AcademicUpdateCourseDTO;
use Synapse\AcademicBundle\EntityDto\AcademicUpdateCourseListDTO;
use Synapse\AcademicBundle\EntityDto\CourseNavigationListDto;
use Synapse\AcademicBundle\EntityDto\FacultyDetailsDto;
use Synapse\AcademicBundle\EntityDto\FacultyPermissionSetDto;
use Synapse\AcademicBundle\EntityDto\SingleCourseDto;
use Synapse\AcademicBundle\EntityDto\StudentDetailsDto;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Repository\OrgCourseStudentRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicRecordRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\RestBundle\Exception\ValidationException;

/**
 * Class CourseServiceHelper
 * @package Synapse\AcademicBundle\Service\Impl
 */
class CourseServiceHelper extends AbstractService
{
    /**
     * @var AcademicRecordRepository
     */
    private $academicRecordRepository;
    /**
     * @var AcademicUpdateRepository
     */
    private $academicUpdateRepository;

    /**
     * @var MetadataListValuesRepository
     */
    private $metadataListValuesRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgCourseFacultyRepository
     */
    private $orgCourseFacultyRepository;

    /**
     * @var OrgCoursesRepository
     */
    private $orgCourseRepository;

    /**
     * @var OrgCourseStudentRepository
     */
    private $orgCourseStudentRepository;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * CourseServiceHelper constructor
     *
     * @param RepositoryResolver $repositoryResolver
     * @param Logger $logger
     */
    public function __construct($repositoryResolver, $logger)
    {
        parent::__construct($repositoryResolver, $logger);

        //Repository
        $this->academicRecordRepository = $this->repositoryResolver->getRepository(AcademicRecordRepository::REPOSITORY_KEY);
        $this->academicUpdateRepository = $this->repositoryResolver->getRepository(AcademicUpdateRepository::REPOSITORY_KEY);
        $this->metadataListValuesRepository = $this->repositoryResolver->getRepository(MetadataListValuesRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgCourseFacultyRepository = $this->repositoryResolver->getRepository(OrgCourseFacultyRepository::REPOSITORY_KEY);
        $this->orgCourseRepository = $this->repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $this->orgCourseStudentRepository = $this->repositoryResolver->getRepository(OrgCourseStudentRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
    }

    /**
     * Check object exists or not
     *
     * @param object $object
     * @param string $message
     * @param string $key
     * @throws ValidationException
     */
    protected function isObjectExist($object, $message, $key)
    {
        if (! isset($object) || empty($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    /**
     * Check existence of user type
     *
     * @param string $userType
     * @throws ValidationException
     */
    protected function isUserTypeExists($userType)
    {
        $userTypes = array(
            CourseConstant::COORDINATOR,
            CourseConstant::FACULTY
        );
        if (! in_array($userType, $userTypes)) {
            throw new ValidationException([
                CourseConstant::INVALID_USER_TYPE
            ], CourseConstant::INVALID_USER_TYPE, CourseConstant::INVALID_USER_TYPE_KEY);
        }
    }

    /**
     * Get timezone
     *
     * @param string $userType
     * @param Organization $personOrganization
     * @return string
     */
    protected function getTimeZone($userType, $personOrganization)
    {
        if ($userType == CourseConstant::NAVIGATION) {
            $timezone = $personOrganization->getTimeZone();
        } else {
            $timezone = $personOrganization->getOrganization()->getTimeZone();
        }
        $timezone = $this->metadataListValuesRepository->findByListName($timezone);
        if ($timezone) {
            $timezone = $timezone[0]->getListValue();
        }
        try {
            $currentNow = new \DateTime('now', new \DateTimeZone($timezone));
            $currentNow->setTimezone(new \DateTimeZone('UTC'));
        } catch (Exception $e) {
            $currentNow = new \DateTime('now');
        }
        $currentDate = $currentNow->format(CourseConstant::YMD_FORMAT);
        return $currentDate;
    }

    /**
     * Validate coordinator
     *
     * @param string $userType
     * @param OrganizationRoleRepository $orgRoleRepository
     * @param int $organizationId
     * @param Person $user
     */
    protected function validateCoordinator($userType, $orgRoleRepository, $organizationId, $user)
    {
        if ($userType == CourseConstant::COORDINATOR) {
            $coordinator = $orgRoleRepository->getUserCoordinatorRole($organizationId, $user);
            $this->isObjectExist($coordinator, CourseConstant::COORDINATOR_NOT_FOUND, CourseConstant::COORDINATOR_NOT_FOUND_KEY);
        }
    }

    /**
     * Validate year
     *
     * @param OrgAcademicYearRepository $orgAcademicYearRepository
     * @param int $yearId
     */
    protected function validateYear($orgAcademicYearRepository, $yearId)
    {
        if ($yearId != 'all') {
            $year = $orgAcademicYearRepository->findOneByYearId($yearId);
            $this->isObjectExist($year, CourseConstant::YEAR_NOT_FOUND, CourseConstant::YEAR_NOT_FOUND_KEY);
        }
    }

    /**
     * Validate faculty
     *
     * @param string $type
     * @param OrgCourseFacultyRepository $orgCourseFacultyRepository
     * @param int $organizationId
     * @param int $courseId
     * @param int $userId
     */
    protected function isFacultyExist($type, $orgCourseFacultyRepository, $organizationId, $courseId, $userId)
    {
        if ($type == CourseConstant::FACULTY) {
            $facultyCourse = $orgCourseFacultyRepository->findOneBy([
                CourseConstant::ORGANIZATION => $organizationId,
                'course' => $courseId,
                CourseConstant::PERSON => $userId
            ]);
            $this->isObjectExist($facultyCourse, CourseConstant::FACULTY_NOT_FOUND, CourseConstant::FACULTY_NOT_FOUND_KEY);
        }
    }

    /**
     * Get permission DTO based on orgId and faculty
     *
     * @param int $organizationId
     * @param int $facultyId
     * @return array
     */
    protected function permissionDTO($organizationId, $facultyId)
    {
        $coursePermissions = $this->orgGroupFacultyRepository->findBy([
            CourseConstant::ORGANIZATION => $organizationId,
            CourseConstant::PERSON => $facultyId
        ]);
        $permissions = array();
        if (! empty($coursePermissions)) {
            foreach ($coursePermissions as $coursePermission) {
                $orgPermissionSet = $coursePermission->getOrgPermissionset();
                if ($orgPermissionSet && $orgPermissionSet != null) {
                    $permissionsDto = new FacultyPermissionSetDto();
                    $permissionsDto->setTemplateId($orgPermissionSet->getId());
                    $permissionsDto->setTemplateName($orgPermissionSet->getPermissionsetName());
                }
                $permissions[] = $permissionsDto;
            }
        }
        return $permissions;
    }

    /**
     * Gets faculty members associated with any course in the organization
     *
     * @param int $organizationId
     * @return array
     */
    protected function getFaculty($organizationId)
    {
        $staff = [];
        $facultyResults = $this->orgCourseRepository->getFacultyList($organizationId);
        if ($facultyResults) {
            foreach ($facultyResults as $faculty) {
                $staff[$faculty['id']][] = $faculty;
            }
        }
        return $staff;
    }

    /**
     * Format the passed in array of courses into array groups of year / term / college and department
     *
     * @param array $arraySet
     * @return array
     */
    public function formatStudentCourseList($arraySet)
    {
        $arrayGroup = [];
        foreach ($arraySet as $array) {
            $key = $array['org_academic_year_id'] . '_' . $array['org_academic_terms_id'] . '_' . $array['college_code'] . '_' . $array['dept_code'];
            if (! isset($arrayGroup[$key])) {
                $arrayGroup[$key]['org_academic_year_id'] = $array['org_academic_year_id'];
                $arrayGroup[$key]['org_academic_terms_id'] = $array['org_academic_terms_id'];
                $arrayGroup[$key]['college_code'] = $array['college_code'];
                $arrayGroup[$key]['dept_code'] = $array['dept_code'];
                $arrayGroup[$key]['year_id'] = $array['year_id'];
                $arrayGroup[$key]['term_name'] = $array['term_name'];
                $arrayGroup[$key]['current_or_future_term_course'] = $array['current_or_future_term_course'];
            }
            $arrayGroup[$key]['courses'][] = $array;
        }
        return $arrayGroup;
    }

    /**
     * Get course details
     *
     * @param int $courseId
     * @param array $facultyList
     * @param array $studentList
     * @param OrgCourses $course
     * @param string $type
     * @param int $organizationId
     * @param int $loggedInUserId
     * @return SingleCourseDto
     */
    protected function getCourseDetailsJSON($courseId, $facultyList, $studentList, $course, $type, $organizationId, $loggedInUserId)
    {
        $singleCourseDto = new SingleCourseDto();
        $singleCourseDto->setTotalFaculties(count($facultyList));
        $singleCourseDto->setTotalStudents(count($studentList));
        $singleCourseDto->setCourseId($courseId);
        $singleCourseDto->setCourseName($course->getCourseName());
        $subjectCode = $course->getSubjectCode() . $course->getCourseNumber();
        $singleCourseDto->setSubjectCode($subjectCode);
        $singleCourseDto->setSectionNumber($course->getSectionNumber());

        if ($type == 'faculty') {
            $facultyCourse = $this->orgCourseFacultyRepository->findOneBy([
                'organization' => $organizationId,
                'course' => $courseId,
                'person' => $loggedInUserId
            ]);

            // set $createViewAcademicUpdate
            if ($facultyCourse->getOrgPermissionset() && $facultyCourse->getOrgPermissionset()->getCreateViewAcademicUpdate()) {
                $createViewAcademicUpdate = true;
            } else {
                $createViewAcademicUpdate = false;
            }
            // set $viewAllAcademicUpdateCourses
            if ($facultyCourse->getOrgPermissionset() && $facultyCourse->getOrgPermissionset()->getViewAllAcademicUpdateCourses()) {
                $viewAllAcademicUpdateCourses = true;
            } else {
                $viewAllAcademicUpdateCourses = false;
            }

            $singleCourseDto->setCreateViewAcademicUpdate($createViewAcademicUpdate);
            $singleCourseDto->setViewAllAcademicUpdateCourses($viewAllAcademicUpdateCourses);
        }

        $facultyDetails = [];
        if (count($facultyList) > 0) {

            foreach ($facultyList as $faculty) {
                $facultyId = $faculty['faculty_id'];
                $facultyDetailsDto = new FacultyDetailsDto();
                $facultyDetailsDto->setFacultyId($facultyId);
                $facultyDetailsDto->setFirstName($faculty['firstname']);
                $facultyDetailsDto->setLastName($faculty['lastname']);
                $facultyDetailsDto->setEmail($faculty['primary_email']);
                if ($type != 'faculty') {
                    $facultyDetailsDto->setPermissionsetId($faculty['org_permission_set']);
                    $facultyDetailsDto->setId($faculty['external_id']);
                } else {
                    $permissions = $this->permissionDTO($organizationId, $facultyId);
                    $facultyDetailsDto->setPermissions($permissions);
                    $facultyDetailsDto->setId($faculty['external_id']);
                }
                $facultyDetails[] = $facultyDetailsDto;
            }
        }
        $studentDetails = [];
        if (count($studentList) > 0) {
            foreach ($studentList as $student) {
                $studentDetailsDto = new StudentDetailsDto();
                $studentDetailsDto->setStudentId($student['student_id']);
                $studentDetailsDto->setFirstName($student['firstname']);
                $studentDetailsDto->setLastName($student['lastname']);
                $studentDetailsDto->setEmail($student['primary_email']);
                $studentDetailsDto->setId($student['external_id']);
                $studentStatus = $student['student_status'];

                $academicRecord = $this->academicRecordRepository->findOneBy(['personStudent' => $student['student_id'], 'orgCourses' => $courseId]);
                $hasAcademicRecordData = isset($academicRecord) && !is_null($academicRecord->getUpdateDate());

                if ($hasAcademicRecordData) {
                    $studentDetailsDto->setAcademicUpdates(1);
                } else {
                    $studentDetailsDto->setAcademicUpdates(0);
                }

                // if $studentStatus length is 0, set $studentStatus to 1
                if (!strlen($studentStatus)) {
                    $studentStatus = 1;
                }
                $studentDetailsDto->setStudentStatus($studentStatus);
                $studentDetails[] = $studentDetailsDto;
            }
        }
        $singleCourseDto->setFacultyDetails($facultyDetails);
        $singleCourseDto->setStudentDetails($studentDetails);
        return $singleCourseDto;
    }

    /**
     * Get course by user roles
     *
     * @param array $courseDetails
     * @param array $courseFacultyCountArray
     * @param array $courseStudentCountArray
     * @param string $userType
     * @param int $courseListCount
     * @return array
     */
    public function formatCourseList($courseDetails, $courseFacultyCountArray, $courseStudentCountArray, $userType, $courseListCount)
    {

        $coordinatorCourses = [];
        $coordinatorCourses['total_courses'] = $courseListCount;
        if ($userType == 'coordinator') {
            $facultyCount = array_sum($courseFacultyCountArray);
            $studentCount = array_sum($courseStudentCountArray);
            $coordinatorCourses['total_faculty'] = $facultyCount;
            $coordinatorCourses['total_students'] = $studentCount;
        }
        $groupByYearArray = [];
        $yearSpecificData = [];
        $termSpecificData = [];
        $collegeSpecificData = [];
        $departmentSpecificData = [];
        $courseSpecificData = [];
        $sectionSpecificData = [];
        foreach ($courseDetails as $courseDetail) {
            $courseSubjectNumberName = $courseDetail['subject_code'] . $courseDetail['course_number'] . trim($courseDetail['course_name']);
            $groupByYearArray
            [$courseDetail['year_id']]
            ['terms'][$courseDetail['term_code']]
            ['colleges'] [$courseDetail['college_code']]
            ['departments'] [$courseDetail['dept_code']]
            ['courses'] [$courseSubjectNumberName]
            ['sections'] [$courseDetail['course_section_id']] = $courseDetail;

            $yearSpecificData[$courseDetail['year_id']] = $courseDetail['year_name'];
            $termSpecificData[$courseDetail['term_code'] . "-" . $courseDetail['year_id']] = $courseDetail['term_name'];
            $collegeSpecificData[$courseDetail['college_code']] = $courseDetail['college_code'];
            $departmentSpecificData[$courseDetail['dept_code']] = $courseDetail['dept_code'];
            $courseSpecificData[$courseSubjectNumberName]['subject_code'] = $courseDetail['subject_code'];
            $courseSpecificData[$courseSubjectNumberName]['course_number'] = $courseDetail['course_number'];
            $courseSpecificData[$courseSubjectNumberName]['course_name'] = trim($courseDetail['course_name']);
            if (isset($courseDetail['org_course_id'])) {
                $sectionSpecificData[$courseDetail['course_section_id']]['course_id'] = $courseDetail['org_course_id'];
            } else {
                $sectionSpecificData[$courseDetail['course_section_id']]['course_id'] = $courseDetail['course_id'];
            }
            $sectionSpecificData[$courseDetail['course_section_id']]['section_number'] = $courseDetail['section_number'];
            $sectionSpecificData[$courseDetail['course_section_id']]['course_section_id'] = $courseDetail['course_section_id'];
            $sectionSpecificData[$courseDetail['course_section_id']]['location'] = $courseDetail['location'];
            $sectionSpecificData[$courseDetail['course_section_id']]['days_times'] = $courseDetail['days_times'];

            if ($userType == 'faculty') {
                if (isset($courseDetail['create_view_academic_update'])) {
                    $createViewAcademicUpdate = $courseDetail['create_view_academic_update'];
                } else {
                    $createViewAcademicUpdate = false;
                }

                if (isset($courseDetail['view_all_academic_update_courses'])) {
                    $viewAllAcademicUpdateCourses = $courseDetail['view_all_academic_update_courses'];
                } else {
                    $viewAllAcademicUpdateCourses = false;
                }

                $sectionSpecificData[$courseDetail['course_section_id']]['create_view_academic_update'] = $createViewAcademicUpdate;
                $sectionSpecificData[$courseDetail['course_section_id']]['view_all_academic_update_courses'] = $viewAllAcademicUpdateCourses;
            }
        }
        $courseListArray = [];
        foreach ($groupByYearArray as $key => $value) {
            $yearArray = [];
            $yearArray['year_id'] = $key;
            $yearArray['year_name'] = $yearSpecificData[$key];
            foreach ($value['terms'] as $termKey => $termValue) {
                $termArray = [];
                $termArray['term_code'] = $termKey;
                $termArray['term_name'] = $termSpecificData[$termKey . "-" . $key];

                foreach ($termValue['colleges'] as $collegeKey => $collegeValue) {
                    $collegeArray = [];
                    $collegeArray['college_code'] = $collegeSpecificData[$collegeKey];

                    foreach ($collegeValue['departments'] as $departmentKey => $departmentValue) {
                        $departmentArray = [];
                        $departmentArray['dept_code'] = $departmentSpecificData[$departmentKey];

                        foreach ($departmentValue['courses'] as $courseKey => $courseValue) {
                            $courseArray = [];
                            $courseArray['subject_code'] = $courseSpecificData[$courseKey]['subject_code'];
                            $courseArray['course_number'] = $courseSpecificData[$courseKey]['course_number'];
                            $courseArray['course_name'] = $courseSpecificData[$courseKey]['course_name'];

                            foreach ($courseValue['sections'] as $sectionKey => $sectionValue) {
                                $sectionArray = [];
                                $sectionArray['course_id'] = $sectionSpecificData[$sectionKey]['course_id'];
                                $sectionArray['section_number'] = $sectionSpecificData[$sectionKey]['section_number'];
                                $sectionArray['course_section_id'] = $sectionSpecificData[$sectionKey]['course_section_id'];
                                $sectionArray['location'] = $sectionSpecificData[$sectionKey]['location'];
                                $sectionArray['days_times'] = $sectionSpecificData[$sectionKey]['days_times'];

                                // set $facultyCount
                                if (isset($courseFacultyCountArray[$sectionSpecificData[$sectionKey]['course_id']])) {
                                    $facultyCount = $courseFacultyCountArray[$sectionSpecificData[$sectionKey]['course_id']];
                                } else {
                                    $facultyCount = 0;
                                }

                                // set $studentCount
                                if (isset($courseStudentCountArray[$sectionSpecificData[$sectionKey]['course_id']])) {
                                    $studentCount = $courseStudentCountArray[$sectionSpecificData[$sectionKey]['course_id']];
                                } else {
                                    $studentCount = 0;
                                }

                                $sectionArray['total_faculty'] = $facultyCount;
                                $sectionArray['total_students'] = $studentCount;

                                if ($userType == 'faculty') {

                                    // set $createViewAcademicUpdate
                                    if (isset($sectionValue['create_view_academic_update'])) {
                                        $createViewAcademicUpdate = true;
                                    } else {
                                        $createViewAcademicUpdate = false;
                                    }

                                    // set $viewAllAcademicUpdateCourses
                                    if (isset($sectionValue['view_all_academic_update_courses'])) {
                                        $viewAllAcademicUpdateCourses = true;
                                    } else {
                                        $viewAllAcademicUpdateCourses = false;
                                    }

                                    $sectionArray['create_view_academic_update'] = $createViewAcademicUpdate;
                                    $sectionArray['view_all_academic_update_courses'] = $viewAllAcademicUpdateCourses;
                                }
                                $courseArray['sections'][] = $sectionArray;
                            }
                            $departmentArray['courses'][] = $courseArray;
                        }
                        $collegeArray['departments'][] = $departmentArray;
                    }
                    $termArray['colleges'][] = $collegeArray;
                }
                $yearArray['terms'][] = $termArray;
            }
            $courseListArray[] = $yearArray;
        }
        $coordinatorCourses['course_list'] = $courseListArray;

        return $coordinatorCourses;
    }

    /**
     * Get faculty DTO
     *
     * @param array $faculty
     * @param int $courseId
     * @return array
     */
    protected function getFacultyDTO($faculty, $courseId)
    {
        $facultyDetailsArray = [];
        if (! empty($faculty[$courseId])) {
            foreach ($faculty[$courseId] as $staff) {
                $facultyDetailsDto = new FacultyDetailsDto();
                $facultyDetailsDto->setFacultyId($staff['personId']);

                // set $firstName
                if ($staff['personFirstName']) {
                    $firstName = $staff['personFirstName'];
                } else {
                    $firstName = '';
                }

                // set $lastName
                if ($staff['personLastName']) {
                    $lastName = $staff['personLastName'];
                } else {
                    $lastName = '';
                }

                $facultyDetailsDto->setFacultyName($firstName . ' ' . $lastName);
                $facultyDetailsArray[] = $facultyDetailsDto;
            }
        }
        return $facultyDetailsArray;
    }

    /**
     * Get courses navigation dto based on course array
     *
     * @param array $courseValues
     * @param string $type
     * @param string $queryParam
     * @param integer $organizationId
     * @param string $userType
     * @return array
     */
    protected function courseNavigationDTO($courseValues, $type, $queryParam, $organizationId, $userType)
    {
        $organization = $this->organizationRepository->find($organizationId);
        $currentDate = $this->getTimeZone(CourseConstant::NAVIGATION, $organization);
        foreach ($courseValues as $course) {
            $courseList = new CourseNavigationListDto();
            if ($type == 'term') {
                $startDate = $course['startDate']->format(CourseConstant::YMD_FORMAT);
                $endDate = $course['endDate']->format(CourseConstant::YMD_FORMAT);
                if ($queryParam['year'] == '' || $queryParam['year'] == 'all') {
                    $fieldValue = "(" . $course['yearId'] . ")" . $course['name'];
                } else {
                    if ($userType == 'coordinator') {
                        $fieldValue = $course['name'] . "(" . $course['termCode'] . ")";
                    } else {
                        $fieldValue = $course['name'];
                    }
                }
                $courseList->setKey($course['id']);
                $courseList->setValue($fieldValue);
                if ($startDate <= $currentDate && $endDate >= $currentDate) {
                    $courseList->setCurrentTerm(true);
                } else {
                    $courseList->setCurrentTerm(false);
                }
            } else {
                $courseList->setKey($course['key']);
                $courseList->setValue($course['value']);
            }
            $courses[] = $courseList;
        }
        return $courses;
    }

}