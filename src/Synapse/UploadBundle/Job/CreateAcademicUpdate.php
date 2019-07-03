<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\AcademicBundle\Entity\OrgCourses;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\AcademicUpdateBundle\Entity\AcademicRecord;
use Synapse\AcademicUpdateBundle\Entity\AcademicUpdate;
use Synapse\AcademicUpdateBundle\Repository\AcademicRecordRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRepository;
use Synapse\AcademicUpdateBundle\Repository\AcademicUpdateRequestRepository;
use Synapse\AcademicUpdateBundle\Service\Impl\AcademicUpdateCreateService;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\UploadBundle\Service\Impl\AcademicUpdateUploadValidatorService;
use Synapse\UploadBundle\Service\Impl\UploadFileLogService;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

class CreateAcademicUpdate extends ContainerAwareJob
{
    // Constants

    const STUDENTID = 'StudentId';

    // variables

    /**
     * @var int
     */
    private $absences;

    /**
     * @var string
     */
    private $comments;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @var int
     */
    private $failureRisk;

    /**
     * @var int
     */
    private $finalGrade;

    /**
     * @var int
     */
    private $inProgressGrade;

    /**
     * @var int
     */
    private $sendToStudent;

    // Services

    /**
     * @var AcademicUpdateCreateService
     */
    private $academicUpdateCreateService;

    /**
     * @var AcademicUpdateUploadValidatorService
     */
    private $academicUpdateUploadValidatorService;

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationsService;

    /**
     * @var UploadFileLogService
     */
    private $uploadFileLogService;

    // Repositories

    /**
     * @var AcademicRecordRepository
     */
    private $academicRecordRepository;

    /**
     * @var AcademicUpdateRepository
     */
    private $academicUpdateRepository;

    /**
     * @var AcademicUpdateRequestRepository
     */
    private $academicUpdateRequestRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    public function run($args)
    {
        $creates = $args['creates'];
        $jobNumber = $args['jobNumber'];
        $uploadId = $args['uploadId'];
        $orgId = $args['orgId'];
        $userId = $args['userId'];
        $serverArray = $args['serverArray'];

        $this->errors = [];
        $validRows = 0;
        $updatedRows = 0;
        $requiredFields = [
            'UniqueCourseSectionId',
            'StudentId'
        ];

        // Scaffolding
        $repositoryResolver = $this->getContainer()->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $cache = $this->getContainer()->get(SynapseConstant::REDIS_CLASS_KEY);

        // Services
        $this->academicUpdateCreateService = $this->getContainer()->get(AcademicUpdateCreateService::SERVICE_KEY);
        $this->academicUpdateUploadValidatorService = $this->getContainer()->get(AcademicUpdateUploadValidatorService::SERVICE_KEY);
        $this->academicYearService = $this->getContainer()->get(AcademicYearService::SERVICE_KEY);
        $this->alertNotificationsService = $this->getContainer()->get(AlertNotificationsService::SERVICE_KEY);
        $this->uploadFileLogService = $this->getContainer()->get(UploadFileLogService::SERVICE_KEY);

        // Repositories
        $this->academicRecordRepository = $repositoryResolver->getRepository(AcademicRecordRepository::REPOSITORY_KEY);
        $this->academicUpdateRepository = $repositoryResolver->getRepository(AcademicUpdateRepository::REPOSITORY_KEY);
        $this->academicUpdateRequestRepository = $repositoryResolver->getRepository(AcademicUpdateRequestRepository::REPOSITORY_KEY);
        $this->organizationRepository = $repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $repositoryResolver->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->personRepository = $repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);

        $personCreated = $this->personRepository->find($userId);
        $currentAcademicYear = $this->academicYearService->getCurrentOrgAcademicYearId($orgId);

        $notifiedStudentsArray = [];
        foreach ($creates as $id => $data) {
            // Added this for the valid row counter, this will be set to
            // true after a persist/flush of the academic_update table
            $isThisAValidRow = false;
            $requiredMissing = false;
            if (is_null($personCreated)) {
                $this->errors[$id][] = [
                    'name' => 'ExternalId',
                    'value' => '',
                    'errors' => [
                        "User could not be found."
                    ]
                ];
                continue;
            }
            foreach ($requiredFields as $field) {
                if (!array_key_exists(strtolower($field), $data) || empty($data[strtolower($field)])) {
                    $this->errors[$id][] = [
                        'name' => $field,
                        'value' => '',
                        'errors' => [
                            "{$field} is a required field"
                        ]
                    ];
                    $requiredMissing = true;
                }
            }

            if ($requiredMissing) {
                continue;
            }

            $courseObject = $data['uniquecoursesectionid'];
            $personStudentId = $data['studentid'];

            $organization = $this->academicUpdateUploadValidatorService->validateContents('Organization', $orgId, array(
                'class' => 'SynapseCoreBundle:Organization',
                'keys' => array(
                    'id' => $orgId
                )
            ));

            if ($organization) {
                $personStudent = $this->academicUpdateUploadValidatorService->validateContents('StudentId', $personStudentId, array(
                    'class' => 'SynapseCoreBundle:Person',
                    'keys' => array(
                        'organization' => $organization,
                        'externalId' => $personStudentId
                    )
                ), "Student could not be found.");
            } else {
                $personStudent = false;
            }


            $courseObject = $this->academicUpdateUploadValidatorService->validateContents('UniqueCourseSectionId', $courseObject, array(
                'class' => 'SynapseAcademicBundle:OrgCourses',
                'keys' => array(
                    'organization' => $organization,
                    'externalId' => $courseObject
                )
            ), 'UniqueCourseSectionId does not exist');
            $courseStudentObject = null;
            if ($courseObject && $personStudent) {
                $courseStudentObject = $this->academicUpdateUploadValidatorService->validateContents('UniqueCourseSectionId', $courseObject, array(
                    'class' => 'SynapseAcademicBundle:OrgCourseStudent',
                    'keys' => [
                        'organization' => $organization,
                        'course' => $courseObject,
                        'person' => $personStudent
                    ]
                ), "Not a valid UniqueCourseSectionId / ExternalId combination");
            }

            // Check the length of the Comments to see if it is longer than limit for the comment size
            $comments = $data['comments'];

            if (strlen($comments) > 300) {
                $this->comments  = null;

                $this->academicUpdateUploadValidatorService->setErrors([
                    'name' => 'Comments',
                    'value' => '',
                    'errors' => [
                        "field has too many characters: Field cannot exceed more than 300 characters."
                    ]]);
            } else if ($comments === '') {
                $this->comments  = null;
            } else {
                $this->comments = $comments;
            }
            $this->failureRisk = $this->academicUpdateUploadValidatorService->validateAU('FailureRisk', $data[strtolower('FailureRisk')]);
            $this->inProgressGrade = $this->academicUpdateUploadValidatorService->validateAU('InProgressGrade', $data[strtolower('InProgressGrade')]);
            $this->finalGrade = $this->academicUpdateUploadValidatorService->validateAU('FinalGrade', $data[strtolower('FinalGrade')]);
            $this->absences = $this->academicUpdateUploadValidatorService->validateAU('Absences', $data[strtolower('Absences')]);
            $this->sendToStudent = $this->setSentToStudent($data, $id, $orgId);
            $previousAcademicUpdate = $this->academicUpdateRepository->findOneBy(
                [
                    'org' => $organization,
                    'orgCourses' => $courseObject,
                    'personStudent' => $personStudent
                ],
                null,
                ['id' => 'desc']
            );

            if ($previousAcademicUpdate) {
                $isDuplicate = $this->academicUpdateUploadValidatorService->isCurrentAcademicUpdateDuplicate($previousAcademicUpdate, $organization,
                    $this->failureRisk, $this->inProgressGrade, $this->comments,
                    $this->finalGrade, $this->absences, $this->sendToStudent);
                if ($isDuplicate) {
                    $this->setUploadErrorsAndClearAcademicValidatorServicesErrors($id);
                    $updatedRows++;
                    continue;
                }
            }

            $academicUpdateRequest = $this->academicUpdateRequestRepository->findOneBy([
                'org' => $organization,
                'person' => $personStudent
            ]);

            $academicUpdateRequest = (!$academicUpdateRequest) ? $academicUpdateRequest : null;
            $orgCurrentUtcDate = new \DateTime();


            if ($courseStudentObject) {
                //Find An existing Academic Update
                $academicUpdates = $this->academicUpdateRequestRepository->getAcademicUpdatesInOpenRequestsForStudent($courseObject->getId(), $orgId, $personStudent->getId(), $orgCurrentUtcDate->format(SynapseConstant::DEFAULT_DATETIME_FORMAT));
                $academicUpdateIds = array_column($academicUpdates, 'academic_update_id');
                $academicUpdateEntities = $this->academicUpdateRepository->findBy([
                    'id' => $academicUpdateIds
                ]);

                if (!$academicUpdateEntities || !($this->canAcademicUpdateFulfillARequest())) {
                    $academicUpdateEntity = new AcademicUpdate();
                    $academicUpdateEntity->setAcademicUpdateRequest($academicUpdateRequest);
                    $academicUpdateEntity->setOrg($organization);
                    $academicUpdateEntity->setOrgCourses($courseObject);
                    $academicUpdateEntity->setPersonStudent($personStudent);

                    $academicUpdateObject = $this->setAcademicUpdateEntity($academicUpdateEntity, $data, $personCreated, $orgCurrentUtcDate, $id, $orgId);
                    // Ignore the Academic Update if there is an error in the row
                    if (!empty($this->academicUpdateUploadValidatorService->getErrors()) || is_null($academicUpdateObject)) {
                        $this->setUploadErrorsAndClearAcademicValidatorServicesErrors($id);
                        continue;
                    }
                    $this->academicUpdateRepository->persist($academicUpdateObject, false);
                    $this->academicUpdateRepository->flush();

                    // uploaded rows are only valid if the academic update has been created
                    $isThisAValidRow = true;

                    // creates/updates the academic record record
                    $this->setAndPersistAcademicRecord($personStudent, $courseObject, $orgCurrentUtcDate);

                    // After creating new academic update send alert notification and email notification to student
                    $studentIds = [$personStudent->getId()];
                    $doesStudentIdListContainNonParticipants = $this->orgPersonStudentYearRepository->doesStudentIdListContainNonParticipants($studentIds, $currentAcademicYear);

                    if ($doesStudentIdListContainNonParticipants && $this->sendToStudent) {
                        $this->academicUpdateUploadValidatorService->setErrors([
                            'name' => $data[strtolower(UploadConstant::STUDENTID)],
                            'value' => '',
                            'errors' => [
                                'This student is not a current participant in Mapworks. Your academic update was submitted but an email/notification was not sent to the student.'
                            ]
                        ]);
                    } else if ($this->sendToStudent) {
                        $this->academicUpdateCreateService->checkEmailSendToStudent($this->sendToStudent, $studentIds,  $organization);
                        $this->alertNotificationsService->createNotification('Academic_Update', 'You have received an academic update for one or more of your courses click here to review your update.', $personStudent, null, null, null, null, $academicUpdateObject);
                    }

                } else {
                    foreach ($academicUpdateEntities as $academicUpdateEntity) {

                        $academicUpdateObject = $this->setAcademicUpdateEntity($academicUpdateEntity, $data, $personCreated, $orgCurrentUtcDate, $id, $orgId);

                        // Ignore the Academic Update if there is an error in the row
                        if (!empty($this->academicUpdateUploadValidatorService->getErrors()) || is_null($academicUpdateObject)) {
                            $this->setUploadErrorsAndClearAcademicValidatorServicesErrors($id);
                            continue;
                        }

                        $studentIds = [$personStudent->getId()];
                        $doesStudentIdListContainNonParticipants = $this->orgPersonStudentYearRepository->doesStudentIdListContainNonParticipants($studentIds, $currentAcademicYear);

                        $academicUpdateRequestObject = $academicUpdateObject->getAcademicUpdateRequest();
                        $this->academicUpdateRepository->flush();

                        // uploaded rows are only valid if the academic update has been created
                        $isThisAValidRow = true;

                        // creates/updates the academic record record
                        $this->setAndPersistAcademicRecord($personStudent, $courseObject, $orgCurrentUtcDate);

                        if ($academicUpdateRequestObject) {
                            if ($doesStudentIdListContainNonParticipants && $this->sendToStudent) {
                                $this->academicUpdateUploadValidatorService->setErrors([
                                    'name' => $data[strtolower(UploadConstant::STUDENTID)],
                                    'value' => '',
                                    'errors' => [
                                        'This student is not a current participant in Mapworks. Your academic update was submitted but an email/notification was not sent to the student.'
                                    ]
                                ]);
                                $this->academicUpdateCreateService->checkToCloseAcademicUpdateRequest(false, $studentIds, $academicUpdateRequestObject, $orgId);
                            } else {
                                $this->academicUpdateCreateService->checkToCloseAcademicUpdateRequest($this->sendToStudent, $studentIds, $academicUpdateRequestObject, $orgId);
                            }
                        } else {
                            if ($doesStudentIdListContainNonParticipants && $this->sendToStudent) {
                                $this->academicUpdateUploadValidatorService->setErrors([
                                    'name' => $data[strtolower(UploadConstant::STUDENTID)],
                                    'value' => '',
                                    'errors' => [
                                        'This student is not a current participant in Mapworks. Your academic update was submitted but an email/notification was not sent to the student.'
                                    ]
                                ]);
                            } else if ($this->sendToStudent) {
                                // if this is an adhoc academic updates but no entry in the academic update table, then skip straight to send to student
                                $this->academicUpdateCreateService->checkEmailSendToStudent($this->sendToStudent, $studentIds, $organization);
                            }
                        }

                        if (!in_array($personStudent->getId(), $notifiedStudentsArray) && !$doesStudentIdListContainNonParticipants) {
                            $this->alertNotificationsService->createNotification('Academic_Update', 'You have received an academic update for one or more of your courses click here to review your update.', $personStudent, null, null, null, null, $academicUpdateObject);
                            $notifiedStudentsArray[] = $personStudent->getId();
                        }
                    }
                }
            }

            // set errors in case nothing else caught it earlier
            $this->setUploadErrorsAndClearAcademicValidatorServicesErrors($id);
            if ($isThisAValidRow) {
                $validRows++;
            }
        }

        // All AcademicUpdated Processed here will be created if they are valid
        $this->uploadFileLogService->updateValidRowCount($uploadId, $validRows);
        $this->uploadFileLogService->updateCreatedRowCount($uploadId, $validRows);
        $this->uploadFileLogService->updateUpdatedRowCount($uploadId, $updatedRows);
        $this->uploadFileLogService->updateErrorRowCount($uploadId, count($this->errors));

        $this->academicUpdateRepository->flush();
        $this->academicUpdateRepository->clear();


        $jobs = $cache->fetch("organization.{$orgId}.upload.{$uploadId}.jobs");
        if ($jobs) {
            unset($jobs[array_search($jobNumber, $jobs)]);
        }
        $cache->save("organization.{$orgId}.upload.{$uploadId}.jobs", $jobs);
        $cache->save("organization:{$orgId}:upload:{$uploadId}:job:{$jobNumber}:errors", $this->errors);
        return $this->errors;
    }

    protected function isObjectExist($object, $message, $key)
    {
        if (!isset($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    /**
     * This function will set the errors array in the upload and clear the
     * academic update validator service's error tracker.
     *
     * @param $id => the row for which errors are being set
     * @return mixed => returns the updated array of errors
     */
    private function setUploadErrorsAndClearAcademicValidatorServicesErrors($id)
    {
        $errorsTrack = $this->academicUpdateUploadValidatorService->getErrors();
        if (sizeof($errorsTrack) > 0) {
            $this->errors[$id] = $errorsTrack;
            $this->academicUpdateUploadValidatorService->clearErrors();
        }
    }

    /**
     * This function will validate and set the Academic Update
     * that has been uploaded
     *
     * @param AcademicUpdate $academicUpdateEntity
     * @param array $data
     * @param Person $personCreated
     * @param \DateTime $orgCurrentUtcDate
     * @param int $id
     * @param $organizationId
     * @return null
     */
    private function setAcademicUpdateEntity($academicUpdateEntity, $data, $personCreated, $orgCurrentUtcDate, $id, $organizationId)
    {
        // If everything is null, there are no errors and there are no comments; then the person uploaded an empty row
        if (is_null($this->failureRisk) && is_null($this->inProgressGrade) && is_null($this->finalGrade) && is_null($this->absences) && trim($this->comments) == null && empty($this->academicUpdateUploadValidatorService->getErrors())) {
            $this->errors[$id][] = [
                'name' => $data[strtolower(UploadConstant::STUDENTID)],
                'value' => '',
                'errors' => [
                    'Cannot have FailureRisk, InProgressGrade, FinalGrade, Comments, and Absences all blank; Please fill in at least one of the columns to upload an academic update.'
                ]
            ];
            return null;
        }

        $academicUpdateEntity->setFailureRiskLevel($this->failureRisk);
        $academicUpdateEntity->setGrade($this->inProgressGrade);
        $academicUpdateEntity->setFinalGrade($this->finalGrade);
        $academicUpdateEntity->setAbsence($this->absences);
        $academicUpdateEntity->setComment($this->comments);
        $academicUpdateEntity->setSendToStudent($this->sendToStudent);
        $academicUpdateEntity->setUpdateDate($orgCurrentUtcDate);
        $academicUpdateEntity->setIsUpload(true);
        $academicUpdateEntity->setStatus('closed');
        $academicUpdateEntity->setPersonFacultyResponded($personCreated);

        return $academicUpdateEntity;
    }

    /**
     * performs the logic checks and sets the sentToStudent
     *
     * @param array $data
     * @param int $id
     * @param int $organizationId
     * @return bool|int - returns 0 or 1
     */
    public function setSentToStudent($data, $id, $organizationId)
    {

        // I need to make sure that the organization has the ability to
        // send academic updates to the student via uploads
        $organization = $this->organizationRepository->find($organizationId);
        $canThisUniversitySendAcademicUpdatesToStudents = $organization->getSendToStudent();

        // this makes sure that the organization can send information to the student
        if ($canThisUniversitySendAcademicUpdatesToStudents == 1) {

            return $this->academicUpdateUploadValidatorService->validateAU('SentToStudent', $data[strtolower('SentToStudent')]);

            // throw an error only if the upload has something filled in the
            // sentToStudent column
        } elseif (isset($data[strtolower('SentToStudent')])) {

            // This if statement is here because if there is an error
            // that has been thrown in the validator service, the normal
            // way to throw the error will be deleted. But having it always
            // done this way will stop it from uploading all the way as this
            // error does not stop the upload from occurring
            if (sizeof($this->academicUpdateUploadValidatorService->getErrors()) > 0) {
                $this->academicUpdateUploadValidatorService->setErrors([
                    'name' => 'SentToStudent',
                    'value' => '',
                    'errors' => [
                        'Your organization has disabled \'SentToStudent\'. Anything placed into this column was ignored.',
                    ]
                ]);
            } else {
                $this->errors[$id][] = [
                    'name' => 'SentToStudent',
                    'value' => '',
                    'errors' => [
                        'Your organization has disabled \'SentToStudent\'. Anything placed into this column was ignored.',
                    ]
                ];
            }
        }

        return 0;
    }

    /**
     * checks to see if an academic update can't fulfill a request, if an academic update only contains
     * $this->finalGrade, then it cannot fulfill an academic update request. We can skip
     *
     * @return bool
     */
    private function canAcademicUpdateFulfillARequest()
    {
        if ((is_null($this->failureRisk) && is_null($this->inProgressGrade) && is_null($this->absences) && trim($this->comments) == null) && !is_null($this->finalGrade)) {
            return false;
        }
        return true;
    }

    /**
     * Takes the values that were set in the Academic Update and
     * copy the non-null values over to the Academic Record
     *
     * @param AcademicRecord $academicRecord
     * @param \DateTime $orgCurrentUtcDate
     * @return AcademicRecord
     */
    private function generateAcademicRecord($academicRecord, $orgCurrentUtcDate)
    {
        if (!is_null($this->failureRisk)) {
            $academicRecord->setFailureRiskLevel($this->failureRisk);
            $academicRecord->setFailureRiskLevelUpdateDate($orgCurrentUtcDate);
            $academicRecord->setUpdateDate($orgCurrentUtcDate);
        }

        if (!is_null($this->inProgressGrade)) {
            $academicRecord->setInProgressGrade($this->inProgressGrade);
            $academicRecord->setInProgressGradeUpdateDate($orgCurrentUtcDate);
            $academicRecord->setUpdateDate($orgCurrentUtcDate);
        }

        if (!is_null($this->finalGrade)) {
            $academicRecord->setFinalGrade($this->finalGrade);
            $academicRecord->setFinalGradeUpdateDate($orgCurrentUtcDate);
            $academicRecord->setUpdateDate($orgCurrentUtcDate);
        }

        if (!is_null($this->absences)) {
            $academicRecord->setAbsence($this->absences);
            $academicRecord->setAbsenceUpdateDate($orgCurrentUtcDate);
            $academicRecord->setUpdateDate($orgCurrentUtcDate);
        }

        if (!is_null($this->comments)) {
            $academicRecord->setComment($this->comments);
            $academicRecord->setCommentUpdateDate($orgCurrentUtcDate);
            $academicRecord->setUpdateDate($orgCurrentUtcDate);
        }

        return $academicRecord;
    }

    /**
     * This will create a new academic record for a student course pair
     *
     * @param Person $personStudent
     * @param OrgCourses $courseObject
     * @return AcademicRecord
     */
    public function createAcademicRecord($personStudent, $courseObject)
    {
        $academicRecord = new AcademicRecord();
        $academicRecord->setPersonStudent($personStudent);
        $academicRecord->setOrganization($personStudent->getOrganization());
        $academicRecord->setOrgCourses($courseObject);
        return $academicRecord;
    }

    /**
     * Sets and Persists Academic record based off of an academic update.
     *
     * @param Person $personStudentObject
     * @param OrgCourses $courseObject
     * @param $orgCurrentUtcDate
     */
    public function setAndPersistAcademicRecord($personStudentObject, $courseObject, $orgCurrentUtcDate)
    {
        $academicRecord = $this->academicRecordRepository->findOneBy(['personStudent' => $personStudentObject->getId(), 'orgCourses' => $courseObject->getId()]);

        if (!$academicRecord) {
            $academicRecord = $this->createAcademicRecord($personStudentObject, $courseObject);
        }

        $academicRecord = $this->generateAcademicRecord($academicRecord, $orgCurrentUtcDate);
        $this->academicRecordRepository->persist($academicRecord, false);
        $this->academicRecordRepository->flush();
    }
}
