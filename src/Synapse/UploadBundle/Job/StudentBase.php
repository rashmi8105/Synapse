<?php
namespace Synapse\UploadBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\AcademicBundle\Entity\OrgAcademicYear;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Repository\SurveyRepository;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Entity\OrgPersonStudentRetention;
use Synapse\ReportsBundle\Repository\OrgPersonStudentRetentionRepository;
use Synapse\SurveyBundle\Entity\OrgPersonStudentSurvey;
use Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyRepository;
use Synapse\UploadBundle\Service\Impl\StudentUploadValidatorService;

/*
 *
 *  The abstract class is created to store the common codes between the files CreateStudent and UpdateStudent
 *  the create and update file has quite a bit of code which is common to both, so going forward while if we are
 *  doing any refactoring or improvement we can use this file to store the common codes.
 */

abstract class StudentBase extends ContainerAwareJob
{

    // Services
    /**
     * @var StudentUploadValidatorService
     */
    private $studentUploadValidatorService;

    // Repository
    /**
     * @var OrgPersonStudentRetentionRepository
     */
    private $orgPersonStudentRetentionRepository;

    /**
     * @var OrgPersonStudentSurveyRepository
     */
    private $orgPersonStudentSurveyRepository;

    /**
     * @var SurveyRepository
     */
    private $surveyRepository;

    /**
     *
     * The below method checks for the receive survey status for each of the survey.
     * data and errors are passed by reference
     *
     * @param array $rowData
     * @param array $errors
     * @param int $id
     * @param Organization $organization
     * @param Person $person
     * @param string $type
     */
    public function processReceiveSurvey(&$rowData, &$errors, $id, $organization, $person, $type = "null")
    {
        $this->studentUploadValidatorService = $this->getContainer()->get(StudentUploadValidatorService::SERVICE_KEY);
        $repositoryResolver = $this->getContainer()->get('repository_resolver');
        $this->orgPersonStudentSurveyRepository = $repositoryResolver->getRepository(OrgPersonStudentSurveyRepository::REPOSITORY_KEY);
        $this->surveyRepository =  $repositoryResolver->getRepository(SurveyRepository::REPOSITORY_KEY);

        $receiveSurveyArray = $this->studentUploadValidatorService->validateReceiveSurveys($organization, $rowData);

        // Fetch all survey error with key and value
        $surveyErrorsArray = $this->studentUploadValidatorService->getErrors();

        if (count($surveyErrorsArray) > 0) {
            foreach($surveyErrorsArray as $errorKey => $errorValue) {
                $errors[$id][] = [
                    'name' => ucfirst($errorKey),
                    'value' => '',
                    'errors' => [$errorValue]
                ];
            }
        }

        if (count($receiveSurveyArray) > 0) {

            foreach ($receiveSurveyArray as $surveyExternalId => $receiveSurvey) {

                $survey = $this->surveyRepository->findOneBy([
                    'externalId' => $surveyExternalId
                ]);

                if ($type == "update") {
                    $orgPersonStudentSurvey = $this->orgPersonStudentSurveyRepository->findOneBy([
                            'survey' => $survey->getId(),
                            'organization' => $organization->getId(),
                            'person' => $person->getId()
                        ]
                    );
                } else {
                    $orgPersonStudentSurvey = null;
                }

                if ($orgPersonStudentSurvey) {
                    $orgPersonStudentSurvey->setReceiveSurvey($receiveSurvey);
                    $this->orgPersonStudentSurveyRepository->update($orgPersonStudentSurvey);
                } else {
                    $orgPersonStudentSurvey = new OrgPersonStudentSurvey();
                    $orgPersonStudentSurvey->setOrganization($organization);
                    $orgPersonStudentSurvey->setSurvey($survey);
                    $orgPersonStudentSurvey->setPerson($person);
                    $orgPersonStudentSurvey->setReceiveSurvey($receiveSurvey);
                    $this->orgPersonStudentSurveyRepository->persist($orgPersonStudentSurvey);
                }
            }
            $this->orgPersonStudentSurveyRepository->flush();
        }
    }


    /**
     *  used for saving retention variables values in database
     *
     * @param OrgAcademicYear $orgAcademicYear
     * @param Person $person
     * @param Organization $organization
     * @param integer|null $enrolledBeginningYear
     * @param integer|null $enrolledMidYear
     * @param integer|null $isDegreeCompleted
     */
    public function processRetentionVariables($orgAcademicYear, $person, $organization, $enrolledBeginningYear, $enrolledMidYear, $isDegreeCompleted)
    {
        $container = $this->getContainer();
        $repositoryResolver = $container->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->orgPersonStudentRetentionRepository = $repositoryResolver->getRepository(OrgPersonStudentRetentionRepository::REPOSITORY_KEY);
        $isCreate = false;
        $orgPersonStudentRetentionEntity = $this->orgPersonStudentRetentionRepository->findOneBy(
            [
                'organization' => $organization,
                'person' => $person,
                'orgAcademicYear' => $orgAcademicYear
            ]
        );
        if (!$orgPersonStudentRetentionEntity) {

            $orgPersonStudentRetentionEntity = new OrgPersonStudentRetention();
            $orgPersonStudentRetentionEntity->setPerson($person);
            $orgPersonStudentRetentionEntity->setOrganization($organization);
            $orgPersonStudentRetentionEntity->setOrgAcademicYear($orgAcademicYear);
            $isCreate = true;
        }
        if (!is_null($enrolledBeginningYear)) {
            $orgPersonStudentRetentionEntity->setEnrolledBeginningYear($enrolledBeginningYear);
        }
        if (!is_null($enrolledMidYear)) {
            $orgPersonStudentRetentionEntity->setEnrolledMidyear($enrolledMidYear);
        }
        if (!is_null($isDegreeCompleted)) {
            $orgPersonStudentRetentionEntity->setIsDegreeCompleted($isDegreeCompleted);
        }

        if ($isCreate) {
            $this->orgPersonStudentRetentionRepository->persist($orgPersonStudentRetentionEntity);
        } else {
            $this->orgPersonStudentRetentionRepository->flush();
        }
    }
}