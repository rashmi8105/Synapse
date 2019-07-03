<?php

namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use SoftDeleteable\Fixture\Entity\User;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\OrgQuestion;
use Synapse\CoreBundle\Entity\OrgQuestionResponse;
use Synapse\CoreBundle\Entity\OrgSurveyReportAccessHistory;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentRepository;
use Synapse\CoreBundle\Repository\OrgQuestionResponseRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\SurveyLangRepository;
use Synapse\CoreBundle\Service\Utility\DateUtilityService;
use Synapse\CoreBundle\Util\Constants\SurveyConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RestBundle\Entity\CohortsDto;
use Synapse\RestBundle\Entity\SurveyAccessStatusDto;
use Synapse\RestBundle\Entity\SurveyDto;
use Synapse\RestBundle\Entity\WessSurveyResponseDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\RiskBundle\EntityDto\RiskCalculationInputDto;
use Synapse\RiskBundle\Service\Impl\RiskCalculationService;
use Synapse\SurveyBundle\Entity\SurveyResponse;
use Synapse\SurveyBundle\Entity\WessLink;
use Synapse\SurveyBundle\EntityDto\IsqDataArrayDto;
use Synapse\SurveyBundle\EntityDto\StudentIsqQuesResponseDto;
use Synapse\SurveyBundle\EntityDto\WessLinkDto;
use Synapse\SurveyBundle\EntityDto\WessLinkInsertDto;
use Synapse\SurveyBundle\Repository\WessLinkRepository;

/**
 * @DI\Service("survey_service")
 */
class SurveyService extends AbstractService
{
	
	const SERVICE_KEY = 'survey_service';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

    // Services
    /**
     * @var DateUtilityService
     */
    private $dateUtilityService;

    /**
     * @var ISQPermissionsetService
     */
    private $ISQPermissionsetService;

    /**
     * @var RiskCalculationService
     */
    private $riskCalculationService;

    /**
     * @var RoleService
     */
    private $roleService;

    // Repositories
    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var OrgPersonStudentRepository
     */
    private $orgPersonStudentRepository;

    /**
     * @var OrgQuestionResponseRepository
     */
    private $orgQuestionResponseRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var SurveyLangRepository
     */
    private $surveyLangRepository;

    /**
     * @var WessLinkRepository
     */
    private $wessLinkRepository;
    
    /**
     * SurveyService constructor.
     *
     * @DI\InjectParams({
     *     "repositoryResolver" = @DI\Inject("repository_resolver"),
     *     "logger" = @DI\Inject("logger"),
     *     "container" = @DI\Inject("service_container")
     * })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // Services
        $this->dateUtilityService = $this->container->get(DateUtilityService::SERVICE_KEY);
        $this->ISQPermissionsetService = $this->container->get(ISQPermissionsetService::SERVICE_KEY);
        $this->riskCalculationService = $this->container->get(RiskCalculationService::SERVICE_KEY);
        $this->roleService = $this->container->get(RoleService::SERVICE_KEY);

        // Repositories
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);       
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(OrgPersonStudentRepository::REPOSITORY_KEY);
        $this->orgQuestionResponseRepository  = $this->repositoryResolver->getRepository(OrgQuestionResponseRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
        $this->surveyLangRepository = $this->repositoryResolver->getRepository(SurveyLangRepository::REPOSITORY_KEY);
        $this->wessLinkRepository = $this->repositoryResolver->getRepository(WessLinkRepository::REPOSITORY_KEY);
    }

    /**
     * @return SurveyDto
     */
    public function viewSurvey()
    {
        $survey_response = array(
            SurveyConstant::SURVEY => array(
                SurveyConstant::SURVEY_ID => 100,
                "survey_name" => "Fall Transition Survey",
                SurveyConstant::SURVEY_START_DATE => "09/01/2014",
                SurveyConstant::SURVEY_END_DATE => "11/30/2014",
                "cohorts" => array(
                    0 => array(
                        SurveyConstant::FIELD_STARTDATE => "09/01/2014",
                        SurveyConstant::FIELD_ENDDATE => "09/30/2014",
                        SurveyConstant::COHORT_NAME => "FRESHMAN",
                        SurveyConstant::FIELD_TOTALSTUDENTS => 52,
                        SurveyConstant::FIELD_RESPONDED => 42,
                        SurveyConstant::FIELD_NOTRESPONDED => 10
                    ),
                    1 => array(
                        SurveyConstant::FIELD_STARTDATE => "10/01/2014",
                        SurveyConstant::FIELD_ENDDATE => "11/25/2014",
                        SurveyConstant::COHORT_NAME => "SOPHOMORES",
                        SurveyConstant::FIELD_TOTALSTUDENTS => 30,
                        SurveyConstant::FIELD_RESPONDED => 10,
                        SurveyConstant::FIELD_NOTRESPONDED => 20
                    )
                )
            )
        );
        foreach ($survey_response as $survey_result) {
            $surveyDto = new SurveyDto();
            $surveyDto->setSurveyId($survey_result[SurveyConstant::SURVEY_ID]);
            $surveyDto->setSurveyName($survey_result[SurveyConstant::SURVEY_NAME]);
            $startDate = Date(SurveyConstant::DATE_FORMAT, strtotime($survey_result[SurveyConstant::SURVEY_START_DATE]));
            $surveyDto->setSurveyName($survey_result[SurveyConstant::SURVEY_NAME]);
            $startDate = Date(SurveyConstant::DATE_FORMAT, strtotime($survey_result[SurveyConstant::SURVEY_START_DATE]));
            $startDateTime = new \DateTime($startDate);
            $startDateTime->setTimezone(new \DateTimeZone('UTC'));
            $surveyDto->setSurveyStartDate($startDateTime);
            $endDate = Date(SurveyConstant::DATE_FORMAT, strtotime($survey_result[SurveyConstant::SURVEY_END_DATE]));
            $endDate = Date(SurveyConstant::DATE_FORMAT, strtotime($survey_result[SurveyConstant::SURVEY_END_DATE]));
            $endDateTime = new \DateTime($endDate);
            $endDateTime->setTimezone(new \DateTimeZone('UTC'));
            $surveyDto->setSurveyEndDate($endDateTime);
            
            foreach ($survey_result['cohorts'] as $cohorts) {
                $cohortsDto = new CohortsDto();
                $percentage = round($cohorts[SurveyConstant::FIELD_RESPONDED] / $cohorts[SurveyConstant::FIELD_TOTALSTUDENTS] * 100);
                $surveyStartDate = Date(SurveyConstant::DATE_FORMAT, strtotime($cohorts[SurveyConstant::FIELD_STARTDATE]));
                $percentage = round($cohorts[SurveyConstant::FIELD_RESPONDED] / $cohorts[SurveyConstant::FIELD_TOTALSTUDENTS] * 100);
                
                $surveyStartDate = Date(SurveyConstant::DATE_FORMAT, strtotime($cohorts[SurveyConstant::FIELD_STARTDATE]));
                $surveyStartDateTime = new \DateTime($surveyStartDate);
                $surveyStartDateTime->setTimezone(new \DateTimeZone('UTC'));
                $cohortsDto->setStartDate($surveyStartDateTime);
                $surveyEndDate = Date(SurveyConstant::DATE_FORMAT, strtotime($cohorts[SurveyConstant::FIELD_ENDDATE]));
                $surveyEndDate = Date(SurveyConstant::DATE_FORMAT, strtotime($cohorts[SurveyConstant::FIELD_ENDDATE]));
                $surveyEndDateTime = new \DateTime($surveyEndDate);
                $surveyEndDateTime->setTimezone(new \DateTimeZone('UTC'));
                $cohortsDto->setEndDate($surveyEndDateTime);
                $cohortsDto->setCohortName($cohorts[SurveyConstant::COHORT_NAME]);
                $cohortsDto->setTotalStudents($cohorts[SurveyConstant::FIELD_TOTALSTUDENTS]);
                $cohortsDto->setResponded($cohorts[SurveyConstant::FIELD_RESPONDED]);
                $cohortsDto->setNotResponded($cohorts[SurveyConstant::FIELD_NOTRESPONDED]);
                $cohortsDto->setCohortName($cohorts[SurveyConstant::COHORT_NAME]);
                $cohortsDto->setTotalStudents($cohorts[SurveyConstant::FIELD_TOTALSTUDENTS]);
                $cohortsDto->setResponded($cohorts[SurveyConstant::FIELD_RESPONDED]);
                $cohortsDto->setNotResponded($cohorts[SurveyConstant::FIELD_NOTRESPONDED]);
                $cohortsDto->setPercentage($percentage);
                $cohortData[] = $cohortsDto;
            }
            $surveyDto->setCohorts($cohortData);
        }
        return $surveyDto;
    }

    /**
     * @param $surveyId
     * @param int $langId
     * @return mixed
     */
    public function getSurveyName($surveyId, $langId = 1)
    {
        $this->surveyLangRepoitory = $this->repositoryResolver->getRepository("SynapseCoreBundle:SurveyLang");
        $surveyLangObj = $this->surveyLangRepoitory->findOneBy(array(
            SurveyConstant::SURVEY => $surveyId,
            'languageMaster' => $langId
        ));
        $surveyLangObj = $this->surveyLangRepoitory->findOneBy(array(
            SurveyConstant::SURVEY => $surveyId,
            'languageMaster' => $langId
        ));
        return $surveyLangObj->getName();
    }


    /**
     * Lists students ISQ Questions based on survey
     *
     * @param int $surveyId
     * @param int $studentId
     * @param int $organizationId
     * @param int $loggedInUserId
     * @throws SynapseValidationException
     * @return StudentIsqQuesResponseDto
     */
    public function listStudentISQQuestions($surveyId, $studentId, $organizationId, $loggedInUserId)
    {
        // Validating if student exists
        $this->personRepository->find($studentId, new SynapseValidationException("Person Not Found."));

        $this->orgPersonStudentRepository->findOneBy(['organization' => $organizationId, 'person' => $studentId], new SynapseValidationException("Student Not Found."));

        // Validating if survey exists
        $survey = $this->surveyLangRepository->findOneBy(['survey' => $surveyId], new SynapseValidationException("Survey Not Found."));

        // Fetching student isq questions and response
        $facultyStudentPermissions = $this->orgGroupFacultyRepository->getPermissionsByFacultyStudent($loggedInUserId, $studentId);
        $facultyStudentPermissionIds = array_column($facultyStudentPermissions, 'org_permissionset_id');

        // This will get the person's ISQs within the organization with the faculty's student permissionset id and survey id
        $listStudentISQs = $this->ISQPermissionsetService->getFilteredISQIds($loggedInUserId, $organizationId, $facultyStudentPermissionIds, null, null, false, $surveyId);
        $orgQuestionIds = array_column($listStudentISQs, 'org_question_id');
        $studentQuestions = [];
        if (!empty($orgQuestionIds)) {
            $studentQuestions = $this->orgQuestionResponseRepository->getStudentISQResponses($surveyId, $studentId, $organizationId, $orgQuestionIds);
        }
        // Setting the response
        $studentIsqQuesResponseDto = new StudentIsqQuesResponseDto();
        $studentIsqQuesResponseDto->setSurveyId($surveyId);
        $studentIsqQuesResponseDto->setSurveyName($survey->getName());
        $isqResponse = $this->setStudentIsqResponse($studentQuestions);
        $studentIsqQuesResponseDto->setIsqData($isqResponse);
        return $studentIsqQuesResponseDto;
    }


    /**
     * @param $studentQues
     * @return array
     */
    private function setStudentIsqResponse($studentQues)
    {
        $isqResponse = array();
        foreach ($studentQues as $question) {
            $isqQues = new IsqDataArrayDto();
            $isqQues->setId($question[SurveyConstant::SURVEY_QUE_ID]);
            $isqQues->setName($question[SurveyConstant::QUESTION_TEXT]);
            if ($question[SurveyConstant::RESPONSE_TYPE] == SurveyConstant::DECIMAL) {
                $isqQues->setResponse($question['option_name']);
            } elseif ($question[SurveyConstant::RESPONSE_TYPE] == SurveyConstant::CHAR) {
                $isqQues->setResponse($question['char_value']);
            } elseif ($question[SurveyConstant::RESPONSE_TYPE] == 'charmax') {
                $isqQues->setResponse($question['charmax_value']);
            } else {
                $isqQues->setResponse("");
            }
            $isqResponse[] = $isqQues;
        }
        return $isqResponse;
    }

    /**
     * @param $object
     * @param $message
     * @param $key
     */
    private function isObjectExist($object, $message, $key)
    {
        if (! ($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }



    /**
     * @param WessLinkDto $wessLinkDto
     * @return WessLinkDto
     */
    public function editWessLink(WessLinkDto $wessLinkDto)
    {
        
        $this->wessLinkRepository = $this->repositoryResolver->getRepository(SurveyConstant::WESS_LINK_ENTITY);
        $wessLink = $this->wessLinkRepository->findOneBy(array(
            'wessSurveyId' => $wessLinkDto->getWessSurveyId(),
            'wessCohortId' => $wessLinkDto->getWessCohortId(),
            'wessProdYear' => $wessLinkDto->getWessProdYear(),
            'wessOrderId' => $wessLinkDto->getWessOrderId()
        ));
        $this->isObjectExist($wessLink, SurveyConstant::WESSLINK_NOT_FOUND, SurveyConstant::WESSLINK_NOT_FOUND_KEY);
        $wessLink->setStatus(strtolower($wessLinkDto->getStatus()));
        $wessLink->setWessLaunchedflag($wessLinkDto->getWessLaunchedflag());
        $wessLink->setOpenDate(Helper::getUtcDate($wessLinkDto->getOpenDate()));
        $wessLink->setCloseDate(Helper::getUtcDate($wessLinkDto->getCloseDate()));
        $this->wessLinkRepository->flush();
        return $wessLinkDto;
    }

    /**
     * @param WessLinkInsertDto $wessLinkInsertDto
     */
    public function insertWessLink(WessLinkInsertDto $wessLinkInsertDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($wessLinkInsertDto);
        $this->logger->debug(" Insert WESS LINK -  " . $logContent);
        
        $wessSurveyId = $wessLinkInsertDto->getSurveyIdExternal();
        $customerId = $wessLinkInsertDto->getCustomerId();
        $cohortCode = $wessLinkInsertDto->getCohortIdExternal();
        $prodYear = $wessLinkInsertDto->getProdYearExternal();
        $orderId = $wessLinkInsertDto->getOrderIdExternal();
        $mapOrder = $wessLinkInsertDto->getMapOrderKeyExternal();
        $adminLink = $wessLinkInsertDto->getAdminLinkExternal();
        $this->orgRepoitory = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_ORGANIZATION_ENTITY);
        $orgObj = $this->orgRepoitory->findOneBy(array(
            SurveyConstant::CAMPUS_ID => $customerId
        ));
        
        $this->isObjectExist($orgObj, 'Invalid organization Id', 'Invalid_Organization_Id');
        $synapseOrgId = $orgObj->getId();
        
        $this->surveyRepoitory = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_SURVEY_ENTITY);
        $surveyObj = $this->surveyRepoitory->findOneBy(array(
            'externalId' => $wessSurveyId
        ));
        
        $this->isObjectExist($surveyObj, 'Invalid Survey Id', 'Invalid_Survey_Id');
        $synapseSurveyId = $surveyObj->getId();
        
        $wessObj = new WessLink();
        $wessObj->setOrganization($orgObj);
        $wessObj->setWessCustId($customerId);
        $wessObj->setWessSurveyId($wessSurveyId);
        $wessObj->setSurvey($surveyObj);
        $wessObj->setCohortCode($cohortCode);
        $wessObj->setWessProdYear($prodYear);
        $wessObj->setWessCohortId($cohortCode);
        $wessObj->setWessOrderId($orderId);
        $wessObj->setWessMaporderKey($mapOrder);
        $wessObj->setStatus("open");
        $wessObj->setWessAdminLink($adminLink);
        $this->wessRepo = $this->repositoryResolver->getRepository("SynapseSurveyBundle:WessLink");
        $this->wessRepo->persist($wessObj);
    }


    /**
     * @return array
     */
    public function generateCampusDump()
    {
        $this->org = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_ORGANIZATION_ENTITY);
        $this->logger->info('get the campus data');
        $campusData = $this->org->getAllCampus();
        $date = date('Y-m-d_h-i-s');
        $this->ebiConfig = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_EBI_CONFIG_ENTITY);
        $this->logger->info('get the campus admin url');
        $ebiConfig = $this->ebiConfig->findOneBy(array(
            'key' => 'System_Admin_URL'
        ));
        if ($ebiConfig) {
            $systemUrl = $ebiConfig->getValue();
        }
        foreach ($campusData as $key => $campus) {
            $campus['url'] = $systemUrl;
            $finalArr[] = $campus;
        }
        return $finalArr;
    }

    /**
     * Update Survey Responses.
     *
     * @param WessSurveyResponseDto $wessSurveyResponseDto
     */
    public function updateSurveyResponse($wessSurveyResponseDto)
    {
        $surveyResponseData = $wessSurveyResponseDto->getSurveyResponse();
        
        foreach ($surveyResponseData as $surveyResponse) {
            $organizationId = $surveyResponse->getOrganizationId();
            $personId = $surveyResponse->getPersonId();
            $personObject = $this->person->findOneById($personId);

            if (! $personObject) {
                $this->logger->error(" Survey Service -  updateSurveyResponse - Not a valid person  $personId  for Org :$organizationId ");
                throw new ValidationException([
                    SurveyConstant::INVALID_PERSON_ID . $personId . SurveyConstant::AND_ORG_ID . $organizationId
                ], SurveyConstant::INVALID_PERSON_ID . $personId . SurveyConstant::AND_ORG_ID . $organizationId, SurveyConstant::INVALID_PERSON_ID . $personId . SurveyConstant::AND_ORG_ID . $organizationId);
            }
            $surveyId = $surveyResponse->getSurveyId();
            $surveyInstance = $this->survey->findOneById($surveyId);
            if (! $surveyInstance) {
                $this->logger->error( "Survey Service - updateSurveyResponse - " . SurveyConstant::INVALID_SURVEY_ID . $surveyId . " for Organization :" . $organizationId );
                throw new ValidationException([
                    SurveyConstant::INVALID_SURVEY_ID . $surveyId
                ], SurveyConstant::INVALID_SURVEY_ID . $surveyId, SurveyConstant::INVALID_SURVEY_ID . $surveyId);
            }
            
            // Updating risk calculation
            $riskCalculationInputDto = new RiskCalculationInputDto();
            $riskCalculationInputDto->setPersonId($personId);
            $riskCalculationInputDto->setOrganizationId($organizationId);
            $riskCalculationInputDto->setIsRiskvalCalcRequired('y');
            $riskCalculationInputDto->setIsTalkingPointCalcReqd('y');
            $riskCalculationInputDto->setIsSuccessMarkerCalcReqd('y');
            $riskCalculationInputDto->setIsFactorCalcReqd('y');
            $this->riskCalculationService->createRiskCalculationInput($riskCalculationInputDto);
        }
        
        foreach ($surveyResponseData as $responseData) {
            $surveyResponseEntity = new SurveyResponse();
            $organizationId = $responseData->getOrganizationId();
            $orgObject = $this->org->findOneById($organizationId);
            $personId = $responseData->getPersonId();
            $personObject = $this->person->findOneById($personId);
            $surveyId = $responseData->getSurveyId();
            $surveyObj = $this->survey->findOneById($surveyId);
            $academicYearId = $responseData->getAcademicYearId();
            
            $academicTermId = $responseData->getAcademicTermId();
            $academicYearObject = $this->academicYear->findOneById($academicYearId);
            $surveyQuestionId = $responseData->getSurveyQuestionId();
            $surveyQuesObject = $this->surveyQuestion->findOneById($surveyQuestionId);
            
            if (! $academicYearObject) {
                throw new ValidationException([
                    "Invalid Academic year for Person: $personId and surveyQuestion :$surveyQuestionId AND org : $organizationId "
                ], "Invalid Academic year for Person: $personId and surveyQuestion :$surveyQuestionId AND org : $organizationId", "Invalid_Academic_Year");
            }
            $academicTerm = $this->checkAcademicTerm($academicTermId, $academicYearId, $orgObject->getId());
            
            $surveyResponseType = $responseData->getResponseType();
            $surveyResponseValue = $responseData->getResponseValue();
            
            $existingSurveyResponseEntity = $this->surveyResponse->findOneBy(array(
                SurveyConstant::ORGANIZATION => $organizationId,
                SurveyConstant::PERSON => $personId,
                'survey' => $surveyId,
                'orgAcademicYear' => $academicYearId,
                'surveyQuestions' => $surveyQuestionId
            ));
            
            if ($existingSurveyResponseEntity) {
                $existingSurveyResponseEntity->setResponseType($surveyResponseType);
                $existingSurveyResponseEntity->setDecimalValue($surveyResponseValue);
                $existingSurveyResponseEntity->setOrgAcademicTerms($academicTerm);
                $this->surveyResponse->update($existingSurveyResponseEntity);
            } else {
                $surveyResponseEntity->setOrganization($orgObject);
                $surveyResponseEntity->setPerson($personObject);
                $surveyResponseEntity->setSurvey($surveyObj);
                $surveyResponseEntity->setOrgAcademicYear($academicYearObject);
                $surveyResponseEntity->setOrgAcademicTerms($academicTerm);
                $surveyResponseEntity->setSurveyQuestions($surveyQuesObject);
                $surveyResponseEntity->setResponseType($surveyResponseType);
                $surveyResponseEntity->setDecimalValue($surveyResponseValue);
                $this->surveyResponse->persist($surveyResponseEntity);
            }
        }
    }

    /**
     * @param $wessOrgResponseDto
     * @param $orgId
     */
    public function updateISQ($wessOrgResponseDto, $orgId)
    {
        $responseData = $wessOrgResponseDto->getOrgResponse();
        $this->org = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_ORGANIZATION_ENTITY);
        $this->person = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_PERSON_ENTITY);
        $this->survey = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_SURVEY_ENTITY);
        $this->academicYear = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_ACADEMIC_YEAR_ENTITY);
        
        $this->orgQuestion = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgQuestion');
        $this->orgResponse = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgQuestionResponse');
        
        $this->logger->info("Update ISQ for OrgId ");
        $this->logger->info("Response Data OrgId ");
        foreach ($responseData as $resp) {
            $orgId = $resp->getOrganizationId();
            $orgObj = $this->org->findOneById($orgId);
            $persoId = $resp->getPersonId();
            $personObj = $this->person->findOneById($persoId);
            $surveyId = $resp->getSurveyId();
            $surveyObj = $this->survey->findOneById($surveyId);
            $personObj = $this->person->findOneBy(array(
                SurveyConstant::ORGANIZATION => $orgId,
                'id' => $persoId
            ));
            if (! $personObj) {
                $this->logger->error(" Survey Service - updateISQ - Not a valid person " . $persoId ." for Org :$orgId ");
                throw new ValidationException([
                    SurveyConstant::INVALID_PERSON_ID . $persoId . SurveyConstant::AND_ORG_ID . $orgId
                ], SurveyConstant::INVALID_PERSON_ID . $persoId . SurveyConstant::AND_ORG_ID . $orgId, SurveyConstant::INVALID_PERSON_ID . $persoId . SurveyConstant::AND_ORG_ID . $orgId);
            }
        }
        
        foreach ($responseData as $resp) {
            $orgEntity = new OrgQuestionResponse();
            $orgId = $resp->getOrganizationId();
            $orgObj = $this->org->findOneById($orgId);
            $persoId = $resp->getPersonId();
            $personObj = $this->person->findOneById($persoId);
            
            $academicYearId = $resp->getAcademicYearId();
            
            $academicTermId = $resp->getAcademicTermId();
            $academicYearObj = $this->academicYear->findOneById($academicYearId);
            $orgQuestionId = $resp->getOrgQuestionId();
            $orgQuesObj = $this->orgQuestion->findOneById($orgQuestionId);
            
            if (! $academicYearObj) {
                
                $this->logger->error(" Survey Service - updateISQ - Not a valid Academic year $academicYearId  for Org :$orgId ");
                throw new ValidationException([
                    "Invalid Academic year for Person: $persoId and orgQuestion :$orgQuestionId AND org : $orgId "
                ], "Invalid Academic year for Person: $persoId and orgQuestion :$orgQuestionId AND org : $orgId", "Invalid_Academic_Year");
            }
            $academicTerm = $this->checkAcademicTerm($academicTermId, $academicYearId, $orgObj->getId());
            
            $respText = $resp->getResponseType();
            $respval = $resp->getResponseValue();
            
            $respObj = $this->orgResponse->findOneBy(array(
                'org' => $orgId,
                'person' => $persoId,
                'survey' => $surveyId,
                'orgAcademicYear' => $academicYearId,
                'orgQuestion' => $orgQuestionId
            ));
            
            if ($respObj) {
                $respObj->setResponseType($respText);
                $respObj->setDecimalValue($respval);
                $respObj->setOrgAcademicTerms($academicTerm);
                $this->orgResponse->update($respObj);
            } else {
                $orgEntity->setOrg($orgObj);
                $orgEntity->setPerson($personObj);
                
                $orgEntity->setOrgAcademicYear($academicYearObj);
                $orgEntity->setOrgAcademicTerms($academicTerm);
                $orgEntity->setSurvey($surveyObj);
                $orgEntity->setOrgQuestion($orgQuesObj);
                $orgEntity->setResponseType($respText);
                $orgEntity->setDecimalValue($respval);
                $this->orgResponse->persist($orgEntity);
            }
        }
    }

    /**
     * @param WessLinkInsertDto $wessLinkInsertDto
     */
    public function updateWess(WessLinkInsertDto $wessLinkInsertDto)
    {
        $wessSurveyId = $wessLinkInsertDto->getSurveyIdExternal();
        $customerId = $wessLinkInsertDto->getCustomerId();
        $cohortCode = $wessLinkInsertDto->getCohortIdExternal();
        $prodYear = $wessLinkInsertDto->getProdYearExternal();
        $orderId = $wessLinkInsertDto->getOrderIdExternal();
        $mapOrder = $wessLinkInsertDto->getMapOrderKeyExternal();
        $adminLink = $wessLinkInsertDto->getAdminLinkExternal();
        
        $surveyOpenDate = $wessLinkInsertDto->getSurveyOpenDate();
        $surveyCloseDate = $wessLinkInsertDto->getSurveyCloseDate();
        $launchedFlag = $wessLinkInsertDto->getWessLaunchedflag();
        $surveyStatus = $wessLinkInsertDto->getSurveyStatus();
        
        $this->wessLinkRepository = $this->repositoryResolver->getRepository(SurveyConstant::WESS_LINK_ENTITY);
        
        $this->orgRepoitory = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_ORGANIZATION_ENTITY);
        $orgObj = $this->orgRepoitory->findOneBy(array(
            SurveyConstant::CAMPUS_ID => $customerId
        ));
        
        if (! $orgObj) {
            $this->logger->error(" Survey Service - updateWess - Not a valid  Org : " . $orgObj->getId() );
            throw new ValidationException([
                SurveyConstant::INVALID_ORGANIZATION
            ], SurveyConstant::INVALID_ORGANIZATION, SurveyConstant::INVALID_ORGANIZATION);
        }
        
        $this->isObjectExist($orgObj, 'Invalid organization Id', 'Invalid_Organization_Id');
        $synapseOrgId = $orgObj->getId();
        
        $this->surveyRepoitory = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_SURVEY_ENTITY);
        $surveyObj = $this->surveyRepoitory->findOneBy(array(
            'externalId' => $wessSurveyId
        ));
        
        $this->isObjectExist($surveyObj, 'Invalid Survey Id', 'Invalid_Survey_Id');
        $synapseSurveyId = $surveyObj->getId();
        
        $wessLink = $this->wessLinkRepository->findOneBy(array(
            'wessSurveyId' => $wessLinkInsertDto->getSurveyIdExternal(),
            'wessCohortId' => $wessLinkInsertDto->getCohortIdExternal(),
            'wessProdYear' => $wessLinkInsertDto->getProdYearExternal(),
            'wessOrderId' => $wessLinkInsertDto->getOrderIdExternal(),
            'wessCustId' => $wessLinkInsertDto->getCustomerId()
        ));
        
        if ($wessLink) {
            $wessLink->setWessMaporderKey($mapOrder);
            $wessLink->setStatus($surveyStatus);
            $wessLink->setOpenDate($surveyOpenDate);
            $wessLink->setCloseDate($surveyCloseDate);
            $wessLink->setWessAdminLink($adminLink);
            $wessLink->setWessLaunchedflag($launchedFlag);
            $this->wessLinkRepository->flush();
        } else {
            throw new ValidationException([
                "No record found to update "
            ], "No record found to update", "No_record_found_to_update");
        }
    }


    /**
     * @param $academicTermId
     * @param $academicYearId
     * @param $orgId
     * @return null|object
     */
    private function checkAcademicTerm($academicTermId, $academicYearId, $orgId)
    {
        $this->academicTerm = $this->repositoryResolver->getRepository('SynapseAcademicBundle:OrgAcademicTerms');
        if ($academicTermId) {
            $acaTerm = $this->academicTerm->findOneBY(array(
                'id' => $academicTermId,
                'orgAcademicYearId' => $academicYearId,
                'organization' => $orgId
            ));
            
            if (! $acaTerm) {
                throw new ValidationException([
                    SurveyConstant::INVALID_ACADEMIC_TERM_FOR_YEAR . $academicYearId . SurveyConstant::AND_ORG_ID . $orgId
                ], SurveyConstant::INVALID_ACADEMIC_TERM_FOR_YEAR . $academicYearId . SurveyConstant::AND_ORG_ID . $orgId, SurveyConstant::INVALID_ACADEMIC_TERM_FOR_YEAR . $academicYearId . SurveyConstant::AND_ORG_ID . $orgId);
            } else {
                return $acaTerm;
            }
        } else {
            return null;
        }
    }

    /**
     * @param $orgQuesDto
     */
    public function updateOrgQuestions($orgQuesDto)
    {
        $orgQuesArr = $orgQuesDto->getOrgQuestion();
        
        foreach ($orgQuesArr as $orgQues) {
            
            $orgId = $orgQues->getOrganizationId();
            $questionTypeId = $orgQues->getQuestionTypeId();
            $questionCategoryId = $orgQues->getQuestionCategoryId();
            $questionkey = $orgQues->getQuestionkey();
            $questionText = $orgQues->getQuestionText();
            
            $this->logger->info("In Update org Question OrgId :$orgId ");
            $this->orgRepoitory = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_ORGANIZATION_ENTITY);
            $orgObj = $this->orgRepoitory->findOneBy(array(
                'id' => $orgId
            ));
            
            if (! $orgObj) {
                $this->logger->error("Survey Service - updateOrgQuestions - In valid Org  :$orgId ");
                throw new ValidationException([
                    "Invalid Org  :($orgId)"
                ], "Invalid Org :($orgId)", "Invalid Org :($orgId)");
            }
            
            $this->qType = $this->repositoryResolver->getRepository("SynapseCoreBundle:QuestionType");
            $qTypeObj = $this->qType->findOneById($questionTypeId);
            
            if (! $qTypeObj) {
                $this->logger->error("Survey Service - updateOrgQuestions - In valid question type :   :$questionTypeId ");
                throw new ValidationException([
                    "Invalid Question Type  :($questionTypeId)"
                ], "Invalid Question Type :($questionTypeId)", "Invalid_Question_Type :($questionTypeId)");
            }
            
            $this->qCategory = $this->repositoryResolver->getRepository("SynapseCoreBundle:QuestionCategory");
            $qCatObj = $this->qCategory->findOneById($questionCategoryId);
            if (! $qCatObj) {
                $this->logger->error("Survey Service - updateOrgQuestions - Invalid category  ");
                throw new ValidationException([
                    "Invalid Category  :($questionCategoryId)"
                ], "Invalid Category :($questionCategoryId)", "Invalid_Category :($questionCategoryId)");
            }
            $this->orgQuestion = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgQuestion");
            
            $orgQues = new OrgQuestion();
            $orgQues->setQuestionCategory($qCatObj);
            $orgQues->setQuestionType($qTypeObj);
            $orgQues->setOrganization($orgObj);
            $orgQues->setQuestionKey($questionkey);
            $orgQues->setQuestionText($questionText);
            $this->orgQuestion->persist($orgQues, false);
        }
        
        $this->orgQuestion->flush();
    }

    /**
     * @param $orgId
     * @return bool|string
     */
    public function getWessLinkSurveyStatus($orgId)
    {
        $this->wessLinkRepository = $this->repositoryResolver->getRepository(SurveyConstant::WESS_LINK_ENTITY);
        $surveyStatus = '';
        $surveyDetails = '';
        $surveyDetails = $this->wessLinkRepository->findOneBy(array(
            "organization" => $orgId,
            "status" => 'closed'
        ));
        $surveyStatus = $surveyDetails ? true : false;
        return $surveyStatus;
    }

    /**
     * @param SurveyAccessStatusDto $surveyAccessStatusDto
     * @param $loggedUser
     * @return SurveyAccessStatusDto
     */
    public function updateSurveyReportStatus(SurveyAccessStatusDto $surveyAccessStatusDto, $loggedUser)
    {
        $organization = $loggedUser->getOrganization();
        $personRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:Person");
        $surveyrepo = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_SURVEY_ENTITY);
        $academicYearRepo = $this->repositoryResolver->getRepository(SurveyConstant::SYNAPSE_ACADEMIC_YEAR_ENTITY);
        $surveyAccessRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrgSurveyReportAccessHistory");
        
        $student = $personRepo->findOneBy([
            'id' => $surveyAccessStatusDto->getStudent(),
            'organization' => $organization
        ]);
        if (! $student) {
            throw new ValidationException([
                'Student Doens\'t exist'
            ], 'Student Doens\'t exist', 'person_not_fouond');
        }
        $survey = $surveyrepo->find($surveyAccessStatusDto->getSurvey());
        
        if (! $survey) {
            throw new ValidationException([
                'Survey Doens\'t exist'
            ], 'Survey Doens\'t exist', 'survey_not_fouond');
        }
        
        $academicYearDetails = $academicYearRepo->findOneBy([
            'organization' => $organization,
            'yearId' => $surveyAccessStatusDto->getYear()
        ]);
        
        if (! $academicYearDetails) {
            
            throw new ValidationException([
                SurveyConstant::NO_ACADEMIC_YEAR_FOUND
            ], SurveyConstant::NO_ACADEMIC_YEAR_FOUND, SurveyConstant::NO_ACADEMIC_YEAR_FOUND);
        }
        
        $year = $academicYearDetails->getYearId();
        $cohort = $surveyAccessStatusDto->getCohort();
        $accessReport = $surveyAccessRepo->findOneBy([
            'orgId' => $organization,
            'person' => $loggedUser,
            'student' => $student,
            'survey' => $survey,
            'year' => $year,
            'cohortCode' => $cohort
        ]);
        
        if ($accessReport) {
            $accessReport->setLastAccessedOn(new \DateTime('now'));
        } else {
            $accessReport = new OrgSurveyReportAccessHistory();
            $accessReport->setCohortCode($cohort);
            $accessReport->setLastAccessedOn(new \DateTime('now'));
            $accessReport->setOrganization($organization);
            $accessReport->setPerson($loggedUser);
            $accessReport->setStudent($student);
            $accessReport->setSurvey($survey);
            $accessReport->setYear($year);
            $surveyAccessRepo->persist($accessReport);
        }
        $surveyAccessRepo->flush();
        
        return $surveyAccessStatusDto;
    }

    /**
     * Gets surveys and cohorts within each survey, including survey completion data if requested.
     * If the user is a coordinator, returns data for the whole organization; otherwise, only considers accessible students.
     *
     * If $purpose is set to "isq_access", only includes survey and cohort combinations for which the user has ISQ access.
     * The $isAggregateReporting parameter is meant to be used in conjunction with this $purpose parameter to determine which permission sets to include when determining ISQ access.
     * If $isAggregateReporting is true (e.g., in report filters), all permission sets will be included for determining ISQ access.
     * If $isAggregateReporting is false (e.g., in custom search), only individual permission sets will be included for determining ISQ access.
     *
     * @param int $orgId
     * @param int $personId
     * @param int|null $orgAcademicYearId
     * @param array|null $surveyStatus
     * @param int|null $surveyId
     * @param boolean $includeCompletionDataFlag
     * @param string|null $purpose -- possible value "isq_access"
     * @param boolean $isAggregateReporting
     * @return array
     */
    public function getSurveysAndCohorts($orgId, $personId, $orgAcademicYearId, $surveyStatus, $surveyId, $includeCompletionDataFlag, $purpose, $isAggregateReporting)
    {
        $hasCoordinatorAccess = $this->roleService->hasCoordinatorOmniscience($personId);

        if ($purpose == 'isq_access') {
            $surveysAndCohortsData = $this->ISQPermissionsetService->getSurveysAndCohortsHavingAccessibleISQs($personId, $orgId, $isAggregateReporting, $hasCoordinatorAccess, $orgAcademicYearId, $surveyStatus);
        } else {

            if (!$includeCompletionDataFlag && $hasCoordinatorAccess) {
                // get the survey and cohort data using a permission bypass
                $surveysAndCohortsData = $this->wessLinkRepository->getSurveysAndCohortsForOrganizationWithoutPermissionCheck($orgId, $orgAcademicYearId, $surveyStatus, $surveyId);

            } else if (!$includeCompletionDataFlag) {
                // get cohorts and surveys without has responses information
                $surveysAndCohortsData = $this->wessLinkRepository->getSurveysAndCohortsForOrganizationWithoutCompletionData($orgId, $personId, $orgAcademicYearId, $surveyStatus, $surveyId);

            } else {
                // get has responses vs total students in addition to the other survye and cohort data
                $surveysAndCohortsData = $this->wessLinkRepository->getSurveysAndCohortsForOrganizationWithCompletionData($orgId, $personId, $orgAcademicYearId, $surveyStatus, $surveyId, $hasCoordinatorAccess);

            }
        }

        if (!empty($surveysAndCohortsData)) {
            $formattedSurveyAndCohortData = $this->formatSurveyAndCohortData($orgId, $surveysAndCohortsData, $includeCompletionDataFlag);
        } else {
            $formattedSurveyAndCohortData = [];
        }

        return $formattedSurveyAndCohortData;
    }


    /**
     * Format data into proper JSON return format for survey and cohort with completion data
     *
     * @param int $orgId
     * @param array $surveysAndCohortsData
     * @param boolean $includeCompletionDataFlag
     * @return array
     */
    private function formatSurveyAndCohortData($orgId, $surveysAndCohortsData, $includeCompletionDataFlag)
    {
        // As a first pass at formatting the data, sort the database records into an array
        // where each key is a survey_id and the corresponding value is an array of records for that survey.
        $dataGroupedBySurvey = [];

        foreach ($surveysAndCohortsData as $record) {
            $dataGroupedBySurvey[$record['survey_id']][] = $record;
        }

        // Sort the surveys in descending order.
        krsort($dataGroupedBySurvey);

        // Then iterate over each of these dimensions to format the data as desired.
        $surveyDataToReturn = [];

        foreach ($dataGroupedBySurvey as $surveyId => $dataForSurvey) {
            $recordToReturn = [];
            $recordToReturn['year_id'] = $dataForSurvey[0]['year_id'];
            $recordToReturn['org_academic_year_id'] = $dataForSurvey[0]['org_academic_year_id'];
            $recordToReturn['year_name'] = $dataForSurvey[0]['year_name'];
            $recordToReturn['survey_id'] = $surveyId;
            $recordToReturn['survey_name'] = $dataForSurvey[0]['survey_name'];

            $cohortData = [];

            foreach ($dataForSurvey as $record) {
                $cohortRecord = [];
                $cohortRecord['cohort'] = (int) $record['cohort'];
                $cohortRecord['cohort_name'] = $record['cohort_name'];
                $cohortRecord['status'] = $record['status'];
                $cohortRecord['open_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($record['open_date']);
                $cohortRecord['close_date'] = $this->dateUtilityService->convertDatabaseStringToISOString($record['close_date']);

                if ($includeCompletionDataFlag) {
                    $cohortRecord['student_count'] = $record['student_count'];
                    $cohortRecord['students_responded_count'] = $record['students_responded_count'];
                    if ($cohortRecord['student_count'] > 0) {
                        $cohortRecord['percentage_responded'] = round(100 * $record['students_responded_count'] / $record['student_count']);
                    } else {
                        $cohortRecord['percentage_responded'] = '-';
                    }
                }

                $cohortData[] = $cohortRecord;
            }

            $recordToReturn['cohorts'] = $cohortData;

            $surveyDataToReturn[] = $recordToReturn;
        }

        $returnData = [];
        $returnData['organization_id'] = $orgId;
        $returnData['surveys'] = $surveyDataToReturn;

        return $returnData;
    }

    /**
     * Does return survey data with open/close date and status for Admin user
     *
     * @return array
     */
    public function getAllSurveys()
    {
        $result = $this->surveyLangRepository->getAllSurveys();

        return $result;
    }

    /**
     * Returns basic survey data (survey ids, names, and years) for the given organization,
     * ordered by year_id and then by survey_id, both with the most recent listed first.
     * If the statusArray parameter is used, surveys will be included if they have one of the listed statuses
     * for at least one cohort in the organization.
     *
     * @param int $organizationId
     * @param int|null $orgAcademicYearId
     * @param array $statusArray Eg:launched|closed|launched,closed
     * @return array
     */
    public function getSurveysForOrganization($organizationId, $orgAcademicYearId, $statusArray)
    {
        $result = $this->wessLinkRepository->getSurveysForOrganization($organizationId, $orgAcademicYearId, $statusArray);

        return $result;
    }

}
