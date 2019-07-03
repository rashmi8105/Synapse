<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\AcademicBundle\Entity\OrgCourseFaculty;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class AddCourseFaculty extends ContainerAwareJob
{

    const FACULTYID = 'FacultyID';

    const PERMISSIONSET = 'PermissionSet';

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
        $orgCourseRepository = $repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourses');
        $orgPermissionsetRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgPermissionset');
        $orgCourseFacultyRepository = $repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseFaculty');
        $orgCourseStudentRepository = $repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseStudent');
        $orgPersonFacultyRepository = $repositoryResolver->getRepository('SynapseCoreBundle:OrgPersonFaculty');

        $validator = $this->getContainer()->get('course_faculty_upload_validator_service');
        $uploadFileLogService = $this->getContainer()->get('upload_file_log_service');
        $errors = [];

        $validRows = 0;
        $updatedFacultyCourseRows = 0;
        $unchangedFacultyCourseRows = 0;
        $createdFacultyCourseRows = 0;
        $deletedFacultyCourseRows = 0;

        $requiredItems = [
            UploadConstant::UNIQUECOURSESECTIONID,
            self::FACULTYID
        ];

        $organization = $organizationRepository->findOneById($orgId);

        foreach ($creates as $id => $data) {
            $requiredMissing = false;
            foreach ($requiredItems as $item) {
                if (! array_key_exists(strtolower($item), $data) || empty($data[strtolower($item)])) {
                    $errors[$id][] = [
                        'name' => $item,
                        UploadConstant::VALUE => '',
                        UploadConstant::ERRORS => [
                            "{$item} is a required field"
                        ]
                    ];
                    $requiredMissing = true;
                }
                if (! $validator->validate(strtolower($item), $data[strtolower($item)], $orgId)) {
                    $errors[$id][] = [
                        'name' => $item,
                        UploadConstant::VALUE => $data[strtolower($item)],
                        UploadConstant::ERRORS => $validator->getErrors()
                    ];
                    $requiredMissing = true;
                }
            }
            if ($requiredMissing) {
                continue;
            }

            $personId =  $data[strtolower(self::FACULTYID)];
            $booleanRemove = ( array_key_exists(strtolower(UploadConstant::REMOVE),$data) && !empty($data[strtolower(UploadConstant::REMOVE)]))? strtolower($data[strtolower(UploadConstant::REMOVE)]) : "";
            if (! empty($booleanRemove) && $booleanRemove !== 'remove') {
                $errors[$id][] = [
                    'name' => 'Remove',
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "'Remove' column accepts only 'remove' or 'Remove' text."
                    ]
                ];
                continue;
            }

            $course = $courseService->findOneByExternalIdOrg($data[strtolower(UploadConstant::UNIQUECOURSESECTIONID)], $orgId,false);
            if (! $course) {
                $errors[$id][] = [
                    'name' => UploadConstant::UNIQUECOURSESECTIONID,
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "Course ID does not exist"
                    ]
                ];
                continue;
            }

            $person = $personService->findOneByExternalIdOrg($personId, $orgId, false);
            if (! $person) {

                $errors[$id][] = [
                    'name' => self::FACULTYID,
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "Faculty ID does not exist."
                    ]
                ];
                continue;
            }

            $personFaculty = $orgPersonFacultyRepository->findOneBy(array(UploadConstant::ORGN => $organization, 'person' => $person));
            if( ! $personFaculty ) {
                $errors[$id][] = [
                    'name' => self::FACULTYID,
                    UploadConstant::VALUE => '',
                    UploadConstant::ERRORS => [
                        "Faculty ID does not exist."
                    ]
                ];
                continue;
            }

            $personCourseFaculty = $orgCourseFacultyRepository->findOneBy(['course' => $course, 'person' => $person]);
            if (! $personCourseFaculty && $booleanRemove == 'remove') {
                $errors[$id][] = [
                    'name' => self::FACULTYID,
                    'value' => '',
                    'errors' => [
                        $person->getLastname()." ".$person->getFirstname()." is not enrolled in this course."
                    ]
                ];
                continue;
            }

            $personCourseStudent = $orgCourseStudentRepository->findOneBy(['organization' => $orgId, 'course' => $course, 'person' => $person]);
            if ($personCourseStudent) {
                $errors[$id][] = [
                    'name' => 'FacultyId',
                    'value' => '',
                    'errors' => [
                        "ID " . $data[strtolower(self::FACULTYID)] . " is already in course as a student. Cannot add as faculty."
                    ]
                ];
                continue;
            }

            $permissionSet = $data[strtolower(self::PERMISSIONSET)];
            $orgPermissionset = $orgPermissionsetRepository->findOneBy([
                'permissionsetName' => $data[strtolower(self::PERMISSIONSET)],
                UploadConstant::ORGN => $organization
            ]);

            if (!$orgPermissionset && $booleanRemove != 'remove') {
                $errorMsg = (trim($permissionSet)) ? "{$permissionSet} is not valid." : " is a required field.";
                $errors[$id][] = [
                    'name' => 'PermissionSet',
                    UploadConstant::VALUE => '',
                    'errors' => [
                        $errorMsg
                    ]
                ];
                continue;
            }


            $result = $orgCourseRepository->detectCourseEntityDeleted($orgId, $course->getId(), $person->getId(), 'Faculty');

            if (is_array($result) && isset($result[0]['CourseFacultyId']) && $result[0]['CourseFacultyId']>0) {

                if( $booleanRemove=='remove' )
                {
                    try{
                        $courseService->deleteFacultyFromCourse($course->getId(),$person->getId(),false,false);
                    }catch (\Exception $e){
                        $errors[$id][] = [
                        'name' => self::FACULTYID,
                        'value' => '',
                        'errors' => [
                        $person->getLastname()." ".$person->getFirstname()." is not enrolled in this course."
                            ]
                            ];
                        continue;
                    }
                    $deletedFacultyCourseRows++;
                } else {

                    if(!$result[0]['deleted_at'])
                    {
                        $orgCourseFaculty = $this->saveCourseFaculty($organization, $person, $course, $orgPermissionset);
                        $unchangedFacultyCourseRows++;
                    } else {
                        $orgCourseRepository->activateEntityCourse($orgId, $course->getId(), $person->getId(),'faculty');
                        $updatedFacultyCourseRows++;
                    }

                }

            } else {
                $orgCourseFaculty = $this->saveCourseFaculty($organization, $person, $course, $orgPermissionset);
                $createdFacultyCourseRows ++;
            }
            $validRows ++;

        }

        // update the valid, created, updated, deleted, and unchanged rows
        $uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $uploadFileLogService->updateErrorRowCount($uploadId, count($errors));
        $uploadFileLogService->updateCreatedRowCount($uploadId, $createdFacultyCourseRows);
        $uploadFileLogService->updateUpdatedRowCount($uploadId, $updatedFacultyCourseRows + $deletedFacultyCourseRows + $unchangedFacultyCourseRows);

        $orgCourseFacultyRepository->flush();
        $orgCourseFacultyRepository->clear();
        $jobs = $cache->fetch("organization.{$orgId}.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("organization.{$orgId}.upload.{$uploadId}.jobs", $jobs);
        $cache->save("organization:{$orgId}:upload:{$uploadId}:job:{$jobNumber}:errors", $errors);
        return $errors;
    }

    private function saveCourseFaculty($organization, $person, $course, $orgPermissionset)
    {
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $orgCourseFacultyRepository = $repositoryResolver->getRepository('SynapseAcademicBundle:OrgCourseFaculty');
        $orgCourseFacultyEntity =$orgCourseFacultyRepository->findOneBy(array(UploadConstant::ORGN => $organization, 'person' => $person, 'course' => $course));
        $orgCourseFaculty = (!$orgCourseFacultyEntity) ? new OrgCourseFaculty() : $orgCourseFacultyEntity;
        $orgCourseFaculty->setOrganization($organization);
        $orgCourseFaculty->setPerson($person);
        $orgCourseFaculty->setCourse($course);
        $orgCourseFaculty->setOrgPermissionset($orgPermissionset);
        $orgCourseFaculty = (!$orgCourseFacultyEntity) ? $orgCourseFacultyRepository->persist($orgCourseFaculty) : $orgCourseFacultyRepository->update($orgCourseFaculty);

        return   $orgCourseFaculty;
    }

}
