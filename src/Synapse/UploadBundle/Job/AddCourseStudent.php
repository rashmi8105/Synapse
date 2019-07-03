<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\AcademicBundle\Entity\OrgCourseStudent;
use Synapse\CoreBundle\Util\Constants\CourseConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class AddCourseStudent extends ContainerAwareJob
{

    const UNIQUE_SECTIONID = 'UniqueCourseSectionId';

    const VALUE = 'value';

    const ERRORS = 'errors';

    const NAME = 'name';

    public function run($args)
    {
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $orgId = $args['orgId'];

        $personService = $this->getContainer()->get('person_service');
        $courseService = $this->getContainer()->get('course_service');
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $cache = $this->getContainer()->get('synapse_redis_cache');

        $organizationRepository = $repositoryResolver->getRepository('SynapseCoreBundle:Organization');
        $orgCourseStudentsRepository = $repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseStudent');
        $orgCourseFacultyRepository = $repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseFaculty');
        $orgPersonStudentRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgPersonStudent');

        $validator = $this->getContainer()->get('course_student_upload_validator_service');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $errors = [];

        $validRows = 0;
        $createdRows = 0;
        $updatedRows = 0;
        $deletedRows = 0;
        $unchangedRows = 0;

        $requiredItems = [
            self::UNIQUE_SECTIONID
        ];
        $organization = $organizationRepository->findOneById($orgId);

        foreach ($creates as $id => $data) {

            $requiredMissing = false;

            foreach ($requiredItems as $item) {

                if (! array_key_exists(strtolower($item), $data) || empty($data[strtolower($item)])) {
                    $errors[$id][] = [
                        self::NAME => $item,
                        self::VALUE => '',
                        self::ERRORS => [
                            "{$item} is a required field"
                        ]
                    ];
                    $requiredMissing = true;
                }

                if (! $validator->validate(strtolower($item), $data[strtolower($item)], $orgId)) {
                    $errors[$id][] = [
                        self::NAME => $item,
                        self::VALUE => $data[strtolower($item)],
                        self::ERRORS => $validator->getErrors()
                    ];
                    $requiredMissing = true;
                }
            }

            if ($requiredMissing) {
                continue;
            }

            $personId =  $data[strtolower(UploadConstant::STUDENTID)];
            $booleanRemove = ( array_key_exists(strtolower(UploadConstant::REMOVE),$data) && !empty($data[strtolower(UploadConstant::REMOVE)]))? strtolower($data[strtolower(UploadConstant::REMOVE)]) : "";

            $course = $courseService->findOneByExternalIdOrg($data[strtolower(self::UNIQUE_SECTIONID)], $orgId, false);

            if (! $course) {
                $errors[$id][] = [
                    self::NAME => self::UNIQUE_SECTIONID,
                    self::VALUE => '',
                    self::ERRORS => [
                        "Course ID does not exist"
                    ]
                ];
                continue;
            }

            $person = $personService->findOneByExternalIdOrg($personId, $orgId);

            if (! $person) {
                $errors[$id][] = [
                    self::NAME => UploadConstant::STUDENTID,
                    self::VALUE => '',
                    self::ERRORS => [
                        "Student ID does not exist"
                    ]
                ];
                continue;
            }

            $personStudent = $orgPersonStudentRepository->findOneBy(array('organization' => $organization, 'person' => $person));

            if( ! $personStudent ) {
                $errors[$id][] = [
                    self::NAME => UploadConstant::STUDENTID,
                    self::VALUE => '',
                    self::ERRORS => [
                        "Student ID does not exist."
                    ]
                ];
                continue;
            }

            $personCourseFaculty = $orgCourseFacultyRepository->findOneBy(['organization' => $orgId, 'course' => $course, 'person' => $person]);

            if ($personCourseFaculty) {
                $errors[$id][] = [
                    'name' => 'StudentId',
                    'value' => '',
                    'errors' => [
                        "ID " . $data[strtolower(UploadConstant::STUDENTID)] . " is already in course as a faculty. Cannot add as student."
                    ]
                ];
                continue;
            }

            $isOrgCourseStudent = $orgCourseStudentsRepository->findOneBy(['organization'=> $orgId,'course'=>$course,'person'=>$person]);
            if($isOrgCourseStudent)
            {
                if( $booleanRemove==strtolower(UploadConstant::REMOVE)) {
                    try{
                        $orgCourseStudentsRepository->remove($isOrgCourseStudent);
                    }catch (\Exception $e){
                        $errors[$id][] = [
                        self::NAME => UploadConstant::STUDENTID,
                        self::VALUE => '',
                        self::ERRORS => [
                        $person->getFirstname()." ".$person->getLastname()." is not enrolled in this course."
                            ]
                            ];
                        continue;
                    }
                    $deletedRows++;

                }elseif(! empty($booleanRemove)) {
                    $errors[$id][] = [
                        'name' => 'Remove',
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            UploadConstant::REMOVE_COLUMN_ERROR
                        ]
                    ];
                    continue;
                } else{

                    $unchangedFlag = true;
                    if($isOrgCourseStudent->getOrganization() != $organization)
                    {
                        $unchangedFlag = false;
                    }
                    $isOrgCourseStudent->setOrganization($organization);

                    if($isOrgCourseStudent->getPerson() != $person)
                    {
                        $unchangedFlag = false;
                    }
                    $isOrgCourseStudent->setPerson($person);

                    if($isOrgCourseStudent->getCourse() != $course)
                    {
                        $unchangedFlag = false;
                    }
                    $isOrgCourseStudent->setCourse($course);
                    // keep the person in the class
                    if($unchangedFlag){
                        $unchangedRows++;
                    }
                    else {
                        $updatedRows++;
                    }
                }
            }else{
                /**
                 * checking without entry removing
                 */
                if( $booleanRemove==strtolower(UploadConstant::REMOVE)) {
                    $errors[$id][] = [
                    self::NAME => UploadConstant::STUDENTID,
                    self::VALUE => '',
                    self::ERRORS => [
                    $person->getFirstname()." ".$person->getLastname()." is not enrolled in this course."
                        ]
                        ];
                    continue;
                }elseif(! empty($booleanRemove)) {
                    $errors[$id][] = [
                        'name' => 'Remove',
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            UploadConstant::REMOVE_COLUMN_ERROR
                        ]
                    ];
                    continue;
                } else{
                    $orgCourseStudentEntity =$orgCourseStudentsRepository->findOneBy(array('organization' => $organization, 'person' => $person, 'course' => $course));
                    $orgCourseStudents =  new OrgCourseStudent();
                    $orgCourseStudents->setOrganization($organization);
                    $orgCourseStudents->setPerson($person);
                    $orgCourseStudents->setCourse($course);
                    $orgCourseStudentsRepository->persist($orgCourseStudents);
                    $createdRows++;
                }

            }

            $validRows ++;
        }

        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($errors));
        $uploadFileLogService->updateCreatedRowCount($uploadId, $createdRows);
        $uploadFileLogService->updateUpdatedRowCount($uploadId, $updatedRows + $deletedRows + $unchangedRows);

        $orgCourseStudentsRepository->flush();
        $orgCourseStudentsRepository->clear();

        $jobs = $cache->fetch("organization.{$orgId}.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("organization.{$orgId}.upload.{$uploadId}.jobs", $jobs);

        $cache->save("organization:{$orgId}:upload:{$uploadId}:job:{$jobNumber}:errors", $errors);

        return $errors;
    }
}
