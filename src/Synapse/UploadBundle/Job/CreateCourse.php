<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\AcademicBundle\EntityDto\AcademicUpdateCourseDTO;
use Synapse\AcademicBundle\Repository\OrgAcademicTermRepository;
use Synapse\AcademicBundle\Repository\OrgAcademicYearRepository;
use Synapse\AcademicBundle\Repository\OrgCoursesRepository;
use Synapse\AcademicBundle\Service\Impl\CourseService;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class CreateCourse extends ContainerAwareJob
{

    public function run($args)
    {
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $organizationId = $args['orgId'];

        $courseService = $this->getContainer()->get(CourseService::SERVICE_KEY);
        $repositoryResolver = $this->getContainer()->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $cache = $this->getContainer()->get(SynapseConstant::REDIS_CLASS_KEY);
        $uploadFileLogService = $this->getContainer()->get(UploadFileLogService::SERVICE_KEY);
        $organizationRepository = $repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $academicYearRepository = $repositoryResolver->getRepository(OrgAcademicYearRepository::REPOSITORY_KEY);
        $academicTermRepository = $repositoryResolver->getRepository(OrgAcademicTermRepository::REPOSITORY_KEY);
        $orgCoursesRepository = $repositoryResolver->getRepository(OrgCoursesRepository::REPOSITORY_KEY);
        $entityValidator = $this->getContainer()->get(SynapseConstant::VALIDATOR);
        $logger = $this->getContainer()->get(SynapseConstant::LOGGER_KEY);

        $errors = [];
        $orgArray = [];
        $orgArray[UploadConstant::ORG] = [];
        $orgArray[CourseConstant::ACADEMIC_YEAR] = [];
        $orgArray[CourseConstant::ACADEMIC_TERM] = [];

        $validRows = 0;
        $updatedCourseRows = 0;
        $createdCourseRows = 0;
        $unchangedCourseRows = 0;

        $requiredItems = [
            CourseConstant::YEARID,
            CourseConstant::TERMID,
            CourseConstant::UNIQUECOURSESECID,
            CourseConstant::SUBJECTCODE,
            CourseConstant::COURSENO,
            CourseConstant::SECNO,
            CourseConstant::COURSENAME,
            CourseConstant::COLLGEGCODE,
            CourseConstant::DEPTCODE
        ];


        $validColumns = [
            'yearid',
            'termid',
            'uniquecoursesectionid',
            'collegecode',
            'deptcode',
            'subjectcode',
            'coursenumber',
            'coursename',
            'sectionnumber',
            'credithours',
            'days/times',
            'location'
        ];

        $entityErrorColumns = [
            'organization' => 'UniqueCourseSectionId',
            'courseSectionId' => 'UniqueCourseSectionId'
        ];

        foreach ($creates as $id => $data) {

            $requiredMissing = false;
            $stopCourseInsertFlag = false;

            foreach ($requiredItems as $item) {

                // We wait until the last possible moment to lowercase the required
                //  items so we can display camel case column headers to the user
                if (! array_key_exists(strtolower($item), $data) || strlen(trim($data[strtolower($item)])) == 0) {
                    $errors[$id][] = [
                        "name" => $item,
                        UploadConstant::VALUE => "",
                        UploadConstant::ERRORS => [
                            "{$item} is a required field"
                        ]
                    ];
                    $requiredMissing = true;
                }
            }

            // this will check all of the column headers to see if they are valid column headers
            foreach ($data as $name => $value) {

                if (!in_array($name, $validColumns)) {
                    if (trim($value) !== "") {
                        $errors[$id][] = [
                            "name" => $name,
                            'value' => "",
                            'errors' => [
                                "is not a valid column, any data included in this column has been ignored"
                            ]
                        ];
                    }
                }
            }

            if ( $requiredMissing ) {
                continue;
            }

            if (empty($orgArray['organization'][$organizationId])) {
                $organization = $organizationRepository->findOneById($organizationId);
                $orgArray['organization'][$organizationId] = $organization;
            } else {
                $organization = $orgArray['organization'][$organizationId];
            }

            if (empty($orgArray[CourseConstant::ACADEMIC_YEAR][$organizationId][$data[strtolower(CourseConstant::YEARID)]])) {
                $academicYear = $academicYearRepository->findOneBy(array(
                    'yearId' => $data[strtolower(CourseConstant::YEARID)],
                    'organization' => $organization
                ));
                $orgArray[CourseConstant::ACADEMIC_YEAR][$organizationId][$data[strtolower(CourseConstant::YEARID)]] = $academicYear;
            } else {
                $academicYear = $orgArray[CourseConstant::ACADEMIC_YEAR][$organizationId][$data[strtolower(CourseConstant::YEARID)]];
            }

            if (empty($orgArray[CourseConstant::ACADEMIC_TERM][$organizationId][$data[strtolower(CourseConstant::YEARID)]][$data[strtolower(CourseConstant::YEARID)]])) {
                $academicTerm = $academicTermRepository->findOneBy(array(
                    'termCode' => $data[strtolower(CourseConstant::TERMID)],
                    'organization' => $organization,
                    'orgAcademicYearId' => $academicYear
                ));
                $orgArray[CourseConstant::ACADEMIC_TERM][$organizationId][$data[strtolower(CourseConstant::YEARID)]][$data[strtolower(CourseConstant::TERMID)]] = $academicTerm;
            } else {
                $academicTerm = $orgArray[CourseConstant::ACADEMIC_TERM][$organizationId][$data[strtolower(CourseConstant::YEARID)]][$data[strtolower(CourseConstant::TERMID)]];
            }

            if (! $organization) {
                $errors[$id][] = [
                    'name' => 'Organization',
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "Organization ID does not exist."
                    ]
                ];
                $stopCourseInsertFlag = true;
            }
            if (! $academicYear) {
                $errors[$id][] = [
                    'name' => 'Academic Year',
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "Academic Year does not exist"
                    ]
                ];
                $stopCourseInsertFlag = true;
            }
            if (! $academicTerm) {
                $errors[$id][] = [
                    'name' => 'Academic Term',
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "Academic Term does not exist"
                    ]
                ];
                $stopCourseInsertFlag = true;
            }

            if ($stopCourseInsertFlag == true) {
                continue;
            }

            $uniqueCourseSectionID = isset($data[strtolower(CourseConstant::UNIQUECOURSESECID)]) ? $data[strtolower(CourseConstant::UNIQUECOURSESECID)] : "";
            $subjectCode = isset($data[strtolower(CourseConstant::SUBJECTCODE)]) ? $data[strtolower(CourseConstant::SUBJECTCODE)] : "";
            $courseNumber = isset($data[strtolower(CourseConstant::COURSENO)]) ? $data[strtolower(CourseConstant::COURSENO)] : "";
            $sectionNumber = isset($data[strtolower(CourseConstant::SECNO)]) ? $data[strtolower(CourseConstant::SECNO)] : "";
            $courseName = isset($data[strtolower(CourseConstant::COURSENAME)]) ? $data[strtolower(CourseConstant::COURSENAME)] : "";
            $creditHours = isset($data[strtolower(CourseConstant::CREDIT_HOURS)]) ? $data[strtolower(CourseConstant::CREDIT_HOURS)] : "";
            $collegeCode = isset($data[strtolower(CourseConstant::COLLGEGCODE)]) ? $data[strtolower(CourseConstant::COLLGEGCODE)] : "";
            $deptCode = isset($data[strtolower(CourseConstant::DEPTCODE)]) ? $data[strtolower(CourseConstant::DEPTCODE)] : "";


            $existingCourseId = '';
            $course = $orgCoursesRepository->findOneBy(array(
                'externalId' => $uniqueCourseSectionID,
                'orgAcademicYear' => $academicYear,
                'orgAcademicTerms' => $academicTerm,
                'organization' => $organization
            ));

            $updatedOrUnchangedFlag = false;
            $unchangedFlag = true;
            if ( $course )
            {
                $existingCourseId = $uniqueCourseSectionID;
                $updatedOrUnchangedFlag = true;
                // updated course
            } else {
                $logger->info(">>>>>>>>>>>> INSERT".$organization->getId()."-".$uniqueCourseSectionID);
                $course = new OrgCourses();
                // created course
            }


            // checks to see what has changed from the
            // original course within teh database
            if($course->getOrganization() != $organization){
                $unchangedFlag = false;
            }
            $course->setOrganization($organization);

            if($course->getOrgAcademicYear() != $academicYear){
                $unchangedFlag = false;
            }
            $course->setOrgAcademicYear($academicYear);

            if($course->getOrgAcademicTerms() != $academicTerm){
                $unchangedFlag = false;
            }
            $course->setOrgAcademicTerms($academicTerm);

            if($course->getCourseSectionId() != $uniqueCourseSectionID){
                $unchangedFlag = false;
            }
            $course->setCourseSectionId($uniqueCourseSectionID);

            if($course->getExternalId() != $uniqueCourseSectionID){
                $unchangedFlag = false;
            }
            $course->setExternalId($uniqueCourseSectionID);

            if($course->getSubjectCode() != $subjectCode){
                $unchangedFlag = false;
            }
            $course->setSubjectCode($subjectCode);

            if($course->getCourseNumber() != $courseNumber){
                $unchangedFlag = false;
            }
            $course->setCourseNumber($courseNumber);

            if($course->getSectionNumber() != $sectionNumber){
                $unchangedFlag = false;
            }
            $course->setSectionNumber($sectionNumber);

            if($course->getCourseName() != $courseName){
                $unchangedFlag = false;
            }
            $course->setCourseName($courseName);

            if($course->getCreditHours() != $creditHours){
                $unchangedFlag = false;
            }

            if($course->getCollegeCode() != $collegeCode){
                $unchangedFlag = false;
            }
            $course->setCollegeCode($collegeCode);

            if($course->getDeptCode() != $deptCode){
                $unchangedFlag = false;
            }
            $course->setDeptCode($deptCode);

            // If the data is not entered then these two columns can be ignored
            $dayTimeField = trim($data[strtolower(CourseConstant::DAYS_TIMES)]);
            $locationField = trim($data[strtolower(CourseConstant::LOCATION)]);
            if($dayTimeField == UploadConstant::CLEAR_FIELD)
            {
                $course->setDaysTimes(null);
            }else{
                if(strlen($dayTimeField) > 0) {
                    if(strlen($dayTimeField) <= UploadConstant::MAX_CHARACTER_LENGTH){
                        $course->setDaysTimes($data[strtolower(CourseConstant::DAYS_TIMES)]);
                    } else {
                        $errors[$id][] = [
                            'name' => 'DaysTimes',
                            'value' => '',
                            'errors' => [
                                'DaysTimes length cannot exceed '.UploadConstant::MAX_CHARACTER_LENGTH.' characters in length'
                            ]
                        ];
                    }
                }
            }

            if($locationField == UploadConstant::CLEAR_FIELD)
            {
                $course->setLocation(null);

            }else{
                if(strlen($locationField) > 0) {
                    if(strlen($locationField) <= UploadConstant::MAX_CHARACTER_LENGTH){
                        $course->setLocation($data[strtolower(CourseConstant::LOCATION)]);
                    } else {
                        $errors[$id][] = [
                            'name' => 'Location',
                            'value' => '',
                            'errors' => [
                                'Location length cannot exceed '.UploadConstant::MAX_CHARACTER_LENGTH.' characters in length'
                            ]
                        ];
                    }
                }
            }

            if (trim($creditHours) !== "") {
                if ($creditHours == UploadConstant::CLEAR_FIELD) {
                    $course->setCreditHours(NULL);
                } else if (!is_numeric($creditHours)) {
                    $errors[$id][] = [
                        'name' => 'CreditHours',
                        'value' => '',
                        'errors' => [
                            'Credit hours must be a number'
                        ]
                    ];

                } else if ($creditHours > 40) {
                    $errors[$id][] = [
                        'name' => 'CreditHours',
                        'value' => '',
                        'errors' => [
                            'Credit hours must be less than 40'
                        ]
                    ];
                } else if ($creditHours < 0) {
                    $errors[$id][] = [
                        'name' => 'CreditHours',
                        'value' => '',
                        'errors' => [
                            'Credit hours must be more than 0'
                        ]
                    ];
                } else {
                    $course->setCreditHours($creditHours);
                }

            }



            $entityErrors = $entityValidator->validate($course);
            $logger->info(" >>>>>>>>>>>>> Validated Entity".count($entityErrors));
            if (count($entityErrors) > 0) {
                foreach ($entityErrors as $error) {
                    // When CourseName exists, getPropertyPath returns 'organization' as column name. It should be change to 'UniqueCourseSectionId'
                    //
                    // Reasoning for this fix:
                    // This is because the entity uses 'organization_id' and 'courseSectionId' as a unique key. This means that the error will be displayed as
                    // "organization - course section id already exists." This will change the error message to be 
                    // "UniqueCourseSectionId - course section id already exists". 
                    // The reason for this is that organization can throw two errors one for this issue and one if the organization id does not exist.
                    // In that case we will see "UniqueCourseSectionId - organizaton does not exist". This error is an edge case. 
                    // Course Section Id maps directly UniqueCourseSectionId in the CSV headers
                    $entityProperty = $error->getPropertyPath();
                    if (isset($entityErrorColumns[$entityProperty])) {
                        $entityProperty = $entityErrorColumns[$entityProperty];
                    }
                    $errors[$id][] = [
                        'name' => $entityProperty,
                        'value' => '',
                        'errors' => [
                            $error->getMessage()
                        ]
                    ];
                }
                $logger->info(" >>>>>>>>>>>>> Validated Entity ".$entityErrors[0]->getMessage());
                $organization = $organizationRepository->findOneById($organizationId);
                $orgArray['organization'][$organizationId] = $organization;

                $orgArray[CourseConstant::ACADEMIC_YEAR][$organizationId][$data[strtolower(CourseConstant::YEARID)]] = $academicYear;

                $orgArray[CourseConstant::ACADEMIC_TERM][$organizationId][$data[strtolower(CourseConstant::YEARID)]][$data[strtolower(CourseConstant::TERMID)]] = $academicTerm;
                continue;
            }


            (strlen(trim($existingCourseId)) > 0) ? $orgCoursesRepository->update($course) : $orgCoursesRepository->persist($course);

            if($updatedOrUnchangedFlag){
                if($unchangedFlag){
                    $unchangedCourseRows ++;
                } else {
                    $updatedCourseRows ++;
                }
            } else {
                $createdCourseRows ++;
            }
            $validRows ++;
        }

        // a rows that are valid have been created?
        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($errors));
        $uploadFileLogService->updateCreatedRowCount($uploadId, $createdCourseRows);
        $uploadFileLogService->updateUpdatedRowCount($uploadId, $updatedCourseRows + $unchangedCourseRows);

        $courseService->flush();
        $orgCoursesRepository->clear();

        unset($orgArray);

        $jobs = $cache->fetch("organization.{$organizationId}.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("organization.{$organizationId}.upload.{$uploadId}.jobs", $jobs);

        $cache->save("organization:{$organizationId}:upload:{$uploadId}:job:{$jobNumber}:errors", $errors);

        return $errors;
    }
}
