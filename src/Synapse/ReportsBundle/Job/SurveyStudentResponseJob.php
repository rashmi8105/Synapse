<?php
namespace Synapse\ReportsBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Utility\CSVUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\ReportsBundle\Util\Constants\ReportsConstants;
use Synapse\CoreBundle\Util\Helper;
use SplFileObject;
use Synapse\SurveyBundle\Repository\SurveyResponseRepository;

class SurveyStudentResponseJob extends ReportsJob
{
    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var SurveyResponseRepository
     */
    private $surveyResponseRepository;

    /**
     * @var CSVUtilityService
     */
    private $csvUtilityService;

    public function run($args)
    {
        $this->repositoryResolver = $this->getContainer()->get(SynapseConstant::REPOSITORY_RESOLVER_CLASS_KEY);
        $this->container = $this->getContainer();
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->surveyResponseRepository = $this->repositoryResolver->getRepository(SurveyResponseRepository::REPOSITORY_KEY);
        $this->csvUtilityService = $this->container->get(CSVUtilityService::SERVICE_KEY);

        $ignoreHeaderFields = array(
            'AuthUsername',
            'Title',
            'RecordType',
            'StudentPhoto',
            'IsActive',
            'ReceiveSurvey'
        );

        $currentDateTime = $args['currentDateTime'];
        $loggedInPerson = $args['personId'];
        $orgId = $args['orgId'];
        $cohortId = $args['cohortId'];
        $headerColumns = $args['headerColumns'];
        $cohortPerson = $args['cohortPerson'];
        $surveyList = $args['surveyList'];
        $academicYearId = $args['academicYearId'];

        foreach ($headerColumns as $key => $value) {
            unset($headerColumns[$key]);
            if (!in_array($value, $ignoreHeaderFields)) {
                $headerColumns[$value] = $value;
            }
        }
        
        if (! in_array('RiskColor', $headerColumns)) {
            $headerColumns['RiskColor'] = "RiskColor";
        }

        $completeFilePath = "roaster_uploads/{$orgId}-{$loggedInPerson}-{$currentDateTime}-survey-response.csv";
        $csvArray = [];
        
        $people = $this->personRepository->getDumpByOrganizationByPersonIds($orgId, $cohortPerson);
        
        $ignore = [
            'id',
            'createdAt',
            'receiveSurvey',
            'authUsername',
            'title',
            'recordType',
            'createdBy',
            'modifiedAt',
            'modifiedBy',
            'deletedBy',
            'deletedAt',
            'activationToken',
            'confidentialityStmtAcceptDate',
            'tokenExpiryDate',
            'person',
            'username',
            'password',
            'welcomeEmailSentDate',
            'riskLevel',
            'riskUpdateDate',
            'intentToLeave',
            'intentToLeaveUpdateDate',
            'riskModel',
            'lastContactDate',
            'lastActivity',
            'Dateofbirth',
            'dateofbirth',
            'officePhone'
        ];

        foreach ($people as $personStudent) {
            $photoURL = "";
            $status = "";
            $surveyCohort = "";
            $primaryConnect = "";

            if (isset($personStudent['photoUrl'])) {
                $photoURL = $personStudent['photoUrl'];
            }
            //Get student cohorts form OrgPersonStudentCohort
            $studentCohortObj = $this->getPersonYearCohort($orgId, $personStudent['person']['id'], $academicYearId);
            
            if(!empty($studentCohortObj)){
                
                $surveyCohort = $studentCohortObj[0]['cohort'];
                $cohortYearId = $studentCohortObj[0]['year_id'];
            }else{
                $surveyCohort = "";
                $cohortYearId = "";
            }
                        
            $primaryConnect = "";
            if ($personStudent['personIdPrimaryConnect']) {
                $primaryConnect = $personStudent['personIdPrimaryConnect']['externalId'];
            }
            
            $status = $personStudent['status'];
            $personStudent = $personStudent[ReportsConstants::PERSON];
            $personData = array_fill_keys($headerColumns, '');
            
            $contacts = $personStudent['contacts'];
            if (count($contacts) > 0) {
                end($contacts);
                $key = key($contacts);
                $contact = $contacts[$key];
            } else {
                $contact = [];
            }
            unset($personStudent['contacts']);
            
            $this->getPersonData($personStudent, $ignore, $personData);           
            
            $riskColor = ($personStudent['riskLevel']['riskText']) ? $personStudent['riskLevel']['riskText'] : 'gray';
            unset($personStudent['riskLevel']);
            $personData['RiskColor'] = $riskColor;
            $personData[ReportsConstants::SURVEYCOHORT] = $surveyCohort;
            $personData['YearID'] = $cohortYearId;
            $personData[ReportsConstants::PRIMARY_CONNECT] = $primaryConnect;
            
            $this->getContactData($contact, $ignore, $personData);
            $this->getRiskVal($personStudent, $personData);
            $this->getEbiMetadataData($personStudent, $ignore, $personData);            
            $this->getOrgMetadataData($personStudent, $ignore, $personData);
            $this->getReceiveSurvey($surveyList, $personStudent['id'], $orgId, $personData);
            $this->getStudentSurveyResponse($surveyList, $personStudent['id'], $orgId, $personData, $academicYearId);
            $this->getStudentOrgQuestionSurveyResponse($surveyList, $personStudent['id'], $orgId, $personData, $academicYearId, $cohortId);
            $this->getStudentFactorResponseAndCompletionDate($surveyList, $personStudent['id'], $orgId, $personData);
            $csvArray[] = $personData;

            unset($personData);
        }

        unset($people);
        $personObj = $this->personRepository->find($loggedInPerson);

        $this->csvUtilityService->generateCSV('data://roaster_uploads/', "{$orgId}-{$loggedInPerson}-{$currentDateTime}-survey-response.csv", $csvArray, $headerColumns);

        $alertService = $this->getContainer()->get(ReportsConstants::ALERT_SERVICE);
        $alertService->createNotification(ReportsConstants::SURVEY_DOWNLOAD, ReportsConstants::SURVEY_DOWNLOAD_DESCRIPTION, $personObj, NULL, NULL, NULL, $completeFilePath, NULL, NULL, NULL, TRUE);
    }

    /**
     * Setting a person data into $personData pointer
     * @param array $person
     * @param array $ignore
     * @param pointer $personData
     */
    private function getPersonData($person, $ignore, &$personData)
    {
        foreach ($person as $key => $value) {
            if (! in_array($key, $ignore)) {
                $key = ucfirst($key);
                if (is_a($value, ReportsConstants::DATE_TIME)) {
                    $personData[$key] = $value->format(ReportsConstants::DATETIME);
                } else {
                    $personData[$key] = @iconv(ReportsConstants::INPUT_CHAR_SET, ReportsConstants::OUTPUT_CHAR_SET, $value);
                }
            }
        }
        unset($person);
        unset($ignore);
    }

    /**
     * Setting up a $personData pointer with person's contact details
     * @param array $contact
     * @param array $ignore
     * @param pointer $personData
     */
    private function getContactData($contact, $ignore, &$personData)
    {
        foreach ($contact as $key => $value) {
            if (! in_array($key, $ignore)) {
                $key = ucfirst($key);
                $personData[$key] = $value;
            }
        }
        unset($contact);
        unset($ignore);
    }

    /**
     * Setting up $personData pointer with person's risk value.
     * @param $person
     * @param $personData
     */
    public function getRiskVal($person, &$personData)
    {
        $this->repositoryResolver = $this->getContainer()->get(ReportsConstants::REPOSITORY_RESOLVER);
        $this->personRepository = $this->repositoryResolver->getRepository(ReportsConstants::PERSON_REPO);
        $risk = $this->personRepository->getStudentRisk($person['id']);
        if (count($risk) > 0) {
            $personData['RiskGroupID'] = $risk[0]['RiskGroupID'];
        } else {
            $personData['RiskGroupID'] = "";
        }
        unset($risk);
    }

    /**
     * Setting up $personData pointer with EBI Metadata
     * @param array $person
     * @param array $ignore
     * @param pointer $personData
     */
    private function getEbiMetadataData($person, $ignore, &$personData)
    {
        $this->repositoryResolver = $this->getContainer()->get(ReportsConstants::REPOSITORY_RESOLVER);
        $this->personEbiMetadataRepository = $this->repositoryResolver->getRepository(ReportsConstants::PERSON_EBI_METADATA_REPO);
        $metadata = $this->personEbiMetadataRepository->findByPerson($person['id']);
        
        foreach ($metadata as $metadataItem) {
            try {
                $key = $metadataItem->getEbiMetadata()->getKey();
                if (! in_array($key, $ignore)) {
                    if (is_a($metadataItem->getMetadataValue(), ReportsConstants::DATE_TIME)) {
                        $personData[$key] = $metadataItem->getMetadataValue()->format(ReportsConstants::DATETIME);
                    } else {
                        $personData[$key] = $metadataItem->getMetadataValue();
                    }
                }
            } catch (\Exception $e) {}
        }
        unset($metadata);
        unset($metadataItem);
    }

    /**
     * Setting up $personData pointer with ORG Metatdata
     * @param array $person
     * @param array $ignore
     * @param pointer $personData
     */
    private function getOrgMetadataData($person, $ignore, &$personData)
    {
        $this->repositoryResolver = $this->getContainer()->get(ReportsConstants::REPOSITORY_RESOLVER);
        $this->personOrgMetadataRepository = $this->repositoryResolver->getRepository(ReportsConstants::PERSON_ORG_METADATA_REPO);
        $metadata = $this->personOrgMetadataRepository->findByPerson($person['id']);
        foreach ($metadata as $metadataItem) {
            try {
                $key = $metadataItem->getOrgMetadata()->getMetaKey();
                
                if (! in_array($key, $ignore)) {
                    if (is_a($metadataItem->getMetadataValue(), ReportsConstants::DATE_TIME)) {
                        $personData[$key] = $metadataItem->getMetadataValue()->format(ReportsConstants::DATETIME);
                    } else {
                        $personData[$key] = $metadataItem->getMetadataValue();
                    }
                }
            } catch (\Exception $e) {}
        }
        unset($metadata);
        unset($metadataItem);
    }

    /**
     * To find the student response for each survey.
     * @param array $surveyList
     * @param array $surveyStudentPerson
     * @param int $orgId
     * @param pointer $personData
     * @param int $academicYearId
     */
    private function getStudentSurveyResponse($surveyList, $surveyStudentPerson, $orgId, &$personData, $academicYearId)
    {
        $this->repositoryResolver = $this->getContainer()->get(ReportsConstants::REPOSITORY_RESOLVER);
        $this->surveyResponseRepository = $this->repositoryResolver->getRepository(ReportsConstants::SURVEY_RESPONSE_REPO);
        $count = 1;
        if ($surveyList) {
            foreach ($surveyList as $surveyId) {
                $studentSurveyResponses = $this->surveyResponseRepository->getSurveyResponse($surveyId, $surveyStudentPerson, $orgId, $academicYearId);
                if ($studentSurveyResponses) {
                    foreach ($studentSurveyResponses as $studentSurveyResp) {
                        $responseType = $studentSurveyResp['responseType'];
                        if ($responseType == 'charmax') {
                            $studentResp = $studentSurveyResp['charmaxValue'];
                        } elseif ($responseType == 'char') {
                            $studentResp = $studentSurveyResp['charValue'];
                        } else {
                            $studentResp = $studentSurveyResp['decimalValue'];
                        }
                        $personData[$count . '-Q' . $studentSurveyResp['survey_ques_no']] = $studentResp;
                    }                    
                }
                unset($studentSurveyResponses);
                $count ++;
            }
        }
    }

    /**
     * To find the ISQ response for each survey
     * @param $surveyList
     * @param $surveyStudentPerson
     * @param $orgId
     * @param $personData
     * @param $academicYearId
     * @param $cohortId
     */
    private function getStudentOrgQuestionSurveyResponse($surveyList, $surveyStudentPerson, $orgId, &$personData, $academicYearId, $cohortId)
    {
        $this->repositoryResolver = $this->getContainer()->get(ReportsConstants::REPOSITORY_RESOLVER);
        $this->orgQuestionRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgQuestion');
        $count = 1;
        if ($surveyList) {
            foreach ($surveyList as $surveyId) {
                $orgQuestionResponses = $this->orgQuestionRepository->getOrgQuestionSurveyResponse($surveyId, $surveyStudentPerson, $orgId, $academicYearId, $cohortId);
                if ($orgQuestionResponses) {
                    foreach ($orgQuestionResponses as $orgQuestionResponse) {
                        $responseType = $orgQuestionResponse['response_type'];
                        if ($orgQuestionResponse['question_type'] == ReportsConstants::KEY_MR) {
                            $studentResp = $orgQuestionResponse['sequence'];                            
                        } else {
                            if ($responseType == 'charmax') {
                                $studentResp = $orgQuestionResponse['char_max_value'];
                            } elseif ($responseType == 'char') {
                                $studentResp = $orgQuestionResponse['char_value'];
                            } else {
                                $studentResp = $orgQuestionResponse['decimal_value'];
                            }
                        }
                        $personData[$count . '-ISQ' . $orgQuestionResponse[ReportsConstants::ORG_QUESTION_ID]] = $studentResp;
                    }
                }
                unset($orgQuestionResponses);
                $count ++;
            }
        }
    }

    /**
     * This will return factor response for each students and completion date for the respective survey
     * @param array $surveyList
     * @param array $surveyStudentPerson
     * @param int $orgId
     * @param pointer $personData
     */
    public function getStudentFactorResponseAndCompletionDate($surveyList, $surveyStudentPerson, $orgId, &$personData)
    {
        $this->repositoryResolver = $this->getContainer()->get(ReportsConstants::REPOSITORY_RESOLVER);
        $this->studentSurveyLinkRepo = $this->repositoryResolver->getRepository(ReportsConstants::ORG_PERSON_STUD_SURVEY_LINK_REPO);
        $this->factorRepository = $this->repositoryResolver->getRepository('SynapseSurveyBundle:Factor');
        $orgService = $this->getContainer()->get(ReportsConstants::ORGANIZATION_SERVICE);
        //TODO:: This is fetching the organization object for each student. This should be moved outside of the per-student loop.
        $organization = $orgService->find($orgId);
        $timezone = $organization->getTimeZone();
        $timezone = $this->repositoryResolver->getRepository(ReportsConstants::METADATA_REPO)->findByListName($timezone);
        if ($timezone) {
            $timezone = $timezone[0]->getListValue();
        }
        $count = 1;
        if ($surveyList) {
            foreach ($surveyList as $surveyId) {
                $surveyIdArray = [$surveyId];
                $surveyFactors = $this->factorRepository->getStudentFactorValues($surveyStudentPerson, $surveyIdArray);
                if ($surveyFactors) {
                    foreach ($surveyFactors as $surveyFactor) {
                        $personData[$count . '-Factor ' . $surveyFactor[ReportsConstants::FACTOR_ID]] = $surveyFactor['mean_value'];
                    }
                }
                $completionDate = $this->studentSurveyLinkRepo->getSurveyCompletionDate($surveyId, $surveyStudentPerson, $orgId);                

                // Include survey completion date
                if ($completionDate[0]['survey_completion_date']) {                     
                    $completionDate = new \DateTime($completionDate[0]['survey_completion_date']);
                    Helper::getOrganizationDate($completionDate, $timezone);
                    $personData[$count . '-SurveyResponseDate'] = $completionDate->format('m/d/y h:i A T');
                }
                unset($surveyFactors);
                $count ++;
            }
        }
    }
    
    /**
     * Function to get the receive_survey value for each students
     * @param array $surveyList
     * @param int $surveyStudentPerson
     * @param int $orgId
     * @param pointer $personData
     */
    public function getReceiveSurvey($surveyList, $surveyStudentPerson, $orgId, &$personData)
    {
        $this->personStudentSurveyRepo = $this->repositoryResolver->getRepository('SynapseSurveyBundle:OrgPersonStudentSurvey');
        $count = 1;
        if ($surveyList) {
            foreach ($surveyList as $surveyId) {
                $surveyStudent = $this->personStudentSurveyRepo->findOneBy([ 'organization' => $orgId,
                                                                            'person' => $surveyStudentPerson,
                                                                            'survey' => $surveyId
                                                                           ]);

                if($surveyStudent){
                    $personData[$count . '-ReceiveSurvey'] = $surveyStudent->getReceiveSurvey();
                    $count ++;
                }
            }
        }
	}


    public function getPersonYearCohort($orgId, $personId, $academicYearId){
        $this->repositoryResolver = $this->getContainer()->get(ReportsConstants::REPOSITORY_RESOLVER);
        $this->personStudentCohort = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPersonStudentCohort');

        $studentCohortObj = $this->personStudentCohort->getPersonYearCohort($orgId, $personId,$academicYearId);
        
        return $studentCohortObj;
    }
}