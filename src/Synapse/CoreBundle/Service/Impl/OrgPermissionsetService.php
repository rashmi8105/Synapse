<?php

namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Validator\LegacyValidator;
use Synapse\AcademicBundle\Repository\OrgCourseFacultyRepository;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrgPermissionset;
use Synapse\CoreBundle\Entity\OrgPermissionsetDatablock;
use Synapse\CoreBundle\Entity\OrgPermissionsetQuestion;
use Synapse\CoreBundle\Entity\OrgReportPermissions;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\DatablockMasterLangRepository;
use Synapse\CoreBundle\Repository\FeatureMasterRepository;
use Synapse\CoreBundle\Repository\OrganizationlangRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\OrgFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;
use Synapse\CoreBundle\Repository\OrgGroupStudentsRepository;
use Synapse\CoreBundle\Repository\OrgMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetDatablockRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetMetadataRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetQuestionRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgQuestionRepository;
use Synapse\CoreBundle\Repository\OrgReportPermissionsRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\OrgPermissionsetConstant;
use Synapse\CoreBundle\Util\Constants\SurveyConstant;
use Synapse\RestBundle\Entity\AccessLevelDto;
use Synapse\RestBundle\Entity\BlockDto;
use Synapse\RestBundle\Entity\CoursesAccessDto;
use Synapse\RestBundle\Entity\FeatureBlockDto;
use Synapse\RestBundle\Entity\IspBlockDto;
use Synapse\RestBundle\Entity\IsqBlockDto;
use Synapse\RestBundle\Entity\OrgPermissionSetDto;
use Synapse\RestBundle\Entity\PermissionValueDto;
use Synapse\RestBundle\Entity\ReportSelectionDto;
use Synapse\RestBundle\Entity\ToolSelectionDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\MapworksToolBundle\Entity\OrgPermissionsetTool;
use Synapse\MapworksToolBundle\Repository\MapworksToolRepository;
use Synapse\MapworksToolBundle\Repository\OrgPermissionsetToolRepository;

/**
 * @DI\Service("orgpermissionset_service")
 */
class OrgPermissionsetService extends OrgPermissionsetServiceHelper
{

    const SERVICE_KEY = 'orgpermissionset_service';

    // Scaffolding

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Manager
     */
    private $rbacManager;

    /**
     * @var LegacyValidator
     */
    private $validator;

    // Services

    /**
     * @var FeatureService
     */
    protected $featureService;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var OrganizationService
     */
    protected $organizationService;

    /**
     * @var PermissionSetService
     */
    protected $permissionsetService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var RetentionCompletionService
     */
    private $retentionCompletionService;

    // Repositories

    /**
     * @var DatablockMasterLangRepository
     */
    protected $dataBlockMasterLangRepository;

    /**
     * @var FeatureMasterRepository
     */
    protected $featureMasterRepository;

    /**
     * @var mapworksToolRepository
     */
    protected $mapworksToolRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var OrgCourseFacultyRepository
     */
    protected $orgCourseFacultyRepository;

    /**
     * @var OrganizationlangRepository
     */
    protected $organizationLangRepository;

    /**
     * @var OrgFeaturesRepository
     */
    protected $orgFeaturesRepository;

    /**
     * @var OrgGroupFacultyRepository
     */
    protected $orgGroupFacultyRepository;

    /**
     * @var OrgGroupStudentsRepository
     */
    protected $orgGroupStudentRepository;

    /**
     * @var OrgMetadataRepository
     */
    protected $orgMetadataRepository;

    /**
     *
     * @var OrgPermissionsetDatablockRepository
     */
    protected $orgPermissionsetDatablockRepository;

    /**
     * @var OrgPermissionsetFeaturesRepository
     */
    protected $orgPermissionsetFeaturesRepository;

    /**
     * @var OrgPermissionsetMetadataRepository
     */
    protected $orgPermissionsetMetadataRepository;

    /**
     * @var OrgPermissionsetQuestionRepository
     */
    protected $orgPermissionsetQuestionRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    protected $orgPermissionsetRepository;

    /**
     * @var OrgQuestionRepository
     */
    protected $orgQuestionRepository;

    /**
     * @var orgPermissionsetToolRepository
     */
    protected $orgPermissionsetToolRepository;

    private $featureAccess = array();

    /**
     *
     * OrgPermissionsetService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //Scaffolding
        $this->container = $container;
        $this->validator = $this->container->get(SynapseConstant::VALIDATOR);

        // Services
        $this->loggerHelperService = $this->container->get(LoggerHelperService::SERVICE_KEY);
        $this->permissionsetService = $this->container->get(PermissionSetService::SERVICE_KEY);
        $this->retentionCompletionService = $this->container->get(RetentionCompletionService::SERVICE_KEY);

        //Repositories
        $this->dataBlockMasterLangRepository = $this->repositoryResolver->getRepository(DatablockMasterLangRepository::REPOSITORY_KEY);
        $this->featureMasterRepository = $this->repositoryResolver->getRepository(FeatureMasterRepository::REPOSITORY_KEY);
        $this->mapworksToolRepository = $this->repositoryResolver->getRepository(MapworksToolRepository::REPOSITORY_KEY);
        $this->organizationLangRepository = $this->repositoryResolver->getRepository(OrganizationlangRepository::REPOSITORY_KEY);
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgCourseFacultyRepository = $this->repositoryResolver->getRepository(OrgCourseFacultyRepository::REPOSITORY_KEY);
        $this->orgFeaturesRepository = $this->repositoryResolver->getRepository(OrgFeaturesRepository::REPOSITORY_KEY);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgGroupFacultyRepository::REPOSITORY_KEY);
        $this->orgGroupStudentRepository = $this->repositoryResolver->getRepository(OrgGroupStudentsRepository::REPOSITORY_KEY);
        $this->orgMetadataRepository = $this->repositoryResolver->getRepository(OrgMetadataRepository::REPOSITORY_KEY);
        $this->orgPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(OrgPermissionsetDatablockRepository::REPOSITORY_KEY);
        $this->orgPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository(OrgPermissionsetFeaturesRepository::REPOSITORY_KEY);
        $this->orgPermissionsetMetadataRepository = $this->repositoryResolver->getRepository(OrgPermissionsetMetadataRepository::REPOSITORY_KEY);
        $this->orgPermissionsetQuestionRepository = $this->repositoryResolver->getRepository(OrgPermissionsetQuestionRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPermissionsetToolRepository = $this->repositoryResolver->getRepository(OrgPermissionsetToolRepository::REPOSITORY_KEY);
        $this->orgQuestionRepository = $this->repositoryResolver->getRepository(OrgQuestionRepository::REPOSITORY_KEY);
        $this->orgReportPermissionsRepository = $this->repositoryResolver->getRepository(OrgReportPermissionsRepository::REPOSITORY_KEY);
    }

    public function find($id)
    {
        $this->logger->info(">>>> finding Permission set for Id ");
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_PERMISSIONSET_REPO);
        $orgPermissionset = $this->orgPermissionsetRepository->find($id);
        if (!$orgPermissionset) {
            $this->logger->error(" OrgPermissionset Service - find - " . OrgPermissionsetConstant::ERROR_PERMISSIONSET_NOT_FOUND);
            throw new ValidationException([
                OrgPermissionsetConstant::ERROR_PERMISSIONSET_NOT_FOUND
            ], OrgPermissionsetConstant::ERROR_PERMISSIONSET_NOT_FOUND, 'Permissionset_not_found');
        }
        $this->logger->info(">>>> Finding Permission Set" . $id);
        return $orgPermissionset;
    }

    /**
     * Copy Ebipermissionset to OrgPermissionset
     *
     * @param Organization $organization
     * @param integer $languageMasterId
     */
    public function copyEbiPermissionset(Organization $organization, $languageMasterId)
    {
        $ebiPermissionSets = $this->permissionsetService->listPermissionSetByStatus($languageMasterId, 'active');
        if (isset($ebiPermissionSets['permission_template']) && count($ebiPermissionSets['permission_template']) > 0) {
            foreach ($ebiPermissionSets['permission_template'] as $ebiTemplate) {

                $accessLevel = (bool)$ebiTemplate->getAccessLevel();
                $courseAccess = (bool)$ebiTemplate->getCoursesAccess();
                $ebiTemplateFeatures = $ebiTemplate->getFeatures();

                $orgPermissionSetDto = new OrgPermissionSetDto();
                $orgPermissionSetDto->setPermissionTemplateName($ebiTemplate->getPermissionTemplateName());
                $orgPermissionSetDto->setOrganizationId($organization->getId());
                $accessLevelDto = new AccessLevelDto();
                $accessLevelDto->setIndividualAndAggregate($accessLevel->getIndividualAndAggregate());
                $accessLevelDto->setAggregateOnly($accessLevel->getAggregateOnly());
                $orgPermissionSetDto->setAccessLevel($accessLevelDto);
                $orgPermissionSetDto->setRiskIndicator((bool)$ebiTemplate->getRiskIndicator());
                $orgPermissionSetDto->setIntentToLeave((bool)$ebiTemplate->getIntentToLeave());

                // Setting Courses Access
                $coursesAccessDto = new CoursesAccessDto();
                $coursesAccessDto->setCreateViewAcademicUpdate($courseAccess->getCreateViewAcademicUpdate());
                $coursesAccessDto->setViewAllAcademicUpdateCourses($courseAccess->getViewAllAcademicUpdateCourses());
                $coursesAccessDto->setViewAllFinalGrades($courseAccess->getViewAllFinalGrades());
                $coursesAccessDto->setViewCourses($courseAccess->getViewCourses());
                $orgPermissionSetDto->setCoursesAccess($coursesAccessDto);

                // Setting Profile Blocks
                $profileBlocks = $this->getProfileBlocks($ebiTemplate);
                $orgPermissionSetDto->setProfileBlocks($profileBlocks);

                $surveyBlocks = $this->getSurveyBlocks($ebiTemplate);
                $orgPermissionSetDto->setSurveyBlocks($surveyBlocks);

                if ($ebiTemplateFeatures && count($ebiTemplateFeatures) > 0) {
                    $featureBlocks = $this->getOrgFeatureBlock($ebiTemplateFeatures);
                    $orgPermissionSetDto->setFeatures($featureBlocks);
                }

                $this->createOrgPermissionset($orgPermissionSetDto, true);

            }
        }
    }

    /**
     * Get organization feature block based on features
     *
     * @param array $features
     * @return array
     */
    private function getOrgFeatureBlock($features)
    {
        $featureBlocks = [];
        foreach ($features as $feature) {
            $directReferralPublicShare = null;
            $directReferralTeamsShare = null;
            $featureId = $feature->getId();
            $featurePublicShare = null;
            $featureTeamsShare = null;
            $reasonRoutedReferralPublicShare = null;
            $reasonRoutedReferralTeamsShare = null;

            $featureBlockDto = new FeatureBlockDto();
            $featureBlockDto->setId($featureId);

            $permissionValueDto = new PermissionValueDto();
            if ($featureId == SynapseConstant::REFERRAL_FEATURE_ID) {

                $featureReferralBlockDto = new FeatureBlockDto();

                $directReferral = $feature->getDirectReferral();
                $directReferralPublicShare = $directReferral->getPublicShare();
                $directReferralTeamsShare = $directReferral->getTeamsShare();

                $reasonRoutedReferral = $feature->getReasonRoutedReferral();
                $reasonRoutedReferralPublicShare = $reasonRoutedReferral->getPublicShare();
                $reasonRoutedReferralTeamsShare = $reasonRoutedReferral->getTeamsShare();

                $permissionValueDto = new PermissionValueDto();
                $permissionValueDto->setCreate($directReferral->getPrivateShare()
                    ->getCreate());
                $featureReferralBlockDto->setPrivateShare($permissionValueDto);

                $permissionValueDto = new PermissionValueDto();
                $permissionValueDto->setCreate($directReferralPublicShare->getCreate());
                $permissionValueDto->setView($directReferralPublicShare->getView());
                $featureReferralBlockDto->setPublicShare($permissionValueDto);

                $permissionValueDto = new PermissionValueDto();
                $permissionValueDto->setCreate($directReferralTeamsShare->getCreate());
                $permissionValueDto->setView($directReferralTeamsShare->getView());
                $featureReferralBlockDto->setTeamsShare($permissionValueDto);
                $featureBlockDto->setDirectReferral($featureReferralBlockDto);

                // Reason Routed Referral

                $featureReferralsBlockDto = new FeatureBlockDto();
                $permissionValueDto = new PermissionValueDto();
                $permissionValueDto->setCreate($reasonRoutedReferral->getPrivateShare()
                    ->getCreate());
                $featureReferralsBlockDto->setPrivateShare($permissionValueDto);

                $permissionValueDto = new PermissionValueDto();
                $permissionValueDto->setCreate($reasonRoutedReferralPublicShare->getCreate());
                $permissionValueDto->setView($reasonRoutedReferralPublicShare->getView());
                $featureReferralsBlockDto->setPublicShare($permissionValueDto);

                $permissionValueDto = new PermissionValueDto();
                $permissionValueDto->setCreate($reasonRoutedReferralTeamsShare->getCreate());
                $permissionValueDto->setView($reasonRoutedReferralTeamsShare->getView());
                $featureReferralsBlockDto->setTeamsShare($permissionValueDto);
                $featureBlockDto->setReasonRoutedReferral($featureReferralsBlockDto);
            } else {
                $featurePublicShare = $feature->getPublicShare();
                $featureTeamsShare = $feature->getTeamsShare();
                $permissionValueDto->setCreate($feature->getPrivateShare()
                    ->getCreate());
                $featureBlockDto->setPrivateShare($permissionValueDto);

                $permissionValueDto = new PermissionValueDto();
                $permissionValueDto->setCreate($featurePublicShare->getCreate());
                $permissionValueDto->setView($featurePublicShare->getView());
                $featureBlockDto->setPublicShare($permissionValueDto);

                $permissionValueDto = new PermissionValueDto();
                $permissionValueDto->setCreate($featureTeamsShare->getCreate());
                $permissionValueDto->setView($featureTeamsShare->getView());
                $featureBlockDto->setTeamsShare($permissionValueDto);
            }
            if ($featureId == SynapseConstant::REFERRAL_FEATURE_ID) {
                $featureBlockDto->setReceiveReferrals($feature->getReceiveReferrals());
            }
            $featureBlocks[] = $featureBlockDto;
        }

        return $featureBlocks;
    }

    /**
     * Method for creating the OrgPermissionSet
     *
     * @param OrgPermissionSetDto $orgPermissionSetDto
     * @param boolean $copyFlag
     * @return OrgPermissionSetDto
     * @throws SynapseValidationException
     */
    public function createOrgPermissionset(OrgPermissionSetDto $orgPermissionSetDto, $copyFlag)
    {
        $orgPermissionSet = new OrgPermissionset();
        $organizationId = $orgPermissionSetDto->getOrganizationId();
        $courseAccess = $orgPermissionSetDto->getCoursesAccess();

        $organization = $this->organizationRepository->find($organizationId);
        if (!$organization) {
            throw new SynapseValidationException('Invalid Organization');
        }

        $orgPermissionSet->setPermissionsetName($orgPermissionSetDto->getPermissionTemplateName());
        $orgPermissionSet->setIsArchived(NULL);
        $orgPermissionSet->setOrganization($organization);
        if ($orgPermissionSetDto->getAccessLevel()->getAggregateOnly()) {
            $orgPermissionSet->setAccesslevelAgg(true);
            $orgPermissionSet->setAccesslevelIndAgg(false);
        } else {
            $orgPermissionSet->setAccesslevelAgg(false);
            $orgPermissionSet->setAccesslevelIndAgg(true);
        }
        if (!$copyFlag) {
            $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
            $this->rbacManager->checkAccessToOrganization($organizationId);
        }
        $orgPermissionSet->setViewCourses($courseAccess->getViewCourses());
        $orgPermissionSet->setCreateViewAcademicUpdate($courseAccess->getCreateViewAcademicUpdate());
        $orgPermissionSet->setViewAllAcademicUpdateCourses($courseAccess->getViewAllAcademicUpdateCourses());
        $orgPermissionSet->setViewAllFinalGrades($courseAccess->getViewAllFinalGrades());

        $orgPermissionSet->setIntentToLeave($orgPermissionSetDto->getIntentToLeave());
        $orgPermissionSet->setRiskIndicator($orgPermissionSetDto->getRiskIndicator());
        $orgPermissionSet->setRetentionCompletion($orgPermissionSetDto->getRetentionCompletion());

        $orgPermissionSet->setCurrentFutureIsq($orgPermissionSetDto->getCurrentFutureIsq());
        $errors = $this->validator->validate($orgPermissionSet);

        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();
            throw new SynapseValidationException($errorsString);
        }

        $this->orgPermissionsetRepository->persist($orgPermissionSet, false);

        $features = $orgPermissionSetDto->getFeatures();
        $ebiBlocks = $orgPermissionSetDto->getProfileBlocks();
        $ispBlocks = $orgPermissionSetDto->getIsp();
        $isqBlocks = $orgPermissionSetDto->getIsq();
        $surveyBlocks = $orgPermissionSetDto->getSurveyBlocks();
        $reportsAccess = $orgPermissionSetDto->getReportsAccess();
        $tools = $orgPermissionSetDto->getTools();

        if (count($features) > 0 && $orgPermissionSet->getAccesslevelIndAgg()) {
            $this->createOrgFeaturesPermission($features, $orgPermissionSet, $copyFlag);
        } else {
            $orgPermissionSetDto->setFeatures([]);
        }

        $this->createOrgDataBlocks($ebiBlocks, $orgPermissionSet);
        $this->createOrgDataBlocks($surveyBlocks, $orgPermissionSet);
        $this->createReportAccess($reportsAccess, $orgPermissionSet);
        $this->insertToolsIntoToolPermissions($tools, $orgPermissionSet);

        //Creating ISP Permissions
        $this->createIspBlock($ispBlocks, $orgPermissionSet);

        //Creating ISQ Permissions
        $this->createIsqBlock($isqBlocks, $orgPermissionSet);

        $this->orgPermissionsetRepository->flush();
        $orgPermissionSetDto->setPermissionTemplateId($orgPermissionSet->getId());
        return $orgPermissionSetDto;
    }

    public function getActivePermissionset($orgId)
    {
        $this->rbacManager = $this->container->get(OrgPermissionsetConstant::RBC_MANAGER);
        $this->rbacManager->checkAccessToOrganization($orgId);
        $this->logger->debug(">>>> Get Active Permission Set by Organization Id " . $orgId);

        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_PERMISSIONSET_REPO);
        $permissionset = $this->orgPermissionsetRepository->getActivePermissionset($orgId);
        $responseArray = array();
        if ($permissionset) {
            foreach ($permissionset as $pset) {
                $temp = array();
                $temp['id'] = $pset->getId();
                $temp['permissionset_name'] = $pset->getPermissionsetName();
                array_push($responseArray, $temp);
            }
        }
        $this->logger->info(">>>> Get Active Permission Set by Organization Id ");
        return $responseArray;
    }

    /**
     * Editing a permissionset
     *
     * @param OrgPermissionSetDto $orgPermissionSetDto
     * @return OrgPermissionSetDto
     * @throws SynapseValidationException
     */
    public function updateOrgPermissionset(OrgPermissionSetDto $orgPermissionSetDto)
    {
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->rbacManager->checkAccessToOrganization($orgPermissionSetDto->getOrganizationId());

        $orgPermissionSet = $this->orgPermissionsetRepository->find($orgPermissionSetDto->getPermissionTemplateId());
        if (!$orgPermissionSet) {
            throw new SynapseValidationException("Permissionset not found");
        }

        $orgPermissionSet->setPermissionsetName($orgPermissionSetDto->getPermissionTemplateName());
        $orgPermissionSet->setIsArchived(NULL);

        if ($orgPermissionSetDto->getAccessLevel()->getAggregateOnly()) {
            $orgPermissionSet->setAccesslevelAgg(true);
            $orgPermissionSet->setAccesslevelIndAgg(false);
        } else {
            $orgPermissionSet->setAccesslevelAgg(false);
            $orgPermissionSet->setAccesslevelIndAgg(true);
        }

        $orgPermissionSet->setViewCourses($orgPermissionSetDto->getCoursesAccess()
            ->getViewCourses());
        $orgPermissionSet->setCreateViewAcademicUpdate($orgPermissionSetDto->getCoursesAccess()
            ->getCreateViewAcademicUpdate());
        $orgPermissionSet->setViewAllAcademicUpdateCourses($orgPermissionSetDto->getCoursesAccess()
            ->getViewAllAcademicUpdateCourses());
        $orgPermissionSet->setViewAllFinalGrades($orgPermissionSetDto->getCoursesAccess()
            ->getViewAllFinalGrades());

        $orgPermissionSet->setIntentToLeave($orgPermissionSetDto->getIntentToLeave());
        $orgPermissionSet->setRiskIndicator($orgPermissionSetDto->getRiskIndicator());
        $orgPermissionSet->setRetentionCompletion($orgPermissionSetDto->getRetentionCompletion());
        $orgPermissionSet->setCurrentFutureIsq($orgPermissionSetDto->getCurrentFutureIsq());

        $errors = $this->validator->validate($orgPermissionSet);

        if (count($errors) > 0) {
            $errorsString = $errors[0]->getMessage();
            throw new SynapseValidationException($errorsString);
        }

        $features = $orgPermissionSetDto->getFeatures();
        $ebiBlocks = $orgPermissionSetDto->getProfileBlocks();
        $ispBlocks = $orgPermissionSetDto->getIsp();
        $isqBlocks = $orgPermissionSetDto->getIsq();
        $surveyBlocks = $orgPermissionSetDto->getSurveyBlocks();
        $reportsAccess = $orgPermissionSetDto->getReportsAccess();
        $tools = $orgPermissionSetDto->getTools();
        //Creating Permission Set Feature

        if ($orgPermissionSet->getAccesslevelIndAgg()) {
            $this->editOrgFeaturesPermission($features, $orgPermissionSet);
        } else {

            //Remove the features
            $orgFeaturePermissions = $this->orgPermissionsetFeaturesRepository->findBy([
                'orgPermissionset' => $orgPermissionSet
            ]);

            if ($orgFeaturePermissions) {
                foreach ($orgFeaturePermissions as $orgFeaturePermission) {
                    $this->orgPermissionsetFeaturesRepository->remove($orgFeaturePermission);
                }
            }
            $orgPermissionSetDto->setFeatures([]);
        }

        //Creating Data Blocks Permission

        $this->createOrgDataBlocks($ebiBlocks, $orgPermissionSet, 'update');
        $this->createOrgDataBlocks($surveyBlocks, $orgPermissionSet, 'update');
        $this->createReportAccess($reportsAccess, $orgPermissionSet, 'update');
        $this->updatePermissionsetTools($tools, $orgPermissionSet);

        //Creating ISP Permissions
        $this->editIspBlock($ispBlocks, $orgPermissionSet);

        //creating ISQ Permissions
        $this->editIsqBlock($isqBlocks, $orgPermissionSet);
        $this->orgPermissionsetRepository->flush();
        $orgPermissionSetDto->setPermissionTemplateId($orgPermissionSet->getId());
        return $orgPermissionSetDto;
    }


    private function editIsqBlock($isqBlocks, $orgPermissionSet)
    {
        $surveyRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:SurveyLang");
        $organization = $orgPermissionSet->getOrganization();
        if (count($isqBlocks) > 0) {
            foreach ($isqBlocks as $isqBlock) {

                $orgQuestionId = $isqBlock->getId();
                $orgQuestion = $this->orgQuestionRepository->find($orgQuestionId);
                if (!$orgQuestion) {
                    throw new ValidationException([
                        "ISQ Not Found"
                    ], "ISQ Not Found", OrgPermissionsetConstant::ERROR_KEY);
                }

                $isqPermission = $this->orgPermissionsetQuestionRepository->findOneBy([
                    'orgQuestion' => $orgQuestion,
                    OrgPermissionsetConstant::ORG_PERMISSION_SET => $orgPermissionSet
                ]);

                if ($isqPermission && !$isqBlock->getBlockSelection()) {
                    $this->orgPermissionsetQuestionRepository->remove($isqPermission);
                } elseif (!$isqPermission && $isqBlock->getBlockSelection()) {

                    $survey = $surveyRepo->findOneBySurvey($isqBlock->getSurveyId());
                    if (!$survey) {
                        throw new ValidationException([
                            "ISQ Survey Not Found"
                        ], "ISQ Survey Not Found", OrgPermissionsetConstant::ERROR_KEY);
                    }

                    $isqPermission = new OrgPermissionsetQuestion();
                    $isqPermission->setOrganization($organization);
                    $isqPermission->setOrgQuestion($orgQuestion);
                    $isqPermission->setOrgPermissionset($orgPermissionSet);
                    $isqPermission->setSurvey($survey->getSurvey());
                    $isqPermission->setCohortCode($isqBlock->getCohortId());
                    $this->orgPermissionsetQuestionRepository->persist($isqPermission, false);
                }
            }
        }
    }

    /**
     * Get Permission Set by Id
     *
     * @param int $id
     * @return OrgPermissionset
     * @throws SynapseValidationException
     */
    public function getPermissionSetById($id)
    {
        $permissionSet = $this->orgPermissionsetRepository->find($id);

        if (!$permissionSet) {
            $this->logger->error(" OrgPermissionset Service - getPermissionSetById - " . OrgPermissionsetConstant::ERROR_PERMISSIONSET_NOT_FOUND);
            throw new SynapseValidationException('Permissionset Not Found');
        }
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->rbacManager->checkAccessToOrganization($permissionSet->getOrganization()->getId());

        return $permissionSet;
    }

    /**
     * Gets permissionset information for the specified permissionset ID
     *
     * @param int $orgPermissionSetId
     * @return OrgPermissionSetDto
     * @throws SynapseValidationException
     */
    public function getPermissionSetsDataById($orgPermissionSetId)
    {
        $orgPermissionSet = $this->orgPermissionsetRepository->find($orgPermissionSetId);
        if (empty($orgPermissionSet)) {
            throw new SynapseValidationException('Permissionset Not Found');
        }
        $organizationId = $orgPermissionSet->getOrganization()->getId();
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->rbacManager->checkAccessToOrganization($organizationId);

        $organizationLangObject = $this->organizationLangRepository->findOneBy(array('organization' => $organizationId));
        if (empty($organizationLangObject)) {
            throw new SynapseValidationException('OrganizationLang not found');
        }
        $organizationLangId = $organizationLangObject->getLang()->getId();
        $permissionSetInformation = $this->orgPermissionsetRepository->getDatablockInformationByPermissionsetId($orgPermissionSetId, $organizationLangId);
        if (empty($permissionSetInformation)) {
            throw new SynapseValidationException('Permissionset Not Found');
        }
        $permissionSetDto = $this->buildPermissionTemplateGraph($permissionSetInformation[0]);
        return $permissionSetDto;
    }

    /**
     * Gets feature information based on the permissionset ID and language ID
     *
     * @param int $permissionSetId
     * @param int $languageId
     * @return array
     */
    private function getFeatures($permissionSetId, $languageId)
    {
        $featureDataBlockResponse = array();
        $orgPermissionFeatures = $this->orgPermissionsetFeaturesRepository->getFeaturesByPermissionSet($permissionSetId, $languageId);

        foreach ($orgPermissionFeatures as $orgPermissionSetFeature) {
            $featureBlockDto = new FeatureBlockDto();
            $featureBlockDto->setId($orgPermissionSetFeature['feature']['id']);
            $featureBlockDto->setName($orgPermissionSetFeature['feature']['name']);

            if ($orgPermissionSetFeature['feature']['id'] == 1) {
                $featureReferralBlockDto = new FeatureBlockDto();
                $permissionValueDto = $this->createPermissionValueDTO($orgPermissionSetFeature['privateCreate']);
                $featureReferralBlockDto->setPrivateShare($permissionValueDto);

                $permissionValueDto = $this->createPermissionValueDTO($orgPermissionSetFeature['publicCreate'], $orgPermissionSetFeature['publicView']);
                $featureReferralBlockDto->setPublicShare($permissionValueDto);

                $permissionValueDto = $this->createPermissionValueDTO($orgPermissionSetFeature['teamCreate'], $orgPermissionSetFeature['teamView']);
                $featureReferralBlockDto->setTeamsShare($permissionValueDto);
                $featureBlockDto->setDirectReferral($featureReferralBlockDto);

                $featureReferralBlockDto = new FeatureBlockDto();
                $permissionValueDto = $this->createPermissionValueDTO($orgPermissionSetFeature['reasonReferralPrivateCreate']);
                $featureReferralBlockDto->setPrivateShare($permissionValueDto);

                $permissionValueDto = $this->createPermissionValueDTO($orgPermissionSetFeature['reasonReferralPublicCreate'], $orgPermissionSetFeature['reasonReferralPublicView']);
                $featureReferralBlockDto->setPublicShare($permissionValueDto);

                $permissionValueDto = $this->createPermissionValueDTO($orgPermissionSetFeature['reasonReferralTeamCreate'], $orgPermissionSetFeature['reasonReferralTeamView']);
                $featureReferralBlockDto->setTeamsShare($permissionValueDto);
                $featureBlockDto->setReasonRoutedReferral($featureReferralBlockDto);
            } else {
                $permissionValueDto = $this->createPermissionValueDTO($orgPermissionSetFeature['privateCreate']);
                $featureBlockDto->setPrivateShare($permissionValueDto);

                $permissionValueDto = $this->createPermissionValueDTO($orgPermissionSetFeature['publicCreate'], $orgPermissionSetFeature['publicView']);
                $featureBlockDto->setPublicShare($permissionValueDto);

                $permissionValueDto = $this->createPermissionValueDTO($orgPermissionSetFeature['teamCreate'], $orgPermissionSetFeature['teamView']);
                $featureBlockDto->setTeamsShare($permissionValueDto);
            }
            $featureBlockDto->setReceiveReferrals($orgPermissionSetFeature['receiveReferral']);
            $featureDataBlockResponse[] = $featureBlockDto;
        }

        return $featureDataBlockResponse;
    }

    private function getFeaturesList($permissionSet, $lang)
    {
        $featureDataBlockResponse = array();
        $activeFeatures = $this->orgPermissionsetFeaturesRepository->listOrPermissionsetFeaturesAll($permissionSet, $lang);

        if (count($activeFeatures) > 0) {
            foreach ($activeFeatures as $activeFeature) {

                $featureBlockDto = new FeatureBlockDto();
                $featureBlockDto->setId($activeFeature['feature_id']);
                $featureBlockDto->setName($activeFeature['feature_name']);
                /**
                 * for referal only
                 */
                $orgPermissionFeature = $activeFeature[0];
                if ($activeFeature['feature_id'] == 1) {
                    $featureBlockDto->setReceiveReferrals($orgPermissionFeature->getReceiveReferral());
                }

                if ($this->lastUpdated < $orgPermissionFeature->getModifiedAt()) {

                    $this->lastUpdated = $orgPermissionFeature->getModifiedAt();
                }
                if ($activeFeature['feature_id'] == 1) {

                    $featureReferralBlockDto = new FeatureBlockDto();
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($orgPermissionFeature->getPrivateCreate());
                    $featureReferralBlockDto->setPrivateShare($pValueDto);

                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($orgPermissionFeature->getPublicCreate());
                    $pValueDto->setView($orgPermissionFeature->getPublicView());
                    $featureReferralBlockDto->setPublicShare($pValueDto);

                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($orgPermissionFeature->getTeamCreate());
                    $pValueDto->setView($orgPermissionFeature->getTeamView());
                    $featureReferralBlockDto->setTeamsShare($pValueDto);
                    $featureBlockDto->setDirectReferral($featureReferralBlockDto);

                    $featureReferralBlockDto = new FeatureBlockDto();
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($orgPermissionFeature->getReasonReferralPrivateCreate());
                    $featureReferralBlockDto->setPrivateShare($pValueDto);

                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($orgPermissionFeature->getReasonReferralPublicCreate());
                    $pValueDto->setView($orgPermissionFeature->getReasonReferralPublicView());
                    $featureReferralBlockDto->setPublicShare($pValueDto);

                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($orgPermissionFeature->getReasonReferralTeamCreate());
                    $pValueDto->setView($orgPermissionFeature->getReasonReferralTeamView());
                    $featureReferralBlockDto->setTeamsShare($pValueDto);
                    $featureBlockDto->setReasonRoutedReferral($featureReferralBlockDto);
                } else {
                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($orgPermissionFeature->getPrivateCreate());
                    $featureBlockDto->setPrivateShare($pValueDto);

                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($orgPermissionFeature->getPublicCreate());
                    $pValueDto->setView($orgPermissionFeature->getPublicView());
                    $featureBlockDto->setPublicShare($pValueDto);

                    $pValueDto = new PermissionValueDto();
                    $pValueDto->setCreate($orgPermissionFeature->getTeamCreate());
                    $pValueDto->setView($orgPermissionFeature->getTeamView());
                    $featureBlockDto->setTeamsShare($pValueDto);
                }
                $featureDataBlockResponse[] = $featureBlockDto;
            }
        }
        return $featureDataBlockResponse;
    }

    /**
     * Create permission value DTO
     *
     * @param boolean $createPermission
     * @param boolean $viewPermission
     * @return PermissionValueDto
     */
    private function createPermissionValueDTO($createPermission, $viewPermission = null)
    {
        $permissionValueDto = new PermissionValueDto();
        $permissionValueDto->setCreate($createPermission);
        $permissionValueDto->setView($viewPermission);

        return $permissionValueDto;
    }

    /**
     * Get ISPs for organization
     *
     * @param int $permissionsetId
     * @param int $organizationId
     * @return IspBlockDto[]
     */
    protected function getIsps($permissionsetId, $organizationId)
    {
        $ISPsArray = [];
        $ISPsInformation = $this->orgPermissionsetMetadataRepository->getIspsByPermissionSet($permissionsetId, $organizationId);

        foreach ($ISPsInformation as $ISPInformation) {
            $IspBlockDto = new IspBlockDto();
            $IspBlockDto->fromArray($ISPInformation);
            $ISPsArray[] = $IspBlockDto;
        }

        return $ISPsArray;
    }

    /**
     * Get list of isp data blocks based on PermissionSet and OrganizationId
     *
     * @param OrgPermissionset $orgPermissionSet
     * @param int $organizationId
     * @return Array
     */
    protected function getIspsList($orgPermissionSet, $organizationId)
    {
        $orgPermissionSetId = $orgPermissionSet->getId();
        $isps = $this->orgPermissionsetMetadataRepository->getIspsByPermissionSet($orgPermissionSetId, $organizationId);
        $ispDataBlockResponse = [];

        foreach ($isps as $isp) {
            $ispBlockDto = new IspBlockDto();
            $ispBlockDto->setId($isp['ispId']);
            $ispBlockDto->setItemLabel($isp['ispLabel']);
            $ispBlockDto->setBlockSelection(true);
            if ($this->lastUpdated < $isp['modifiedAt']) {
                $this->lastUpdated = $isp['modifiedAt'];
            }
            $ispDataBlockResponse[] = $ispBlockDto;
        }
        return $ispDataBlockResponse;
    }

    /**
     * Get ISQ for the organization
     *
     * @param int $permissionsetId
     * @param int $organizationId
     * @return array
     */
    private function getIsqs($permissionsetId, $organizationId)
    {
        $ISQsInformation = $this->orgPermissionsetQuestionRepository->getIsqsByPermissionSet($permissionsetId, $organizationId);

        $ISQArray = [];
        foreach ($ISQsInformation as $ISQInformation) {
            $isqBlockDto = new IsqBlockDto();
            $isqBlockDto->fromArray($ISQInformation);
            $ISQArray[] = $isqBlockDto;
        }

        return $ISQArray;
    }

    /**
     * Get permission set data for the organization
     *
     * @param int $organizationId
     * @return array
     * @throws SynapseValidationException
     */
    public function getPermissionsetsByOrganizationId($organizationId)
    {
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->rbacManager->checkAccessToOrganization($organizationId);

        $organization = $this->organizationRepository->find($organizationId);
        if (empty($organization)) {
            throw new SynapseValidationException('Organization not found');
        }

        $orgPermissionsets = $this->orgPermissionsetRepository->findBy([
            'organization' => $organization
        ]);

        if (empty($orgPermissionsets)) {
            throw new SynapseValidationException('orgPermissionset not found');
        }
        $response = [];
        $response['organization_id'] = $organizationId;
        $response['permission_template_count'] = count($orgPermissionsets);
        $response['permission_template_last_updated'] = null;
        $response['permission_templates'] = [];

        $this->lastUpdated = null;

        $organizationLang = $this->organizationLangRepository->findOneBy(array(
                'organization' => $organizationId
            )
        );

        if (empty($organizationLang)) {
            throw new SynapseValidationException('OrganizationLang not found');
        }

        $organizationLangId = $organizationLang->getLang()->getId();

        if ($orgPermissionsets && count($orgPermissionsets) > 0) {
            foreach ($orgPermissionsets as $orgPermissionset) {
                $orgPermissionsetId = $orgPermissionset->getId();

                $orgPermissionsetName = $orgPermissionset->getPermissionsetName();
                $permissionSetAccesslevelAggregate = (bool)$orgPermissionset->getAccesslevelAgg();
                $permissionSetAccesslevelIndividualAggregate = (bool)$orgPermissionset->getAccesslevelIndAgg();
                $permissionSetCreateViewAcademicUpdate = (bool)$orgPermissionset->getCreateViewAcademicUpdate();
                $permissionSetViewAllAcademicUpdateCourses = (bool)$orgPermissionset->getViewAllAcademicUpdateCourses();
                $permissionSetViewAllFinalGrades = (bool)$orgPermissionset->getViewAllFinalGrades();
                $permissionSetViewCourses = (bool)$orgPermissionset->getViewCourses();
                $permissionSetRetentionCompletion = (bool)$orgPermissionset->getRetentionCompletion();
                $permissionSetRiskIndicator = (bool)$orgPermissionset->getRiskIndicator();
                $permissionSetIntentToLeave = (bool)$orgPermissionset->getIntentToLeave();
                $permissionSetCurrentFutureIsq = (bool)$orgPermissionset->getCurrentFutureIsq();
                $permissionSetModifiedAt = $orgPermissionset->getModifiedAt();

                $orgPermissionSetDto = new OrgPermissionSetDto();
                $orgPermissionSetDto->setPermissionTemplateId($orgPermissionsetId);
                $orgPermissionSetDto->setPermissionTemplateName($orgPermissionsetName);

                $accessLevelDto = new AccessLevelDto();
                $accessLevelDto->setAggregateOnly($permissionSetAccesslevelAggregate);
                $accessLevelDto->setIndividualAndAggregate($permissionSetAccesslevelIndividualAggregate);
                $orgPermissionSetDto->setAccessLevel($accessLevelDto);

                $coursesAccessDto = new CoursesAccessDto();
                $coursesAccessDto->setCreateViewAcademicUpdate($permissionSetCreateViewAcademicUpdate);
                $coursesAccessDto->setViewAllAcademicUpdateCourses($permissionSetViewAllAcademicUpdateCourses);
                $coursesAccessDto->setViewAllFinalGrades($permissionSetViewAllFinalGrades);
                $coursesAccessDto->setViewCourses($permissionSetViewCourses);

                $orgPermissionSetDto->setCoursesAccess($coursesAccessDto);
                $orgPermissionSetDto->setRiskindicator($permissionSetRiskIndicator);
                $orgPermissionSetDto->setIntentToleave($permissionSetIntentToLeave);
                $orgPermissionSetDto->setCurrentFutureIsq($permissionSetCurrentFutureIsq);
                $orgPermissionSetDto->setRetentionCompletion($permissionSetRetentionCompletion);

                if ($this->lastUpdated < $permissionSetModifiedAt) {

                    $this->lastUpdated = $permissionSetModifiedAt;
                    $orgPermissionSetDto->setLastUpdated($this->lastUpdated);
                }

                $profileOrgDataBlocks = $this->getOrgDataBlocksList('profile', $orgPermissionset, $organizationLangId);
                $surveyOrgDataBlocks = $this->getOrgDataBlocksList('survey', $orgPermissionset, $organizationLangId);

                $orgPermissionSetDto->setProfileBlocks($profileOrgDataBlocks);
                $orgPermissionSetDto->setSurveyBlocks($surveyOrgDataBlocks);

                $reportResponse = $this->getReportAccessSelection($orgPermissionset, $organizationId);
                $orgPermissionSetDto->setReportsAccess($reportResponse);

                $ispDataBlockResponse = $this->getIspsList($orgPermissionset, $organizationId);
                $orgPermissionSetDto->setIsp($ispDataBlockResponse);


                $isqDataBlockResponse = $this->getIsqs($orgPermissionsetId, $organizationId);
                $orgPermissionSetDto->setIsq($isqDataBlockResponse);


                $tools = $this->getToolSelection($orgPermissionsetId, $organizationId);
                $orgPermissionSetDto->setTools($tools);

                $featureDataBlockResponse = $this->getFeaturesList($orgPermissionset, $organizationLangId);

                $orgPermissionSetDto->setFeatures($featureDataBlockResponse);

                $response['permission_templates'][] = $orgPermissionSetDto;

            }
            $response = $this->setLastUpdateDate($response);
        }
        return $response;
    }

    /**
     * This function will call from list by orgs
     *
     * @param OrgPermissionset $orgPermissionset
     * @return ReportSelectionDto[]
     */
    protected function getReportAccessSelection($orgPermissionset, $organization)
    {
        $reports = $this->orgReportPermissionsRepository->getReportsPermissionSet($orgPermissionset->getId(), $organization);

        $reportResponse = array();
        if ($reports) {
            foreach ($reports as $report) {
                if ($report['selection']) {
                    $reportSelectionDto = new ReportSelectionDto();
                    $reportSelectionDto->setId($report['id']);
                    $reportSelectionDto->setName($report['name']);
                    $reportSelectionDto->setShortCode($report['shortCode']);
                    $reportSelectionDto->setSelection(true);

                    $reportResponse[] = $reportSelectionDto;
                }
            }
        }
        return $reportResponse;
    }

    /**
     * Get Profile or survey data blocks for organization
     *
     * @param int $organizationId
     * @param string $type
     * @return array
     * @throws SynapseValidationException
     */
    public function getDataBlocks($organizationId, $type)
    {
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $this->rbacManager->checkAccessToOrganization($organizationId);
        $response = array();

        $organization = $this->organizationRepository->find($organizationId);

        if (empty($organization)) {
            throw new SynapseValidationException('Organization Not Found.');
        }

        $organizationLang = $this->organizationLangRepository->findOneBy(array(
            'organization' => $organizationId
        ));

        if (empty($organizationLang)) {
            throw new SynapseValidationException('Organization Language not found.');
        }
        if ($type == 'profile') {
            $ISPs = $this->orgMetadataRepository->getProfile($organizationId);
            $response['isp'] = $ISPs;
        }
        $organizationLangId = $organizationLang->getLang()->getId();
        $dataBlocks = $this->permissionsetService->getDataBlocksByType($organizationLangId, $type);
        $response['organization_id'] = $organizationId;
        $response['lang_id'] = $organizationLangId;

        $response['data_block_type'] = "profile";
        $response['data_blocks'] = $dataBlocks['data_blocks'];

        return $response;
    }

    protected function createReportAccess($reports, $orgPermissionSet, $type = 'insert')
    {

        if ($type == 'insert' && (count($reports) > 0)) {
            foreach ($reports as $report) {
                if ($report->getSelection()) {
                    $this->addReportPermission($report, $orgPermissionSet);

                }
            }
        } else {
            $this->updateReportAccess($reports, $orgPermissionSet);
        }
    }

    protected function addReportPermission($report, $orgPermissionSet)
    {
        $this->orgReportPermissionsRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_PERMISSIONSET_REPORTACCESS_REPO);
        $this->reportsRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::REPORTS_REPO);

        $reportPermission = new OrgReportPermissions();
        $reportId = $report->getId();
        $reportData = $this->reportsRepository->find($reportId);
        $errorMsg = OrgPermissionsetConstant::ERROR_REPORT_NOT_FOUND;
        $this->validateBlock($reportData, $reportId, $errorMsg);
        $reportPermission->setReport($reportData);
        $reportPermission->setOrganization($orgPermissionSet->getOrganization());
        $reportPermission->setOrgPermissionset($orgPermissionSet);
        $this->orgReportPermissionsRepository->persist($reportPermission, false);
    }

    protected function updateReportAccess($reports, $orgPermissionSet)
    {
        $this->orgReportPermissionsRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_PERMISSIONSET_REPORTACCESS_REPO);
        $this->reportsRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::REPORTS_REPO);
        if (count($reports) > 0) {

            foreach ($reports as $report) {
                /**
                 * Update
                 */
                $reportPerm = $this->orgReportPermissionsRepository->findOneBy([
                    OrgPermissionsetConstant::ORG_PERMISSION_SET => $orgPermissionSet,
                    'report' => $report->getId()
                ]);
                if ($reportPerm && !$report->getSelection()) {
                    $this->orgReportPermissionsRepository->remove($reportPerm);
                } elseif (!$reportPerm && $report->getSelection()) {
                    $this->addReportPermission($report, $orgPermissionSet);
                }
            }
        }
    }

    protected function createOrgDataBlocks($blocks, $orgPermissionSet, $type = 'insert')
    {
        $this->orgPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_PERMISSIONSET_DATABLOCK_REPO);
        $this->dataBlockMasterRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::DATABLOCK_MASTER_REPO);
        if ($type == 'insert' && (count($blocks) > 0)) {
            foreach ($blocks as $block) {
                if ($block->getBlockSelection()) {
                    $ebiDataBlock = new OrgPermissionsetDatablock();
                    $blockId = $block->getBlockId();
                    $dataBlock = $this->dataBlockMasterRepository->find($blockId);
                    $errorMsg = OrgPermissionsetConstant::ERROR_DATABLOCK_NOT_FOUND;
                    $this->validateBlock($dataBlock, $blockId, $errorMsg);
                    $ebiDataBlock->setBlockType($dataBlock->getBlockType());
                    $ebiDataBlock->setDatablock($dataBlock);
                    $ebiDataBlock->setOrganization($orgPermissionSet->getOrganization());
                    $ebiDataBlock->setOrgPermissionset($orgPermissionSet);
                    $this->orgPermissionsetDatablockRepository->persist($ebiDataBlock, false);
                }
            }
        } else {
            $this->createOrgDataBlocksUpdate($blocks, $orgPermissionSet);
        }
    }

    protected function createOrgDataBlocksUpdate($blocks, $orgPermissionSet)
    {
        $this->orgPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_PERMISSIONSET_DATABLOCK_REPO);
        $this->dataBlockMasterRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::DATABLOCK_MASTER_REPO);
        if (count($blocks) > 0) {

            foreach ($blocks as $block) {
                /**
                 * Update
                 */
                $ebiDataBlock = $this->orgPermissionsetDatablockRepository->findOneBy([
                    OrgPermissionsetConstant::ORG_PERMISSION_SET => $orgPermissionSet,
                    'datablock' => $block->getBlockId()
                ]);
                if ($ebiDataBlock && !$block->getBlockSelection()) {
                    $this->orgPermissionsetDatablockRepository->remove($ebiDataBlock);
                } elseif (!$ebiDataBlock && $block->getBlockSelection()) {
                    $ebiDataBlock = new OrgPermissionsetDatablock();
                    $blockId = $block->getBlockId();
                    $dataBlock = $this->dataBlockMasterRepository->find($blockId);
                    $errorMsg = OrgPermissionsetConstant::ERROR_DATABLOCK_NOT_FOUND;
                    $this->validateBlock($dataBlock, $blockId, $errorMsg);
                    $ebiDataBlock->setBlockType($dataBlock->getBlockType());
                    $ebiDataBlock->setDatablock($dataBlock);
                    $ebiDataBlock->setOrganization($orgPermissionSet->getOrganization());
                    $ebiDataBlock->setOrgPermissionset($orgPermissionSet);
                    $this->orgPermissionsetDatablockRepository->persist($ebiDataBlock, false);
                }
            }
        }
    }

    private function validateBlock($dataBlock, $blockId, $errorMsg)
    {
            if (!$dataBlock) {
            throw new ValidationException([
                $errorMsg . $blockId
            ], $errorMsg, OrgPermissionsetConstant::ERROR_KEY);
        }
    }

    /**
     *
     * @param OrgPermissionset $orgPermissionSet
     * @return BlockDto[] - This is for listing the permissionset by Org
     */
    protected function getOrgDataBlocksList($type, $orgPermissionSet, $langid)
    {
        $this->orgPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_PERMISSIONSET_DATABLOCK_REPO);
        $blocks = $this->orgPermissionsetDatablockRepository->getOrgDatablockList($type, $orgPermissionSet, $langid);
        $profileDataBlocksResponse = array();
        if (count($blocks) > 0) {
            foreach ($blocks as $block) {
                $blockDto = new BlockDto();
                $blockDto->setBlockId($block['datablock_id']);
                $blockDto->setBlockName($block['datablock_name']);
                $blockDto->setBlockSelection(true);
                $blockDto->setLastUpdated($block['modified_at']);
                if ($this->lastUpdated < $blockDto->getLastUpdated()) {
                    $this->lastUpdated = $blockDto->getLastUpdated();
                }
                $profileDataBlocksResponse[] = $blockDto;
            }
        }
        return $profileDataBlocksResponse;
    }

    protected function setLastUpdateDate($response)
    {
        if (count($response[OrgPermissionsetConstant::PERMISSION_TEMPLATES]) > 0) {
            $response[OrgPermissionsetConstant::PERMISSION_TEMPLATE_LAST_UPDATED] = $this->lastUpdated;
            foreach ($response[OrgPermissionsetConstant::PERMISSION_TEMPLATES] as $pTemplate) {
                if ($response[OrgPermissionsetConstant::PERMISSION_TEMPLATE_LAST_UPDATED] < $pTemplate->getLastUpdated()) {
                    $response[OrgPermissionsetConstant::PERMISSION_TEMPLATE_LAST_UPDATED] = $pTemplate->getLastUpdated();
                }
            }
        }
        return $response;
    }

    /**
     *
     * @param array $ptInfo
     * @return OrgPermissionSetDto
     */
    protected function buildPermissionTemplateGraph(array $ptInfo)
    {
        // $permissionSet = $this->getPermissionSetById($ptInfo['permissionTemplateId'], true);
        $newInfo = $ptInfo;

        /**
         * Clear overridden elements.
         */
        unset($newInfo[OrgPermissionsetConstant::ACCESS_LEVEL]);
        unset($newInfo[OrgPermissionsetConstant::PROFILE_BLOCKS]);
        unset($newInfo[OrgPermissionsetConstant::SURVEY_BLOCKS]);

        $accessLevel = new AccessLevelDto();
        $accessLevel->fromArray($ptInfo[OrgPermissionsetConstant::ACCESS_LEVEL]);
        $newInfo[OrgPermissionsetConstant::ACCESS_LEVEL] = $accessLevel;

        $coursesAccess = new CoursesAccessDto();
        $coursesAccess->fromArray($ptInfo[OrgPermissionsetConstant::COURSES_ACCESS]);

        $newInfo['coursesAccess'] = $coursesAccess;
        foreach ($ptInfo[OrgPermissionsetConstant::PROFILE_BLOCKS] as $blockInfo) {
            $profileBlock = new BlockDto();
            $profileBlock->fromArray($blockInfo);
            $newInfo[OrgPermissionsetConstant::PROFILE_BLOCKS][] = $profileBlock;
        }

        foreach ($ptInfo[OrgPermissionsetConstant::SURVEY_BLOCKS] as $blockInfo) {
            $surveyBlock = new BlockDto();
            $surveyBlock->fromArray($blockInfo);
            $newInfo[OrgPermissionsetConstant::SURVEY_BLOCKS][] = $surveyBlock;
        }

        $psDto = new OrgPermissionSetDto();
        $psDto->fromArray($ptInfo);

        $permissionsetId = $ptInfo['permissionTemplateId'];
        $organizationId = $ptInfo['organizationId'];
        $organizationLangId = $ptInfo['organizationLangId'];

        $this->orgPermissionsetDatablockRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_PERMISSIONSET_DATABLOCK_REPO);
        $this->orgPermissionsetDatablockRepository->getOrgDataBlocks(OrgPermissionsetConstant::PROFILE, $permissionsetId);

        $features = $this->getFeatures($permissionsetId, $organizationLangId);
        $newInfo[OrgPermissionsetConstant::FEATURES] = $features;

        $reportAccess = $this->getReportAccess($permissionsetId, $organizationId);
        $newInfo['reportsAccess'] = $reportAccess;

        $tools = $this->retrieveOrgPermissionsetTools($permissionsetId, $organizationId);
        $newInfo['totalCount'] = count($tools);
        $newInfo['tools'] = $tools;

        $isps = $this->getIsps($permissionsetId, $organizationId);
        $newInfo['isp'] = $isps;

        $isqs = $this->getIsqs($permissionsetId, $organizationId);
        $newInfo['isq'] = $isqs;
        $psDto = new OrgPermissionSetDto();
        $psDto->fromArray($newInfo);

        return $psDto;
    }

    /**
     *
     * @param int $permissionsetId
     * @param int $organizationId
     * @return ReportSelectionDto[]
     */
    protected function getReportAccess($permissionsetId, $organizationId)
    {
        $reports = [];
        $this->orgReportPermissionsRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_PERMISSIONSET_REPORTACCESS_REPO);
        $reportsPermInfo = $this->orgReportPermissionsRepository->getReportsPermissionSet($permissionsetId, $organizationId);

        foreach ($reportsPermInfo as $info) {

            if ($info['shortCode'] == 'SUR-SSR') {
                continue;
            }

            if (($info['is_coordinator_report'] == 'y')) {
                continue;
            }

            $rsDto = new ReportSelectionDto();
            $rsDto->fromArray($info);
            $reports[] = $rsDto;
        }

        return $reports;
    }

    /**
     * Get Permission Set By User
     *
     * @param int $userId
     * @return array
     */
    public function getPermissionSetsByUser($userId)
    {
        $permissionTemplatesInformation = $this->orgPermissionsetRepository->getPermissionSetsDataByUser($userId);

        $permissionsetIds = [];

        $permissionTemplates = [];

        $organizationId = null;

        foreach ($permissionTemplatesInformation as $permissionTemplateInfo) {
            $permissionsetIds[] = $permissionTemplateInfo['permissionTemplateId'];
            $permissionSetsDto = $this->buildPermissionTemplateGraph($permissionTemplateInfo);
            $permissionTemplates[] = $permissionSetsDto;
        }

        if (!empty($permissionTemplatesInformation) && !empty($permissionTemplatesInformation[0])) {
            $organizationId = $permissionTemplatesInformation[0]['organizationId'];
        }

        $returnSet = [
            'organization_id' => $organizationId,
            'permission_template_count' => count($permissionTemplatesInformation),
            'permission_templates' => $permissionTemplates
        ];
        return $returnSet;
    }

    public function getProfileblockPermission($userId)
    {
        $this->logger->info(">>>> Get Profile  Block Permission");
        $profileBlock = array();
        $profileBlockSet = array();
        $resultProfileBlocks = array();
        $this->rbacManager = $this->container->get(OrgPermissionsetConstant::RBC_MANAGER);
        $accessMap = $this->rbacManager->getAccessMap($userId);

        if ($accessMap && isset($accessMap[OrgPermissionsetConstant::PROFILE_BLOCKS]) && count($accessMap[OrgPermissionsetConstant::PROFILE_BLOCKS]) > 0) {
            foreach ($accessMap[OrgPermissionsetConstant::PROFILE_BLOCKS] as $name => $profileBlocks) {
                if ($profileBlocks[OrgPermissionsetConstant::FIELD_VALUE] == '*') {
                    $profileBlock[OrgPermissionsetConstant::BLOCK_ID] = $profileBlocks['id'];
                    $profileBlock[OrgPermissionsetConstant::BLOCK_NAME] = $profileBlocks['name'];
                    $profileBlockSet[] = $profileBlock;
                }
            }
        } else {
            $profileBlockSet = $this->getProfileblockPermissionFromDB($userId);
        }
        $resultProfileBlocks['profile_blocks'] = $profileBlockSet;
        $this->logger->info(">>>> Get Profile  Block Permission");
        return $resultProfileBlocks;
    }

    public function getProfileblockPermissionFromDB($userId)
    {
        $this->logger->info(">>>> Get Profile  Block Permission from DB for USERID ");
        $profileBlock = array();
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_GROUP_FACULTY_REPO);
        $userGroupsPermission = $this->orgGroupFacultyRepository->getGroupByUserPermissionSet($userId);
        $profileBlockSet = array();
        $resultProfileBlocks = array();
        if ($userGroupsPermission && count($userGroupsPermission) > 0) {
            $resultProfileBlocks[OrgPermissionsetConstant::ORGANIZATION_ID] = $userGroupsPermission[0][OrgPermissionsetConstant::ORGANIZATION];
            $permissionSet = array_unique(array_column($userGroupsPermission, OrgPermissionsetConstant::PERMISSION_SET));
            foreach ($permissionSet as $ps) {
                $psData = $this->getPermissionSetsDataById($ps);
                if ($psData) {
                    $blocks = $psData->getProfileBlocks();
                    $profileBlockSet = $this->getAggrigateBlock($blocks, $profileBlockSet);
                }
            }
        }
        return $profileBlockSet;
    }

    private function getAggrigateBlock($blocks, $blockSet, $features = false)
    {
        $blockId = OrgPermissionsetConstant::BLOCK_ID;
        $blockName = OrgPermissionsetConstant::BLOCK_NAME;
        if ($features) {
            $blockId = 'id';
            $blockName = 'name';
        }

        foreach ($blocks as $pb) {
            $bloclSelection = ($features) ? true : $pb->getBlockSelection();

            if ($bloclSelection) {
                $id = ($features) ? $pb->getId() : $pb->getBlockId();
                $name = ($features) ? $pb->getName() : $pb->getBlockName();
                if (count($blockSet) == 0) {
                    $block[$blockId] = $id;
                    $block[$blockName] = $name;
                    $blockSet[] = $block;
                } else
                    if (!in_array($id, array_column($blockSet, $blockId))) {
                        $block[$blockId] = $id;
                        $block[$blockName] = $name;
                        $blockSet[] = $block;
                    }
            }
        }

        return $blockSet;
    }

    public function getSurveyBlocksPermission($userId)
    {
        $this->logger->info(">>>> Get Survey Blocks Permission for User Id ");
        $surveyBlock = array();
        $surveyBlockSet = array();
        $resultsurveyBlocks = array();
        $resultProfileBlocks = array();
        $this->rbacManager = $this->container->get(OrgPermissionsetConstant::RBC_MANAGER);
        $accessMap = $this->rbacManager->getAccessMap($userId);
        if ($accessMap && isset($accessMap[OrgPermissionsetConstant::SURVEY_BLOCKS]) && count($accessMap[OrgPermissionsetConstant::SURVEY_BLOCKS]) > 0) {
            foreach ($accessMap[OrgPermissionsetConstant::SURVEY_BLOCKS] as $name => $surveyBlocks) {
                if ($surveyBlocks[OrgPermissionsetConstant::FIELD_VALUE] == '*') {
                    $surveyBlock[OrgPermissionsetConstant::BLOCK_ID] = $surveyBlocks['id'];
                    $surveyBlock[OrgPermissionsetConstant::BLOCK_NAME] = $surveyBlocks['name'];
                    $surveyBlockSet[] = $surveyBlock;
                }
            }
        } else {
            $surveyBlockSet = $this->getSurveyBlocksPermissionFromDB($userId);
        }
        $resultsurveyBlocks['survey_blocks'] = $surveyBlockSet;
        $this->logger->info(">>>> Get Survey Blocks Permission for User Id ");
        return $resultsurveyBlocks;
    }

    public function getSurveyBlocksPermissionFromDB($userId)
    {
        $this->logger->info(">>>> Get Survey Blocks Permission From DB for User Id ");
        $surveyBlock = array();
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_GROUP_FACULTY_REPO);
        $userGroupsPermission = $this->orgGroupFacultyRepository->getGroupByUserPermissionSet($userId);
        $surveyBlockSet = array();
        $resultsurveyBlocks = array();
        if ($userGroupsPermission && count($userGroupsPermission) > 0) {
            $resultsurveyBlocks[OrgPermissionsetConstant::ORGANIZATION_ID] = $userGroupsPermission[0][OrgPermissionsetConstant::ORGANIZATION];
            $permissionSet = array_unique(array_column($userGroupsPermission, OrgPermissionsetConstant::PERMISSION_SET));
            foreach ($permissionSet as $ps) {
                $psData = $this->getPermissionSetsDataById($ps);
                if ($psData) {
                    $blocks = $psData->getSurveyBlocks();
                    $surveyBlockSet = $this->getAggrigateBlock($blocks, $surveyBlockSet);
                }
            }
        }
        $this->logger->info(">>>> Get Survey Blocks Permission From DB for User Id ");
        return $surveyBlockSet;
    }


    public function getFeaturesPermission($userId)
    {
        $this->logger->debug(">>>> Get Features Permission By User Id " . $userId);
        $this->rbacManager = $this->container->get(OrgPermissionsetConstant::RBC_MANAGER);
        $accessMap = $this->rbacManager->getAccessMap($userId);

        $featureBlock = array();
        $featureBlockSet = array();
        if ($accessMap && isset($accessMap[OrgPermissionsetConstant::FEATURES]) && count($accessMap[OrgPermissionsetConstant::FEATURES]) > 0) {
            foreach ($accessMap[OrgPermissionsetConstant::FEATURES] as $features) {
                $featureBlock['id'] = $features['id'];
                $featureBlock['name'] = $this->formatNameUpperCase($features['name']);
                $featureBlockSet[] = $featureBlock;
            }
        } else {
            $featureBlockSet = $this->getFeaturesPermissionFromDB($userId);
        }
        $resultfeatureBlocks[OrgPermissionsetConstant::FEATURES] = $featureBlockSet;
        $this->logger->info(">>>> Get Survey Blocks Permission From DB for User Id ");
        return $resultfeatureBlocks;
    }

    public function getFeaturesPermissionFromDB($userId)
    {
        $this->logger->debug(">>>> Get Features Permission From DB " . $userId);
        $featureBlock = array();
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_GROUP_FACULTY_REPO);
        $userGroupsPermission = $this->orgGroupFacultyRepository->getGroupByUserPermissionSet($userId);
        $featureBlockSet = array();
        $resultfeatureBlocks = array();
        if ($userGroupsPermission && count($userGroupsPermission) > 0) {
            $resultfeatureBlocks[OrgPermissionsetConstant::ORGANIZATION_ID] = $userGroupsPermission[0][OrgPermissionsetConstant::ORGANIZATION];
            $permissionSet = array_unique(array_column($userGroupsPermission, OrgPermissionsetConstant::PERMISSION_SET));
            foreach ($permissionSet as $ps) {
                $psData = $this->getPermissionSetsDataById($ps);
                if ($psData) {
                    $featureBlocks = $psData->getFeatures();
                    $featureBlockSet = $this->getAggrigateBlock($featureBlocks, $featureBlockSet, true);
                }
            }
        }
        $this->logger->info(">>>> Get Features Permission From DB ");
        return $featureBlockSet;
    }

    public function getAccessLevelPermission($userId)
    {
        $this->logger->debug(">>>> Get Access Level Permission  for User Id " . $userId);
        $this->rbacManager = $this->container->get(OrgPermissionsetConstant::RBC_MANAGER);
        $permissionTree = $this->rbacManager->getRbac()->getPermissions();

        $individualAndAggregate = false;
        $aggregateOnly = false;
        if ($permissionTree) {
            $individualAndAggregate = (isset($permissionTree[OrgPermissionsetConstant::INDIVIDUAL_AND_AGGREGATE]) && $permissionTree[OrgPermissionsetConstant::INDIVIDUAL_AND_AGGREGATE] == '*') ? true : false;
            $aggregateOnly = (isset($permissionTree[OrgPermissionsetConstant::AGGREGATE_ONLY]) && $permissionTree[OrgPermissionsetConstant::AGGREGATE_ONLY] == '*') ? true : false;
        } else {
            return $this->getAccessLevelPermissionFromDB($userId);
        }
        $accessLevels[OrgPermissionsetConstant::ACCESSLEVEL]['individual_and_aggregate'] = $individualAndAggregate;
        $accessLevels[OrgPermissionsetConstant::ACCESSLEVEL]['aggregate_only'] = $aggregateOnly;
        $this->logger->info(">>>> Get Access Level Permission  for User Id ");
        return $accessLevels;
    }

    public function getAccessLevelPermissionFromDB($userId)
    {
        $this->logger->debug(">>>> Get Access Level Permission  from DB for User Id " . $userId);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_GROUP_FACULTY_REPO);
        $userGroupsPermission = $this->orgGroupFacultyRepository->getGroupByUserPermissionSet($userId);
        $accessLevels = array();
        if ($userGroupsPermission && count($userGroupsPermission) > 0) {
            $accessLevels[OrgPermissionsetConstant::ORGANIZATION_ID] = $userGroupsPermission[0][OrgPermissionsetConstant::ORGANIZATION];
            $permissionSet = array_unique(array_column($userGroupsPermission, OrgPermissionsetConstant::PERMISSION_SET));
            $accessLevelData[OrgPermissionsetConstant::INDIVIDUAL_AND_AGGREGATE] = false;
            $accessLevelData[OrgPermissionsetConstant::AGGREGATE_ONLY] = false;
            foreach ($permissionSet as $ps) {
                $psData = $this->getPermissionSetsDataById($ps);
                $psAccessLevel = $psData->getAccessLevel();
                $accessLevelData = $this->processAccessLevel($psAccessLevel, $accessLevelData);
            }
            $accessLevels[OrgPermissionsetConstant::ACCESSLEVEL]['individual_and_aggregate'] = $accessLevelData[OrgPermissionsetConstant::INDIVIDUAL_AND_AGGREGATE];
            $accessLevels[OrgPermissionsetConstant::ACCESSLEVEL]['aggregate_only'] = $accessLevelData[OrgPermissionsetConstant::AGGREGATE_ONLY];
        }
        $this->logger->info(">>>> Get Access Level Permission  for User Id ");
        return $accessLevels;
    }

    private function processAccessLevel($psAccessLevel, $accessLevelData)
    {
        if ($psAccessLevel) {
            if ($psAccessLevel->getIndividualAndAggregate()) {
                $accessLevelData[OrgPermissionsetConstant::INDIVIDUAL_AND_AGGREGATE] = true;
            }
            if ($psAccessLevel->getAggregateOnly()) {
                $accessLevelData[OrgPermissionsetConstant::AGGREGATE_ONLY] = true;
            }
        }
        return $accessLevelData;
    }

    /**
     * Get the retention completion value from access tree, if its not there get it from database
     *
     * @param integer $userId
     * @param integer $organizationId
     * @return bool
     */

    public function getRetentionCompletion($userId, $organizationId)
    {
        $this->rbacManager = $this->container->get(Manager::SERVICE_KEY);
        $accessTree = $this->rbacManager->getRbac()->getPermissions();
        if ($accessTree) {
            if (isset($accessTree['retentionCompletion']) && $accessTree['retentionCompletion'] == '*') {
                $retentionCompletion = true;
            } else {
                $retentionCompletion = false;
            }
        } else {
            $retentionCompletion = $this->retentionCompletionService->validateRetentionCompletionPermission($userId, $organizationId);
        }

        return $retentionCompletion;
    }

    public function getRiskIndicator($userId)
    {
        $this->logger->debug(">>>> Get Risk Indicator for User Id " . $userId);
        $this->rbacManager = $this->container->get(OrgPermissionsetConstant::RBC_MANAGER);
        $permissionTree = $this->rbacManager->getRbac()->getPermissions();
        $riskIndicatorVal = false;
        $intentToLeave = false;
        if ($permissionTree) {
            $riskIndicatorVal = (isset($permissionTree['riskIndicator']) && $permissionTree['riskIndicator'] == '*') ? true : false;
            $intentToLeave = (isset($permissionTree[OrgPermissionsetConstant::INTENTTOLEAVE]) && $permissionTree[OrgPermissionsetConstant::INTENTTOLEAVE] == '*') ? true : false;
        } else {
            return $this->getRiskIndicatorFromDB($userId);
        }
        $riskIndicator[OrgPermissionsetConstant::RISK_INDICATOR] = $riskIndicatorVal;
        $riskIndicator[OrgPermissionsetConstant::INTENT_TO_LEAVE] = $intentToLeave;
        $this->logger->info(">>>> Get Risk Indicator for User Id ");
        return $riskIndicator;
    }

    public function getRiskIndicatorFromDB($userId)
    {
        $this->logger->debug(">>>> Get Risk Indicator from DB for User Id " . $userId);
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_GROUP_FACULTY_REPO);
        $userGroupsPermission = $this->orgGroupFacultyRepository->getGroupByUserPermissionSet($userId);
        $riskIndicator = array();
        if ($userGroupsPermission && count($userGroupsPermission) > 0) {
            $riskIndicator[OrgPermissionsetConstant::ORGANIZATION_ID] = $userGroupsPermission[0][OrgPermissionsetConstant::ORGANIZATION];
            $permissionSet = array_unique(array_column($userGroupsPermission, OrgPermissionsetConstant::PERMISSION_SET));
            $riskIntendData[OrgPermissionsetConstant::RISK_INDICATOR_VAL] = false;
            $riskIntendData[OrgPermissionsetConstant::INTENTTOLEAVE] = false;
            foreach ($permissionSet as $ps) {
                $psData = $this->getPermissionSetsDataById($ps);
                $riskIntendData = $this->processRiskIndicator($psData, $riskIntendData);
            }
            $riskIndicator[OrgPermissionsetConstant::RISK_INDICATOR] = $riskIntendData[OrgPermissionsetConstant::RISK_INDICATOR_VAL];
            $riskIndicator[OrgPermissionsetConstant::INTENT_TO_LEAVE] = $riskIntendData[OrgPermissionsetConstant::INTENTTOLEAVE];
        }
        $this->logger->info(">>>> Get Risk Indicator for User Id ");
        return $riskIndicator;
    }

    private function processRiskIndicator($psData, $riskIntendData)
    {
        if ($psData) {
            if ($psData->getRiskIndicator()) {
                $riskIntendData[OrgPermissionsetConstant::RISK_INDICATOR_VAL] = true;
            }
            if ($psData->getIntentToLeave()) {
                $riskIntendData[OrgPermissionsetConstant::INTENTTOLEAVE] = true;
            }
        }
        return $riskIntendData;
    }

    public function getFeaturesBlockPermission($userId)
    {
        $this->logger->debug(">>>> Get Features Block Permission for User Id " . $userId);
        $this->rbacManager = $this->container->get(OrgPermissionsetConstant::RBC_MANAGER);
        $accessMap = $this->rbacManager->getAccessMap($userId);
        $featureBlock = array();
        $featureBlockSet = array();
        if ($accessMap && isset($accessMap[OrgPermissionsetConstant::FEATURES]) && count($accessMap[OrgPermissionsetConstant::FEATURES]) > 0) {
            $featureBlockSet = $accessMap[OrgPermissionsetConstant::FEATURES];
        } else {
            $featureBlockSet = $this->getFeaturesBlockPermissionFromDB($userId);
        }
        $resultfeatureBlocks[OrgPermissionsetConstant::FEATURES] = $featureBlockSet;
        $this->logger->info(">>>> Get Risk Indicator for User Id ");
        return $resultfeatureBlocks;
    }

    /**
     *
     * @param
     *            $userId
     * @return multitype:NULL multitype:multitype:NULL
     */
    public function getFeaturesBlockPermissionFromDB($userId)
    {
        $this->logger->debug(">>>> Get Features Block Permission from DB for a User Id " . $userId);
        $featureBlock = array();
        $featureBlockSet = array();
        $resultfeatureBlocks = array();

        $permissionSet = $this->getPermissionSetIdsOfUser($userId);
        if ($permissionSet && count($permissionSet) > 0) {
            foreach ($permissionSet as $ps) {
                $psData = $this->getPermissionSetsDataById($ps);
                if ($psData) {
                    $featureBlocks = $psData->getFeatures();
                    if ($featureBlocks) {
                        foreach ($featureBlocks as $pb) {
                            $fid = $pb->getId();
                            $featureBlock[$fid]['id'] = $pb->getId();
                            $featureBlock[$fid]['name'] = $pb->getName();
                            if (!isset($featureBlock[$fid][OrgPermissionsetConstant::PUBLIC_SHARE]['view'])) {
                                $featureBlock[$fid][OrgPermissionsetConstant::PUBLIC_SHARE]['view'] = false;
                                $featureBlock[$fid][OrgPermissionsetConstant::PUBLIC_SHARE][OrgPermissionsetConstant::CREATE] = false;
                                $featureBlock[$fid][OrgPermissionsetConstant::PRIVATE_SHARE]['view'] = false;
                                $featureBlock[$fid][OrgPermissionsetConstant::PRIVATE_SHARE][OrgPermissionsetConstant::CREATE] = false;
                                $featureBlock[$fid][OrgPermissionsetConstant::TEAMS_SHARE]['view'] = false;
                                $featureBlock[$fid][OrgPermissionsetConstant::TEAMS_SHARE][OrgPermissionsetConstant::CREATE] = false;
                            }

                            if (isset($featureBlock[$fid][OrgPermissionsetConstant::PUBLIC_SHARE]['view']) && $pb->getPublicShare()) {
                                if ($pb->getPublicShare()->getView()) {
                                    $featureBlock[$fid][OrgPermissionsetConstant::PUBLIC_SHARE]['view'] = true;
                                }
                            }
                            if (isset($featureBlock[$fid][OrgPermissionsetConstant::PUBLIC_SHARE][OrgPermissionsetConstant::CREATE]) && $pb->getPublicShare()) {
                                if ($pb->getPublicShare()->getCreate()) {
                                    $featureBlock[$fid][OrgPermissionsetConstant::PUBLIC_SHARE][OrgPermissionsetConstant::CREATE] = true;
                                }
                            }
                            if (isset($featureBlock[$fid][OrgPermissionsetConstant::PRIVATE_SHARE][OrgPermissionsetConstant::CREATE]) && $pb->getPrivateShare()) {
                                if ($pb->getPrivateShare()->getCreate()) {
                                    $featureBlock[$fid][OrgPermissionsetConstant::PRIVATE_SHARE][OrgPermissionsetConstant::CREATE] = true;
                                    $featureBlock[$fid][OrgPermissionsetConstant::PRIVATE_SHARE]['view'] = true;
                                }
                            }
                            if (isset($featureBlock[$fid][OrgPermissionsetConstant::TEAMS_SHARE]['view']) && $pb->getTeamsShare()) {
                                if ($pb->getTeamsShare()->getView()) {
                                    $featureBlock[$fid][OrgPermissionsetConstant::TEAMS_SHARE]['view'] = true;
                                }
                            }
                            if (isset($featureBlock[$fid][OrgPermissionsetConstant::TEAMS_SHARE][OrgPermissionsetConstant::CREATE]) && $pb->getTeamsShare()) {
                                if ($pb->getTeamsShare()->getCreate()) {
                                    $featureBlock[$fid][OrgPermissionsetConstant::TEAMS_SHARE][OrgPermissionsetConstant::CREATE] = true;
                                }
                            }

                            if ($pb->getName() === 'Referrals') {
                                $featureBlock[$fid][OrgPermissionsetConstant::RECIEVE_REFERRALS] = false;
                                if ($pb->getReceiveReferrals()) {
                                    $featureBlock[$fid][OrgPermissionsetConstant::RECIEVE_REFERRALS] = true;
                                }
                            } else {
                                unset($featureBlock[$fid][OrgPermissionsetConstant::RECIEVE_REFERRALS]);
                            }
                        }
                    }
                }
            }
        }

        $featureBlock = array_values($featureBlock);
        $this->logger->info(">>>> Get Features Block Permission from DB for a User Id ");
        return $featureBlock;
    }

    /**
     * Function will return the access of particuler feature type access
     *
     * @param unknown $user
     * @param unknown $featureType
     * @return boolean
     */
    public function getAllowedFeatureAccess($userId, $featureType)
    {
        $this->logger->debug(">>>> Get Allowed Features Access for User Id " . $userId . "Feature Type" . $featureType);
        $featureAccess = false;
        $allFeatures = $this->getFeaturesBlockPermission($userId);
        /**
         * getting featurs only from the array
         */
        if ($allFeatures[OrgPermissionsetConstant::FEATURES]) {
            $formatedFeature = $this->getFeaturesArray($allFeatures[OrgPermissionsetConstant::FEATURES]);
            /**
             * checking if Feature key is as per requirement
             */
            if ($featureType && strpos($featureType, "_")) {
                $featureToken = explode('_', $featureType);
                if (isset($featureToken[0])) {
                    $featureAccess = $this->processAccess($featureToken, $formatedFeature);
                }
            }
        }
        $this->logger->info(">>>> Get Allowed Features Access for User Id ");
        return $featureAccess;
    }

    private function processAccess($featureToken, $formatedFeature)
    {
        $featureAccess = false;
        if (key_exists($featureToken[0], $formatedFeature)) {
            $getFeature = $formatedFeature[$featureToken[0]];
            if (isset($featureToken[1]) && isset($featureToken[2])) {
                $featureKey = $featureToken[1] . '_' . $featureToken[2];
                if (key_exists($featureKey, $getFeature)) {
                    /**
                     * gettig feature value
                     */
                    $featureAccess = $getFeature[$featureKey];
                }
            }
        }
        return $featureAccess;
    }

    /**
     * Function will return feature array with upper case of keys
     *
     * @param array $allFeatures
     * @return array
     */
    public function getFeaturesArray($allFeatures)
    {
        $features = array();
        foreach ($allFeatures as $feature) {
            $featureName = $this->getFormattedName($feature['name']);
            $features[$featureName] = $this->formatArrayKey($feature);
        }
        $this->logger->info(">>>> Get All Features ");
        return $features;
    }

    /**
     * Function formatting array with upper case of keys
     *
     * @param unknown $feature
     * @return multitype:unknown
     */
    public function formatArrayKey($feature)
    {
        $formattedFeature = array();
        foreach ($feature as $featureKey => $featureValue) {
            $formattedKey = $this->getFormattedName($featureKey);
            $formattedFeature[$formattedKey] = $featureValue;
        }
        $this->logger->info(">>>> Get Formatted Feature ");
        return $formattedFeature;
    }

    /**
     * Replacing space and "-" into "_" to get formatted keys
     *
     * @param unknown $featureName
     * @return Ambigous <string, mixed>
     */
    public function getFormattedName($featureName)
    {
        $this->logger->debug(">>>> Get Formatted Feature Name " . $featureName);
        $name = '';
        if ($featureName) {
            $replaceStr = (strpos($featureName, ' ')) ? ' ' : '-';
            $name = str_replace($replaceStr, '_', strtoupper($featureName));
        }
        $this->logger->info(">>>> Get Formatted Feature Name ");
        return $name;
    }

    public function formatNameUpperCase($featureName)
    {
        $this->logger->debug(">>>> Get Formatted FeatureName Upper Case " . $featureName);
        $name = '';
        if ($featureName) {
            $name = (strpos($featureName, '_')) ? ucwords(str_replace('_', ' ', $featureName)) : ucwords($featureName);
        }
        $this->logger->info(">>>> Get Formatted FeatureName Upper Case ");
        return $name;
    }

    /**
     * Funclion will return all ISP/ISQ by passed type
     *
     * @param string $type
     * @param int $userId
     * @return array
     */
    public function getAllowedIspIsqBlocks($type, $userId = null)
    {
        $this->logger->debug(">>>> Get Allowed ISP ISQ Blocks by Type " . $type);
        $block = array();
        $blockSet = array();
        $this->rbacManager = $this->container->get(OrgPermissionsetConstant::RBC_MANAGER);
        $accessMap = $this->rbacManager->getAccessMap($userId);
        if ($accessMap && isset($accessMap[$type]) && count($accessMap[$type]) > 0) {
            foreach ($accessMap[$type] as $blocks) {
                if ($blocks[OrgPermissionsetConstant::FIELD_VALUE]) {
                    $block['id'] = $blocks['id'];
                    $block['item_label'] = $blocks['name'];
                    $blockSet[$type][] = $block;
                }
            }
        }
        $this->logger->info(">>>> Get Allowed ISP ISQ Blocks by Type ");
        return $blockSet;
    }

    /**
     * Funclion will return allowed reports
     *
     * @param unknown $type
     * @return array
     */
    public function getAllowedReports($userId = null)
    {
        $this->logger->debug(">>>> Get Allowed reports");
        $block = array();
        $blockSet = array();
        $this->rbacManager = $this->container->get(OrgPermissionsetConstant::RBC_MANAGER);
        $accessMap = $this->rbacManager->getAccessMap($userId);
        if ($accessMap && isset($accessMap['reportsAccess']) && count($accessMap['reportsAccess']) > 0) {
            foreach ($accessMap['reportsAccess'] as $report) {
                if ($report[OrgPermissionsetConstant::FIELD_VALUE] == '*') {
                    $block['id'] = $report['id'];
                    $block['name'] = $report['name'];
                    $block['short_code'] = $report['shortCode'];
                    $blockSet['reports_access'][] = $block;
                }
            }
        }
        $this->logger->info(">>>> Get Allowed ISP ISQ Blocks by Type ");
        return $blockSet;
    }


    /**
     * Fetching courses access from permission
     *
     * @param int $userId
     * @return array
     */
    public function getCoursesAccess($userId)
    {
        $this->logger->debug(">>>> Get Courses Access for UserId " . $userId);
        $coursePermission = array();
        $this->rbacManager = $this->container->get('tinyrbac.manager');
        $accessMap = $this->rbacManager->getAccessMap($userId);
        if (isset($accessMap[SurveyConstant::COURSES_ACCESS])) {
            foreach ($accessMap[SurveyConstant::COURSES_ACCESS] as $key => $value) {
                $formattedKey = $this->formatKey($key);
                $coursePermission[$formattedKey] = $value;
            }
        }
        $this->logger->info(">>>> Get Courses Access for UserId ");
        return $coursePermission;
    }

    public function formatKey($key)
    {
        $this->logger->debug(">>>> Formatted key " . $key);
        return $result = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $key));
    }

    public function getOrgFeatures($userId)
    {
        $this->logger->debug(">>>> Get Organization Features for User Id" . $userId);
        $personObj = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person')->findOneById($userId);
        $orgId = $personObj->getOrganization()->getId();
        $getOrgFeature = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgFeatures')->getListFeatures($orgId);
        $this->logger->info(">>>> Get Organization Features for User Id");
        return $getOrgFeature;
    }

    /**
     * Finds all of the groups that the passed in $studentId and $facultyId share and pulls out all of the permissionsets that
     * the faculty member has for that student as a result of those groups.  Then based on those permisionsets, returns true
     * if the faculty member has the authority to execute the permissions passed in in the $permission parameter against/for
     * the passed in student.  Otherwise, returns false.
     *
     * @param $studentId
     * @param $permission - an array containing the permissions to check and see if the passed in faculty member can do
     *                      for the passed in student. (See details above)  Each string in this array should be in the format
     *                      of "<asset>-<accessLevel>-<action>" (e.g., "notes-public-view", "referral-private-create", etc.)
     * @param $facultyId
     *
     * @return bool
     */
    public function checkStudentPermission($studentId, $permission, $facultyId)
    {
        $permissionIds = $this->getPermissionSetIds($studentId, $facultyId);
        $this->logger->debug(">>>> Check Student Permission for Student Id " . $studentId . " Faculty Id " . $facultyId);
        if (!empty($permissionIds)) {
            $studentFeatures = $this->buildAggregatedFeatureTree($permissionIds);
            if (!empty($studentFeatures)) {
                return $this->checkFeaturePermission($permission, $studentFeatures);
            }
        }
        $this->logger->info(">>>> Check Student Permission for Student Id ");
        return false;
    }

    /**
     * Check user permission designed for delegate/proxy user permission
     * @param unknown $permission
     * @param unknown $facultyId
     * @return Ambigous <boolean, unknown>|boolean
     */
    public function checkUsersPermission($permission, $facultyId)
    {
        $this->logger->info(">>>> Get Permission Set ids By User");
        $permissionIds = $this->getPermissionSetIdsOfUser($facultyId);

        $this->logger->debug(">>>> Check User Permission for Faculty Id " . $facultyId);
        if (!empty($permissionIds)) {
            $userFeatures = $this->buildAggregatedFeatureTree($permissionIds);
            if (!empty($userFeatures)) {
                return $this->checkFeaturePermission($permission, $userFeatures);
            }
        }
        $this->logger->info(">>>> Check user Permission for delegate or proxy");
        return false;
    }

    /**
     * Get permissionset Ids of user including Group and Course permissions
     * @param int $userId
     * @return array
     */
    public function getPermissionSetIdsOfUser($userId)
    {
        $orgPermissionset = $this->orgPermissionsetRepository->getPermissionSetsDataByUser($userId);

        $permissionIds = [];
        if ($orgPermissionset) {
            foreach ($orgPermissionset as $permissionSetInfo) {
                $permissionIds[] = $permissionSetInfo['permissionTemplateId'];
            }
        }
        return $permissionIds;
    }

    public function getCoursePermissionIds($coursesSelected, $loggedInUserId, $orgId)
    {

        $this->orgCourseFacultyRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_COURSE_FACULTY_REPO);
        $coursePermissionIds = $this->orgCourseFacultyRepository->getPermissionsByFacultyCourse($coursesSelected, $loggedInUserId, $orgId);

        $permissionIds = [];
        if ($coursePermissionIds) {
            foreach ($coursePermissionIds as $ptInfo) {
                $permissionIds[] = $ptInfo['org_permissionset_id'];
            }
        }
        return $permissionIds;
    }

    /**
     * Checks if the passed in user ID is able to access the selected courses
     *
     * @param string $coursesSelected
     * @param int $loggedInUserId
     * @param int $organizationId
     * @param SynapseException|null $exception
     * @return bool
     */
    public function checkCreateAcademicUpdatePermissionForCourses($coursesSelected, $loggedInUserId, $organizationId, $exception = null) {

        $permissionIds = $this->getCoursePermissionIds($coursesSelected, $loggedInUserId, $organizationId);

        if (empty($permissionIds) || is_null($permissionIds[0])) {
            $canAccessCourse = false;
        } else {
            $courseAccess = $this->buildAggregatedFeatureTree($permissionIds);

            if (!empty($courseAccess)) {
                $canAccessCourse = $this->checkFeaturePermission(OrgPermissionsetConstant::CREATE_VIEW_ACADEMIC_UPDATE, $courseAccess);
            } else {
                $canAccessCourse = false;
            }
        }
        if($exception && !$canAccessCourse){
            throw $exception;
        }else{
            return $canAccessCourse;
        }
    }

    /**
     * Returns true if the ANY of the permissions passed in via the $permission parameter are found with a true value in
     * the passed in $studentFeatures parameter.  Otherwise, returns false.
     *
     * @param $permission - an array or a single string
     * @param $studentFeatures
     * @return bool
     */
    private function checkFeaturePermission($permission, $studentFeatures)
    {
        if (!is_array($permission)) {
            return $studentFeatures[$permission];
        }
        /**
         * if has multiple permission
         */
        foreach ($permission as $perm) {
            /** if student has a true value set for the specified permission*/
            if (isset($studentFeatures[$perm]) && $studentFeatures[$perm]) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns all permissionset ids for those permission sets that both the passed in student and faculty are members of.
     *
     * @param $studentId
     * @param $facultyId
     * @return array
     */
    public function getPermissionSetIds($studentId, $facultyId)
    {
        $allPermissionSetIds = array();

        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_GROUP_FACULTY_REPO);
        $getPermissionsetIds = $this->orgGroupFacultyRepository->getPermissionsByFacultyStudent($facultyId, $studentId);
        if (!empty($getPermissionsetIds)) {
            foreach ($getPermissionsetIds as $permissionSetIds) {
                $allPermissionSetIds[] = $permissionSetIds['org_permissionset_id'];
            }
            $allPermissionSetIds = array_filter(array_unique($allPermissionSetIds));
        }
        return $allPermissionSetIds;
    }

    public function getRiskIndicatorForStudent($studentId, $organization, $facultyId)
    {
        $permissionIds = $this->getPermissionSetIds($studentId, $facultyId);

        $this->logger->debug(">>>> Get Risk Indicator For Student " . $studentId . "Faculty Id" . $facultyId);

        $riskIndicatorVal = false;
        $intentToLeave = false;
        $riskIndicator = array();
        if ($permissionIds) {
            foreach ($permissionIds as $ps) {
                $psData = $this->getPermissionSetsDataById($ps);
                if ($psData) {
                    if ($psData->getRiskIndicator()) {
                        $riskIndicatorVal = true;
                    }
                    if ($psData->getIntentToLeave()) {
                        $intentToLeave = true;
                    }
                }
            }
        }
        $riskIndicator[OrgPermissionsetConstant::RISK_INDICATOR] = $riskIndicatorVal;
        $riskIndicator[OrgPermissionsetConstant::INTENT_TO_LEAVE] = $intentToLeave;
        $this->logger->info(">>>> Get Risk Indicator For Student ");
        return $riskIndicator;
    }

    public function getUserFeaturesPermission($userId)
    {
        $this->logger->debug(">>>> Get Features Block Permission from DB for a User Id " . $userId);
        $this->personService = $this->container->get(OrgPermissionsetConstant::PERSON_SERVICE);
        $user = $this->personService->find($userId);
        $userPermission = array();
        $permission = array();
        $permissionIds = $this->getPermissionSetIdsOfUser($userId);
        if (!empty($permissionIds)) {
            $studentFeatures = $this->buildAggregatedFeatureTree($permissionIds);

            if ($this->featureAccess) {
                foreach ($this->featureAccess['/'] as $featureName => $feature) {
                    $featureName = str_replace(' ', '_', strtolower($featureName));
                    $featureName_share = $featureName . '_share';
                    $access = $this->getUserAccess($feature);
                    $userPermission[$featureName] = $access;
                    $userPermission[$featureName_share] = $feature;
                }
            }
        }

        $permission['user_feature_permissions'][] = $userPermission;
        $this->logger->info(">>>> Get Student Feature ");
        return $permission;
    }

    /**
     * To get feature access for student
     *
     * @param int $studentId
     * @param int $facultyId
     * @return multitype:boolean unknown
     */
    public function getStudentFeature($studentId, $facultyId)
    {
        $this->logger->debug(">>>> Get Student Feature " . $studentId . "Faculty Id" . $facultyId);
        $this->personService = $this->container->get(OrgPermissionsetConstant::PERSON_SERVICE);
        $user = $this->personService->find($studentId);
        $studentPermission = array();
        $permission = array();
        $organization = $user->getOrganization()->getId();
        $permissionIds = $this->getPermissionSetIds($studentId, $facultyId);

        if (!empty($permissionIds)) {
            $studentFeatures = $this->buildAggregatedFeatureTree($permissionIds);
            if ($this->featureAccess) {
                foreach ($this->featureAccess['/'] as $featureName => $feature) {
                    $featureName = str_replace(' ', '_', strtolower($featureName));
                    $featureName_share = $featureName . '_share';
                    $access = $this->getUserAccess($feature);
                    $studentPermission[$featureName] = $access;
                    $studentPermission[$featureName_share] = $feature;
                }
            }
        }
        $permission['student_feature_permissions'][] = $studentPermission;
        $this->logger->info(">>>> Get Student Feature ");
        return $permission;
    }

    /**
     * To get only features of faculty for corrsponding faculty
     * Being used in activity stream
     * @param integer $studentId
     * @param integer $facultyId
     * @return multitype:boolean unknown
     */
    public function getStudentFeatureOnly($studentId, $facultyId)
    {
        $this->logger->debug(">>>> Get Student Feature " . $studentId . "Faculty Id" . $facultyId);
        $this->personService = $this->container->get(OrgPermissionsetConstant::PERSON_SERVICE);
        $user = $this->personService->find($studentId);
        $studentPermission = array();
        $permission = array();
        $organization = $user->getOrganization()->getId();
        $permissionIds = $this->getPermissionSetIds($studentId, $facultyId);


        if (!empty($permissionIds)) {
            $studentFeatures = $this->buildAggregatedFeatureTree($permissionIds);

            if ($this->featureAccess) {
                $features = $this->featureAccess['/'];
                $studentPermission = $this->getOnlyfeaturesSharingOptions($features);
            }
        }

        $permission = $studentPermission;
        $this->logger->info(">>>> Get Student Feature ");
        return $permission;
    }

    /**
     * Assigning direct referral for referral sharing option
     * Method is being used for activity stream only.
     *
     * @param array $features
     * @return array
     */
    private function getOnlyFeaturesSharingOptions($features)
    {
        $referralFeature = [];
        if ($features) {
            foreach ($features as $name => $options) {
                if ($name == 'referrals') {
                    $referralFeature[$name] = $options['direct_referral'];
                    $referralFeature[$name . '_reason_routed'] = $options['reason_routed_referral'];
                } else {
                    $referralFeature[$name] = $options;
                }

            }
        }
        return $referralFeature;
    }

    /**
     * checking overall access of features elements if all feature value are "false" it will return "false" for user.
     *
     * @param unknown $feature
     * @return boolean
     */
    private function getUserAccess($feature)
    {
        if (!empty($feature)) {
            foreach ($feature as $key => $featureItem) {
                if ($key == 'direct_referral' || $key == 'reason_routed_referral') {
                    foreach ($featureItem as $refItem) {
                        if ((isset($refItem['view']) && $refItem['view']) || (isset($refItem['create']) && $refItem['create'])) {
                            return true;
                        }
                    }
                } else if ((isset($featureItem['view']) && $featureItem['view']) || (isset($featureItem['create']) && $featureItem['create'])) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * This function takes the passed in array of $permissionsSetIds and iterates through all permission sets associated with those
     * ids.  It then returns a "tree" (associative array) that has a permission set member set to true if and only if one or more of
     * the permission sets (from the passed in ids) had the same member set to true.
     *
     * For example, if one of the permission sets for a passed in ID had receive_referrals set to true and another of the
     * permission sets for a different passed in ID had receive_referrals set to false, the returned tree would have receive_referrals
     * set to true.
     *
     * This function also duplicate a part of the returned tree in the class level variable $this->featureAccess.  Unlike the returned
     * value, $this->featureAccess is NOT initialized each time this function is called. Therefore, the tree is added too
     * and/or values are adjusted by each successive call to the function.  The $this->featureAccess tree also only has feature
     * related permissions in it.  It does NOT have course, academic update, final grades or any other permission set data.
     * Lastly, $this->featureAccess DOES have all of its values set to false if any of the passed in IDs do not return
     * anything (i.e., a null or completely empty string) from the database.
     *
     * (I am just the interpreter here... I didn't decide what this function should or shouldn't do. :-) )
     *
     * @param array $permissionSetIds
     * @return array $featureBlock
     */
    public function buildAggregatedFeatureTree($permissionSetIds)
    {
        $this->logger->info(">>>> Get Aggregate Feature By PermissionsetIds ");
        $featureBlock = array();

        $featureBlock['/']['viewCourses'] = false;
        $featureBlock['/']['createViewAcademicUpdate'] = false;
        $featureBlock['/']['viewAllAcademicUpdateCourses'] = false;
        $featureBlock['/']['viewAllFinalGrades'] = false;
        /*
         * To build a features permission array we are passing permissionset Ids, It could be more then one for a faculty
         * So it will get an aggregate permissions for features.
         */
        foreach ($permissionSetIds as $index => $permissionSetId) {
            // Fetching permission set data for a permissionset id.
            $orgPermissionSetDto = $this->getPermissionSetsDataById($permissionSetId);
            if ($orgPermissionSetDto) {
                // Checking if faculty/staff has Individual and aggregate access
                if ($orgPermissionSetDto->getAccessLevel()->getIndividualAndAggregate()) {

                    // Creating aggregate courses permission for each permission set ids
                    $coursesAccess = $orgPermissionSetDto->getCoursesAccess();
                    if ($coursesAccess) {

                        if ($coursesAccess->getViewCourses()) {
                            $featureBlock['/']['viewCourses'] = true;
                        }
                        if ($coursesAccess->getCreateViewAcademicUpdate()) {
                            $featureBlock['/']['createViewAcademicUpdate'] = true;
                        }
                        if ($coursesAccess->getViewAllAcademicUpdateCourses()) {
                            $featureBlock['/']['viewAllAcademicUpdateCourses'] = true;
                        }
                        if ($coursesAccess->getViewAllFinalGrades()) {
                            $featureBlock['/']['viewAllFinalGrades'] = true;
                        }
                    }
                    // Getting features block permission from permission template
                    $featureBlocks = $orgPermissionSetDto->getFeatures();

                    if ($featureBlocks) {
                        // This loop will iterate for all features(Notes|Contacts|Appointments|Referrals|Email) block to create aggregate permission
                        foreach ($featureBlocks as $feature) {
                            $featureId = $feature->getId();
                            if ($featureId == 1) {
                                // Since referral permission has complicated permission blocks from rest of the features so handling separately containing feature id one(1).
                                $referralPermissions = [];
                                $referralPermissions['direct'] = $feature->getDirectReferral();
                                $referralPermissions['reason_routed'] = $feature->getReasonRoutedReferral();
                                $featureNameKey = str_replace(' ', '_', strtolower($feature->getName()));
                                $featureName = $featureNameKey;

                                foreach ($referralPermissions as $referralSharingType => $referralSharingPermission) {

                                    if ($referralSharingType == 'reason_routed') {
                                        $featureNameKey = 'reason-routed-' . $featureNameKey;
                                        $accessKey = 'reason_routed_referral';
                                    } else {
                                        $accessKey = 'direct_referral';
                                    }

                                    /*
                                     * The thumb rule of below logic is if any permission set sharing option attribute has true value in one permission set and another has false
                                     * then in result it will serve us a true.
                                     *
                                     * For example, if one of the permission sets for a passed in ID had receive_referrals set to true and another of the
                                     * permission sets for a different passed in ID had receive_referrals set to false, the returned tree would have receive_referrals
                                     * set to true.
                                     */
                                    if (isset($featureBlock['/']["$featureNameKey-" . 'public-view']) || isset($featureBlock['/']["$featureNameKey-" . 'public-create'])) {
                                        // This if case will store grant access(true|false) for first time for referrals
                                        if (isset($featureBlock['/']["$featureNameKey-" . 'public-view']) && ($referralSharingPermission->getPublicShare()->getView())) {
                                            $featureBlock['/']["$featureNameKey-" . 'public-view'] = true;
                                            $this->featureAccess['/'][$featureName][$accessKey]['public_share']['view'] = true;
                                        }
                                        if (isset($featureBlock['/']["$featureNameKey-" . 'public-create']) && ($referralSharingPermission->getPublicShare()->getCreate())) {
                                            $featureBlock['/']["$featureNameKey-" . 'public-create'] = true;
                                            $this->featureAccess['/'][$featureName][$accessKey]['public_share']['create'] = true;

                                        }
                                        if (isset($featureBlock['/']["$featureNameKey-" . 'private-create']) && ($referralSharingPermission->getPrivateShare()->getCreate())) {
                                            $featureBlock['/']["$featureNameKey-" . 'private-create'] = true;
                                            $featureBlock['/']["$featureNameKey-" . 'private-view'] = true;
                                            $this->featureAccess['/'][$featureName][$accessKey]['private_share']['create'] = true;
                                            $this->featureAccess['/'][$featureName][$accessKey]['private_share']['view'] = true;
                                        }
                                        if (isset($featureBlock['/']["$featureNameKey-" . 'teams-view']) && ($referralSharingPermission->getTeamsShare()->getView())) {
                                            $featureBlock['/']["$featureNameKey-" . 'teams-view'] = true;
                                            $this->featureAccess['/'][$featureName][$accessKey]['teams_share']['view'] = true;
                                        }
                                        if (isset($featureBlock['/']["$featureNameKey-" . 'teams-create']) && ($referralSharingPermission->getTeamsShare()->getCreate())) {
                                            $featureBlock['/']["$featureNameKey-" . 'teams-create'] = true;
                                            $this->featureAccess['/'][$featureName][$accessKey]['teams_share']['create'] = true;

                                        }

                                    } else {
                                        // This else case will store grant access(true|false) for remaining permission set ids for referrals
                                        $featureBlock['/']["$featureNameKey-" . 'public-create'] = ($referralSharingPermission->getPublicShare()->getCreate()) ? true : false;
                                        $this->featureAccess['/'][$featureName][$accessKey]['public_share']['create'] = ($referralSharingPermission->getPublicShare()->getCreate()) ? true : false;
                                        $featureBlock['/']["$featureNameKey-" . 'public-view'] = ($referralSharingPermission->getPublicShare()->getView()) ? true : false;
                                        $this->featureAccess['/'][$featureName][$accessKey]['public_share']['view'] = ($referralSharingPermission->getPublicShare()->getView()) ? true : false;

                                        $featureBlock['/']["$featureNameKey-" . 'private-create'] = ($referralSharingPermission->getPrivateShare()->getCreate()) ? true : false;
                                        $this->featureAccess['/'][$featureName][$accessKey]['private_share']['create'] = ($referralSharingPermission->getPrivateShare()->getCreate()) ? true : false;
                                        $featureBlock['/']["$featureNameKey-" . 'private-view'] = ($referralSharingPermission->getPrivateShare()->getView()) ? true : false;
                                        $this->featureAccess['/'][$featureName][$accessKey]['private_share']['view'] = ($referralSharingPermission->getPrivateShare()->getView()) ? true : false;
                                        if ($featureBlock['/']["$featureNameKey-" . 'private-create']) {
                                            $featureBlock['/']["$featureNameKey-" . 'private-view'] = true;
                                            $this->featureAccess['/'][$featureName][$accessKey]['private_share']['view'] = true;
                                        }
                                        $featureBlock['/']["$featureNameKey-" . 'teams-create'] = ($referralSharingPermission->getTeamsShare()->getCreate()) ? true : false;
                                        $this->featureAccess['/'][$featureName][$accessKey]['teams_share']['create'] = ($referralSharingPermission->getTeamsShare()->getCreate()) ? true : false;
                                        $featureBlock['/']["$featureNameKey-" . 'teams-view'] = ($referralSharingPermission->getTeamsShare()->getView()) ? true : false;
                                        $this->featureAccess['/'][$featureName][$accessKey]['teams_share']['view'] = ($referralSharingPermission->getTeamsShare()->getView()) ? true : false;

                                    }

                                }
                                // Setting access for receive referral
                                if (isset($featureBlock['/']['receive_referrals'])) {
                                    if ($feature->getReceiveReferrals()) {
                                        $featureBlock['/']['receive_referrals'] = true;
                                        $this->featureAccess['/'][$featureName]['receive_referrals'] = true;
                                    }
                                } else {
                                    $featureBlock['/']['receive_referrals'] = ($feature->getReceiveReferrals()) ? true : false;
                                    $this->featureAccess['/'][$featureName]['receive_referrals'] = ($feature->getReceiveReferrals()) ? true : false;
                                }

                            } else {
                                // This will handle the remaining features exclude referral rules is same as mention in above comment.
                                $featureNameKey = str_replace(' ', '_', strtolower($feature->getName()));

                                if (isset($featureBlock['/']["$featureNameKey-" . 'public-view']) || isset($featureBlock['/']["$featureNameKey-" . 'public-create'])) {
                                    // This if case will check and grant access for each feature first time for all permission set ids except referrals
                                    if (isset($featureBlock['/']["$featureNameKey-" . 'public-view']) && ($feature->getPublicShare()->getView())) {
                                        $featureBlock['/']["$featureNameKey-" . 'public-view'] = true;
                                        $this->featureAccess['/'][$featureNameKey]['public_share']['view'] = true;
                                    }
                                    if (isset($featureBlock['/']["$featureNameKey-" . 'public-create']) && ($feature->getPublicShare()->getCreate())) {
                                        $featureBlock['/']["$featureNameKey-" . 'public-create'] = true;
                                        $this->featureAccess['/'][$featureNameKey]['public_share']['create'] = true;

                                    }
                                    if (isset($featureBlock['/']["$featureNameKey-" . 'private-create']) && ($feature->getPrivateShare()->getCreate())) {
                                        $featureBlock['/']["$featureNameKey-" . 'private-create'] = true;
                                        $featureBlock['/']["$featureNameKey-" . 'private-view'] = true;
                                        $this->featureAccess['/'][$featureNameKey]['private_share']['create'] = true;
                                        $this->featureAccess['/'][$featureNameKey]['private_share']['view'] = true;
                                    }
                                    if (isset($featureBlock['/']["$featureNameKey-" . 'teams-view']) && ($feature->getTeamsShare()->getView())) {
                                        $featureBlock['/']["$featureNameKey-" . 'teams-view'] = true;
                                        $this->featureAccess['/'][$featureNameKey]['teams_share']['view'] = true;
                                    }
                                    if (isset($featureBlock['/']["$featureNameKey-" . 'teams-create']) && ($feature->getTeamsShare()->getCreate())) {
                                        $featureBlock['/']["$featureNameKey-" . 'teams-create'] = true;
                                        $this->featureAccess['/'][$featureNameKey]['teams_share']['create'] = true;

                                    }

                                } else {
                                    // This else case will check and grant access for each feature for remaining permissionset ids except referrals
                                    $featureBlock['/']["$featureNameKey-" . 'public-create'] = ($feature->getPublicShare()->getCreate()) ? true : false;
                                    $this->featureAccess['/'][$featureNameKey]['public_share']['create'] = ($feature->getPublicShare()->getCreate()) ? true : false;
                                    $featureBlock['/']["$featureNameKey-" . 'public-view'] = ($feature->getPublicShare()->getView()) ? true : false;
                                    $this->featureAccess['/'][$featureNameKey]['public_share']['view'] = ($feature->getPublicShare()->getView()) ? true : false;

                                    $featureBlock['/']["$featureNameKey-" . 'private-create'] = ($feature->getPrivateShare()->getCreate()) ? true : false;
                                    $this->featureAccess['/'][$featureNameKey]['private_share']['create'] = ($feature->getPrivateShare()->getCreate()) ? true : false;
                                    $featureBlock['/']["$featureNameKey-" . 'private-view'] = ($feature->getPrivateShare()->getView()) ? true : false;
                                    $this->featureAccess['/'][$featureNameKey]['private_share']['view'] = ($feature->getPrivateShare()->getView()) ? true : false;
                                    if ($featureBlock['/']["$featureNameKey-" . 'private-create']) {
                                        $featureBlock['/']["$featureNameKey-" . 'private-view'] = true;
                                        $this->featureAccess['/'][$featureNameKey]['private_share']['view'] = true;
                                    }
                                    $featureBlock['/']["$featureNameKey-" . 'teams-create'] = ($feature->getTeamsShare()->getCreate()) ? true : false;
                                    $this->featureAccess['/'][$featureNameKey]['teams_share']['create'] = ($feature->getTeamsShare()->getCreate()) ? true : false;
                                    $featureBlock['/']["$featureNameKey-" . 'teams-view'] = ($feature->getTeamsShare()->getView()) ? true : false;
                                    $this->featureAccess['/'][$featureNameKey]['teams_share']['view'] = ($feature->getTeamsShare()->getView()) ? true : false;

                                }
                            }
                        }
                    }
                } else {
                    // Setting false for each feature permission access if faculty/staff has aggregate only permission.
                    array_walk_recursive($this->featureAccess['/'], function (&$item, $key) {
                        if (!is_array($key)) {
                            $item = false;
                        }
                    });

                }
            }
        }

        return $featureBlock['/'];
    }

    public function FutureIsqPermissionSet()
    {
        $orgQuestRepo = $this->repositoryResolver->getRepository('SynapseSurveyBundle:SurveyQuestions');
        $this->surveyRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:SurveyLang');
        $this->orgPermissionsetRepository = $this->repositoryResolver->getRepository(OrgPermissionsetConstant::ORG_PERMISSIONSET_REPO);
        $orgPerm = $orgQuestRepo->getOrgIsqperm();
        if (count($orgPerm) > 0) {
            foreach ($orgPerm as $orgPermission) {

                $orgPermissionSet = $this->orgPermissionsetRepository->find($orgPermission['permissionset_id']);
                //To get all applicable surves and questions.
                $allSurveys = $this->surveyRepository->getAllSurveys(1);
                foreach ($allSurveys as $surveyCohort) {
                    $allcohort = explode(',', $surveyCohort['all_cohorts']);
                    $survey = $surveyCohort['id'];
                    foreach ($allcohort as $cohort) {
                        $futureIsqs = $orgQuestRepo->getFutureIsqs($orgPermission['org_id'], $orgPermission['permissionset_id'], $survey, $cohort);
                        if (count($futureIsqs) > 0) {
                            $this->createFutureIsq($futureIsqs, $orgPermissionSet);
                        }
                    }
                }
            }
            $this->orgPermissionsetRepository->flush();
        }
    }

    protected function createFutureIsq($isqBlocks, $orgPermissionSet)
    {
        $surveyRepo = $this->repositoryResolver->getRepository("SynapseCoreBundle:SurveyLang");
        $organization = $orgPermissionSet->getOrganization();
        if (count($isqBlocks) > 0) {
            $this->orgQuestionRepository = $this->repositoryResolver->getRepository(self::ORG_QUESTION_REPO);
            $this->orgPermissionsetQuestionRepository = $this->repositoryResolver->getRepository(self::ORG_PERMISSIONSET_QUESTION_REPO);
            foreach ($isqBlocks as $isqBlock) {
                /**
                 * checking the block selection-if true only inserts
                 */
                $orgQuestionId = $isqBlock['question_id'];
                $orgQuestion = $this->orgQuestionRepository->find($orgQuestionId);
                if (!$orgQuestion) {
                    throw new ValidationException([
                        "ISQ Not Found"
                    ], "ISQ Not Found", OrgPermissionsetConstant::ERROR_KEY);
                }

                $survey = $surveyRepo->findOneBySurvey($isqBlock['survey_id']);
                if (!$survey) {
                    throw new ValidationException([
                        "ISQ Survey Not Found"
                    ], "ISQ Survey Not Found", OrgPermissionsetConstant::ERROR_KEY);
                }
                $isqPermission = new OrgPermissionsetQuestion();
                $isqPermission->setOrganization($organization);
                $isqPermission->setOrgQuestion($orgQuestion);
                $isqPermission->setOrgPermissionset($orgPermissionSet);
                $isqPermission->setSurvey($survey->getSurvey());
                $isqPermission->setCohortCode($isqBlock['cohort_id']);
                $this->orgPermissionsetQuestionRepository->persist($isqPermission, false);
            }
        }
    }

    /**
     * Given the activity type, check to see if the allowed features map grants permission to that kind of activity.
     * This does not check create vs. view or public/private/team - but only looks to see if a person has access to a
     * given feature <em>somehow</em>
     *
     * @param array $allowedFeatures
     * @param string $activityType
     * @return bool
     */
    public function checkActivityPermission($allowedFeatures, $activityType)
    {
        switch ($activityType) {
            case "appointment":
                return $allowedFeatures['booking'];
            case "contact":
                return $allowedFeatures['log_contacts'];
            case "notes":
                return $allowedFeatures['notes'];
            case "referral":
            case "open referral":
                return $allowedFeatures['referrals'];
            case "email":
                return $allowedFeatures['email'];
            case "login":
                return true;
            default:
                return false;
        }
    }

    /**
     * This will make a new entry in org_tools_permission table for selected mapworks_tool
     *
     * @param array $mapworksTools -List of mapworks tools
     * @param OrgPermissionset $orgPermissionSet -An object of permissionset
     * @return array $insertedToolsPermissions
     * @throws SynapseValidationException
     */
    public function insertToolsIntoToolPermissions($mapworksTools, $orgPermissionSet)
    {
        $insertedToolsPermissions = array();
        foreach ($mapworksTools as $tool) {
            if ($tool->getSelection()) {
                $toolsPermission = new OrgPermissionsetTool();
                $toolId = $tool->getToolId();
                $toolData = $this->mapworksToolRepository->find($toolId);
                $errorMsg = 'Tool Not Found';
                if(!$toolData){
                    throw new SynapseValidationException($errorMsg);
                }
                $toolsPermission->setMapworksToolId($toolData);
                $toolsPermission->setOrganization($orgPermissionSet->getOrganization());
                $toolsPermission->setOrgPermissionset($orgPermissionSet);
                $this->orgPermissionsetToolRepository->persist($toolsPermission, false);
                $insertedToolsPermissions[] = $toolsPermission;
            }
        }
        return $insertedToolsPermissions;
    }

    /**
     * This will make a new entry in org_tools_permission table for newly selected tools and remove the deselected tools
     *
     * @param array $mapworksTools -List of mapworks tools
     * @param OrgPermissionset $orgPermissionSet -An object of permissionset
     * @return array $updatedToolsPermissions -A updated array of permissionsetTools
     * @throws SynapseValidationException
     */
    public function updatePermissionsetTools($mapworksTools, $orgPermissionSet)
    {
        $updatedToolsPermissions = array();
        foreach ($mapworksTools as $tool) {
            $toolPermission = $this->orgPermissionsetToolRepository->findOneBy([
                'orgPermissionset' => $orgPermissionSet->getId(),
                'mapworksToolId' => $tool->getToolId()
            ]);
            if ($toolPermission && !$tool->getSelection()) {
                $this->orgPermissionsetToolRepository->delete($toolPermission);
            } elseif (!$toolPermission && $tool->getSelection()) {
                $toolsPermission = new OrgPermissionsetTool();
                $toolId = $tool->getToolId();
                $toolData = $this->mapworksToolRepository->find($toolId);
                $errorMsg = 'Tool Not Found';
                if(!$toolData){
                    throw new SynapseValidationException($errorMsg);
                }
                $toolsPermission->setMapworksToolId($toolData);
                $toolsPermission->setOrganization($orgPermissionSet->getOrganization());
                $toolsPermission->setOrgPermissionset($orgPermissionSet);
                $this->orgPermissionsetToolRepository->persist($toolsPermission, false);
                $updatedToolsPermissions[] = $toolsPermission;
            }
        }
        return $updatedToolsPermissions;
    }

    /**
     * This function will retrieve the selected permissionset tools and formate them as DTO.
     *
     * @param int $permissionSetId
     * @param int $organizationId
     * @return array $toolSelectionDtoArray -An array of toolSelectionDts objects
     */
    public function getToolSelection($permissionSetId, $organizationId)
    {
        $permissionSetTools = $this->orgPermissionsetToolRepository->getToolsWithPermissionsetSelection($permissionSetId, $organizationId);
        $toolSelectionDtoArray = array();
        foreach ($permissionSetTools as $tool) {
            $selected = !empty($tool['selection']) ? true : false;
            $canAcessAggredate = !empty($tool['can_access_with_aggregate_only_permission']) ? true : false;
            $toolSelectionDto = new ToolSelectionDto();
            $toolSelectionDto->setToolId($tool['tool_id']);
            $toolSelectionDto->setToolName($tool['tool_name']);
            $toolSelectionDto->setShortCode($tool['short_code']);
            $toolSelectionDto->setCanAccessWithAggregateOnlyPermission($canAcessAggredate);
            $toolSelectionDto->setSelection($selected);
            $toolSelectionDtoArray[] = $toolSelectionDto;
        }
        return $toolSelectionDtoArray;
    }

    /**
     * Retrieve tools associated with permissionset
     *
     * @param int $permissionsetId
     * @param int $organizationId
     * @return array $tools
     */
    public function retrieveOrgPermissionsetTools($permissionsetId, $organizationId)
    {
        $tools = [];
        $toolsPermissionInfo = $this->orgPermissionsetToolRepository->getToolsWithPermissionsetSelection($permissionsetId, $organizationId);
        foreach ($toolsPermissionInfo as $tool) {
            $canAcessAggredate = !empty($tool['can_access_with_aggregate_only_permission']) ? true : false;
            $toolSeletionDto = new ToolSelectionDto();
            $toolSeletionDto->setToolId($tool['tool_id']);
            $toolSeletionDto->setToolName($tool['tool_name']);
            $toolSeletionDto->setCanAccessWithAggregateOnlyPermission($canAcessAggredate);
            $toolSeletionDto->setSelection($tool['selection']);
            $tools[] = $toolSeletionDto;
        }
        return $tools;
    }

    /**
     * This function will list all the mapworks tools
     *
     * @return array $output
     */
    public function getMapworksTools()
    {
        $mapworksTools = $this->mapworksToolRepository->findAll();
        $output = [];
        $output['tools'] = array();
        foreach ($mapworksTools as $tool) {
            $toolArray = [];
            $toolArray['tool_id'] = $tool->getId();
            $toolArray['tool_name'] = $tool->getToolName();
            $toolArray['short_code'] = $tool->getShortCode();
            $toolArray['can_access_with_aggregate_only_permission'] = !empty($tool->getCanAccessWithAggregateOnlyPermission()) ? true : false;
            $output['tools'][] = $toolArray;
        }
        $output['total_count'] = count($output['tools']);
        return $output;
    }

}
