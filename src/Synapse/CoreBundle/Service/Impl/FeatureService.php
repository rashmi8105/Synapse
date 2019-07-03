<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\FeatureMaster;
use Synapse\CoreBundle\Entity\OrgFeatures;
use Synapse\CoreBundle\Entity\ActivityReferenceUnassigned;
use Synapse\RestBundle\Entity\FeatureDTO;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Entity\ReferralRoutingRules;
use Synapse\CoreBundle\Util\Helper;
use JMS\Serializer\Serializer;
use Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository;
use Synapse\CoreBundle\Repository\OrgGroupFacultyRepository;


/**
 * @DI\Service("feature_service")
 */
class FeatureService extends AbstractService
{
    const SERVICE_KEY = "feature_service";

    const REFERRAL_FEATURE = "Referral_Feature";

    const NOTES_FEATURE = "Notes Feature";

    const LOG_CONTACTS_FEATURE = "Log Contacts Feature";

    const BOOKING_FEATURE = "Booking Feature";

    const STUDENT_REFERRAL_NOTIFICATION_FEATURE = "Student Referral Notification";

    const REASON_ROUTING_FEATURE = "Reason Routing Feature";
    
    const EMAIL_FEATURE = "Email Feature";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ORGFEATURES_REPO = "SynapseCoreBundle:OrgFeatures";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACTIVITY_CATEGORY_REPO = "SynapseCoreBundle:ActivityCategory";

    /**
     * @deprecated - Use Repository::REPOSITORY_KEY in the future.
     */
    const ACTIVITY_UNASSIGNED_REPO = "SynapseCoreBundle:ActivityReferenceUnassigned";

    const FIELD_ORGANIZATION = "organization";

    const ERROR_FEATURE_NOT_FOUND = "Feature not found.";

    const FIELD_ORG_ID = "organization_id";

    const FIELD_FEATUREID = "feature_id";

    const FIELD_CONNECTED = "connected";

    const FIELD_PERSONID = "person_id";

    const FIELD_PRIMARY_COORDINATOR = "is_primary_coordinator";
    
    const FIELD_PRIMARY_CAMPUS_CONNECTION = "is_primary_campus_connection";
    
    const FEATURE_NOT_FOUND = 'feature_not_found';

    const FEATURE_NAME = 'featureName';
    
    const PCCRR_FEATURE = 'Primary Campus Connection Referral Routing';

    static $features = array(
        self::REFERRAL_FEATURE,
        self::NOTES_FEATURE,
        self::LOG_CONTACTS_FEATURE,
        self::BOOKING_FEATURE,
        self::STUDENT_REFERRAL_NOTIFICATION_FEATURE,
        self::REASON_ROUTING_FEATURE,
        self::EMAIL_FEATURE,
        self::PCCRR_FEATURE
    );

    static $reponseFeatureKey = array(
        'referral',
        'notes',
        'log_contacts',
        'booking',
        'student_referral_notification',
        'reason_routing',
        'email',
        'primary_campus_connection_referral_routing'
    );

    /**
     *
     * @var FeatureRepository
     */
    private $featureRepository;

    /**
     *
     * @var FeatureRepository
     */
    private $featureMasterRepository;

    private $featureMasterLangRepository;

    private $activityCategoryRepository;

    private $activityReferenceUnassignedRepository;
    
    private $referralRoutingRulesRepo;

    private $orgLangRepository;

    private $langService;

    private $orgService;

    private $personService;
    
    private $actCategoryService;

    public $rbacManager;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     * @var OrgGroupFacultyRepository
     */
    private $orgGroupFacultyRepository;

    /**
     * @var OrgPermissionsetFeaturesRepository
     */
    private $orgPermissionsetFeaturesRepository;


    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "langService" = @DI\Inject("lang_service"),
     *            "orgService" = @DI\Inject("org_service"),
     *            "personService" = @DI\Inject("person_service"),
     *            "rbacManager" = @DI\Inject("tinyrbac.manager"),	 
     *            "actCategoryService" = @DI\Inject("activity_category_service"),
	 *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $langService, $orgService, $personService, $rbacManager, $actCategoryService, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->langService = $langService;
        $this->orgService = $orgService;
        $this->personService = $personService;
        $this->rbacManager = $rbacManager;
		$this->actCategoryService = $actCategoryService; 
        $this->container = $container;
        $this->orgGroupFacultyRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgGroupFaculty');
        $this->orgPermissionsetFeaturesRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrgPermissionsetFeatures');

    }



    /**
     *
     * @return FeatureRepository
     */
    public function getFeatureRepository()
    {
        $this->logger->info(">>>> Get Feature Repository");
        $this->featureRepository = $this->repositoryResolver->getRepository(self::ORGFEATURES_REPO);
        return $this->featureRepository;
    }

    protected function createFeature($organization, $key, $name)
    {
        $this->rbacManager->checkAccessToOrganization($organization->getId());

        $this->featureRepository = $this->repositoryResolver->getRepository(self::ORGFEATURES_REPO);
        $this->featureMasterRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:FeatureMaster");
        // array index start with 0,for that added by 1
        $featureMaster = $this->featureMasterRepository->find($key + 1);
        if ($featureMaster) {
            $referrals = $this->featureRepository->findBy(array(
                'feature' => $featureMaster,
                self::FIELD_ORGANIZATION => $organization
            ));
            if (! $referrals) {
                $referrals = new OrgFeatures();
                $referrals->setConnected(0);
                $referrals->setOrganization($organization);
                $referrals->setFeature($featureMaster);
                $this->featureRepository->createOrgFeature($referrals);
                $this->featureRepository->flush();
            }
        } else {
            
            throw new ValidationException([
                $name . self::ERROR_FEATURE_NOT_FOUND
            ], $name . self::ERROR_FEATURE_NOT_FOUND, self::FEATURE_NOT_FOUND);
        }
    }

    protected function getFeatures($organization)
    {
        $this->rbacManager->checkAccessToOrganization($organization->getId());

        $this->featureRepository = $this->repositoryResolver->getRepository(self::ORGFEATURES_REPO);
        $results = $this->featureRepository->getListFeatures($organization);
        $output = array();
        $index = 0;
        //var_dump($results);exit;
        foreach ($results as $result) {
            $output[self::FIELD_ORG_ID] = $result[self::FIELD_ORG_ID];
            $feature_id = $result[self::FIELD_FEATUREID];
            $feature_connected = $result[self::FIELD_CONNECTED];
            $feature_org_id = $result["id"];
            $output[FeatureService::$reponseFeatureKey[$index] . "_" . self::FIELD_FEATUREID] = $feature_id;
            $output[FeatureService::$reponseFeatureKey[$index] . "_org_id"] = $feature_org_id;
            $output["is_" . FeatureService::$reponseFeatureKey[$index] . "_enabled"] = $feature_connected;
            $index ++;
        }
        $reasonRoutingList = $this->actCategoryService->getActivityCategory();
        $reasonRoutingArr = array();
        foreach($reasonRoutingList['category_groups'] as $reasonRouting)
        {
            $routingList = array();
            $routingList['reason_id'] = $reasonRouting['group_item_key'];
            $routingList['reason_name'] = $reasonRouting['group_item_value'];
            
            //Getting the sub categories for the reasonRouting
            $subCategoryArr = array();
            foreach($reasonRouting['subitems'] as $subCategory)
            {                
                $reasonRoutingObj = $this->referralRoutingRulesRepo->findOneBy(array(
                    self::FIELD_ORGANIZATION => $organization,
                    'activityCategory' => $subCategory['subitem_key']
                ));
                if($reasonRoutingObj){
                    $subCatRouting = array();
                    $subCatRouting['reason_id'] = $subCategory['subitem_key'];
                    $subCatRouting['reason_name'] = $subCategory['subitem_value'];
                    if($reasonRoutingObj->getIsPrimaryCoordinator())
                    {
                        $subCatRouting[self::FIELD_PERSONID] = 0;
                        $subCatRouting[self::FIELD_PRIMARY_COORDINATOR] = true;
                        $subCatRouting[self::FIELD_PRIMARY_CAMPUS_CONNECTION] = false;
                    }elseif($reasonRoutingObj->getIsPrimaryCampusConnection()){
                        $subCatRouting[self::FIELD_PERSONID] = 0;
                        $subCatRouting[self::FIELD_PRIMARY_COORDINATOR] = false;
                        $subCatRouting[self::FIELD_PRIMARY_CAMPUS_CONNECTION] = true;
                    }else{
                        $subCatRouting[self::FIELD_PRIMARY_COORDINATOR] = false;
                        $subCatRouting[self::FIELD_PRIMARY_CAMPUS_CONNECTION] = false;
                        if(! $reasonRoutingObj->getPerson())
                        {
                            $subCatRouting[self::FIELD_PERSONID] = 0;
                        }else{
                            $subCatRouting[self::FIELD_PERSONID] = $reasonRoutingObj->getPerson()->getId();
                            $subCatRouting['firstname'] = $reasonRoutingObj->getPerson()->getFirstname();
                            $subCatRouting['lastname'] = $reasonRoutingObj->getPerson()->getLastname();
                        }
                    }       
                }
                $subCategoryArr[] = $subCatRouting;
            }
            $routingList['sub_categories'] = $subCategoryArr;
            $reasonRoutingArr[] = $routingList;
        }
        
        $output['reason_routing_list'] = $reasonRoutingArr;
        return $output;
    }

    /**
     *
     * @param
     *            $organizationId
     * @return array|\Synapse\CoreBundle\Entity\FeatureMaster[]|\Synapse\RestBundle\Entity\Error
     */
    public function getListFeatures($organizationId, $langid)
    {
        $this->activityCategoryRepository = $this->repositoryResolver->getRepository(self::ACTIVITY_CATEGORY_REPO);
        $this->referralRoutingRulesRepo = $this->repositoryResolver->getRepository('SynapseCoreBundle:ReferralRoutingRules');
        $this->rbacManager->checkAccessToOrganization($organizationId);
        $this->logger->debug(">>>> Get List Features for organizationId" . $organizationId . "LangId" . $langid);

        $this->featureRepository = $this->repositoryResolver->getRepository(self::ORGFEATURES_REPO);
        $language = $this->langService->getLangRepository()->find($langid);
        if (! $language) {
            $this->logger->error(" Feature Service - getListFeatures - Language Not Found - " . $langid );
            throw new ValidationException([
                ' Language Not Found.'
            ], ' Language Not Found.', 'lang_not_found');
        }
        
        $organization = $this->orgService->find($organizationId);
        
        foreach (FeatureService::$features as $key => $val) {
            $this->createFeature($organization, $key, $val);
        }
        // Activities Createtion if it not theere
        $routingReasonLists = $this->actCategoryService->getActivityCategory();

        foreach($routingReasonLists['category_groups'] as $reasonRoutingList)
        {
            if($reasonRoutingList['subitems'] && count($reasonRoutingList['subitems']) > 0)
            {
                foreach($reasonRoutingList['subitems'] as $routingReason)
                {
                    $this->setReasonRoutingList($routingReason, $organization);
                }
            }
        }
        $this->logger->info(">>>> Get List Features for organizationId");
        return $this->getFeatures($organization);
    }

    /**
     *
     * @param FeatureDTO $featureDTO            
     * @return array|mixed|\Synapse\RestBundle\Entity\Error
     */
    public function updateFeatures(FeatureDTO $featureDTO)
    {
        $this->referralRoutingRulesRepo = $this->repositoryResolver->getRepository('SynapseCoreBundle:ReferralRoutingRules');
        $this->rbacManager->checkAccessToOrganization($featureDTO->getOrganizationId());

        $logContent = $this->container->get('loggerhelper_service')->getLog($featureDTO);
        $this->logger->debug("Updating Features " . $logContent);

        $this->orgLangRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:OrganizationLang");
        $this->featureRepository = $this->repositoryResolver->getRepository(self::ORGFEATURES_REPO);
        $organization = $this->orgService->find($featureDTO->getOrganizationId());
        $this->updateFeature($organization, $featureDTO);
        if ($featureDTO->getIsReasonRoutingEnabled()) {
            $reasonLists = $featureDTO->getReasonRoutingList();
            foreach($reasonLists as $reasonRouting)
            {
                if(count($reasonRouting['sub_categories']) > 0)
                {
                    foreach($reasonRouting['sub_categories'] as $subCategory)
                    {
                        $this->updateReasonRoutingList($subCategory, $organization);
                    }
                }
            }
        }
        $this->featureRepository->flush();
        $orgLang = $this->orgLangRepository->findOneBy(array(
            self::FIELD_ORGANIZATION => $organization
        ));
        $this->logger->info(">>>> Updated Features");
        return ($this->getListFeatures($organization->getId(), $orgLang->getLang()
            ->getId()));
    }

    private function updateFeature($organization, $featureDTO)
    {   
        $this->featureRepository = $this->repositoryResolver->getRepository(self::ORGFEATURES_REPO);
        foreach (FeatureService::$features as $key => $val) {
            $connectedVal = 0;
            $route = $key + 1;
            $id = 0;
            switch ($route) {
                case 1:
                    $feature = $featureDTO->getReferralFeatureId();
                    $connectedVal = $featureDTO->getIsReferralEnabled();
                    $id = $featureDTO->getReferralOrgId();
                    break;
                case 2:
                    $feature = $featureDTO->getNotesFeatureId();
                    $connectedVal = $featureDTO->getIsNotesEnabled();
                    $id = $featureDTO->getNotesOrgId();
                    break;
                case 3:
                    $feature = $featureDTO->getLogContactsFeatureId();
                    $connectedVal = $featureDTO->getIsLogContactsEnabled();
                    $id = $featureDTO->getLogContactsOrgId();
                    break;
                case 4:
                    $feature = $featureDTO->getBookingFeatureId();
                    $connectedVal = $featureDTO->getIsBookingEnabled();
                    $id = $featureDTO->getBookingOrgId();
                    break;
                case 5:
                    $feature = $featureDTO->getStudentReferralNotificationFeatureId();
                    $connectedVal = $featureDTO->getIsStudentReferralNotificationEnabled();
                    $id = $featureDTO->getStudentReferralNotificationOrgId();
                    break;
                case 6:
                    $feature = $featureDTO->getReasonRoutingFeatureId();
                    $connectedVal = $featureDTO->getIsReasonRoutingEnabled();
                    $id = $featureDTO->getReasonRoutingOrgId();
                    break;
                case 7:
                        $feature = $featureDTO->getEmailFeatureId();
                        $connectedVal = $featureDTO->getIsEmailEnabled();
                        $id = $featureDTO->getEmailOrgId();
                        break;
                case 8:
                    $feature = $featureDTO->getPrimaryCampusConnectionReferralRoutingFeatureId();
                    $connectedVal = $featureDTO->getIsPrimaryCampusConnectionReferralRoutingEnabled();
                    $id = $featureDTO->getPrimaryCampusConnectionReferralRoutingOrgId();
                    break;
                default:
                    throw new ValidationException([
                        ' Invalid Feature  found.'
                    ], 'Invalid  Feature  found.', self::FEATURE_NOT_FOUND);
                    break;
            }
            
            $orgFeature = $this->featureRepository->findOneBy(array(
                'id' => $id,
                self::FIELD_ORGANIZATION => $organization,
                'feature' => $feature
            ));
            if ($orgFeature) {
                $orgFeature->setConnected($connectedVal);
            } else {
                throw new ValidationException([
                    $val . ' not found.'
                ], $val . '  not found.', self::FEATURE_NOT_FOUND);
            }
        }
    }

    /**
     * get Top Level Feature Status
     *
     * @param
     *            $organizationId
     * @return array|\Synapse\CoreBundle\Entity\FeatureMaster[]|\Synapse\RestBundle\Entity\Error
     */
    public function getListMasterFeaturesStatus($organizationId)
    {
        $this->rbacManager->checkAccessToOrganization($organizationId);

        $this->logger->debug(">>>> Get List Master Features Status for Organization Id" . $organizationId);

        $this->featureRepository = $this->repositoryResolver->getRepository(self::ORGFEATURES_REPO);
        $organization = $this->orgService->find($organizationId);
        foreach (FeatureService::$features as $key => $val) {
            $this->createFeature($organization, $key, $val);
        }
        
        $results = $this->featureRepository->getListFeatures($organization);
        $output = array();
        $index = 0;
        foreach ($results as $result) {
            $output[self::FIELD_ORG_ID] = $result[self::FIELD_ORG_ID];
            $feature_id = $result[self::FIELD_FEATUREID];
            $feature_connected = $result[self::FIELD_CONNECTED];
            
            $output[FeatureService::$reponseFeatureKey[$index] . "_" . self::FIELD_FEATUREID] = $feature_id;
            // $output[FeatureService::$reponseFeatureKey[$index] . "_org_id"] = $feature_org_id;
            $output["is_" . FeatureService::$reponseFeatureKey[$index] . "_enabled"] = $feature_connected;
            $index ++;
        }
        $this->logger->info(">>>> Get List Master Features Status for Organization Id" );
        return $output;
    }

    public function listFeatures($langid)
    {
        $this->logger->debug(">>>> list Feature for lang id" . $langid);
        $this->featureMasterLangRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:FeatureMasterLang");
        $this->languageMasterRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Languagemaster");
        $lang = $this->languageMasterRepository->find($langid);
        if (! isset($lang)) {
            $this->logger->error("Feature Service - listFeatures - Language Not Found " . $langid);
            throw new ValidationException([
                'Language Not Found.'
            ], 'Language Not Found', 'lang_not_found');
        }
        
        $responseArray = array();
        $responseArray["langid"] = $langid;
        $features = $this->featureMasterLangRepository->listFeaturesAll($langid);
        
        if (count($features) > 0) {
            $index = 0;
            foreach ($features as $feature) {
                $responseArray["features"][$index]["feature_name"] = $feature['feature_name'];
                $responseArray["features"][$index][self::FIELD_FEATUREID] = $feature[self::FIELD_FEATUREID];
                $index += 1;
            }
        }
        $this->logger->info(">>>> list Feature for lang id" );
        return $responseArray;
    }

    /**
     * needs to remove this funtion with the permission
     *
     * @param unknown $userId            
     * @return multitype:NULL multitype:multitype:NULL
     */
    public function getFeaturesStatusByOrg($userId)
    {
        $this->logger->debug(">>>>getFeatures Status By Org" );
        $this->featureRepository = $this->repositoryResolver->getRepository(self::ORGFEATURES_REPO);
        $results = $this->featureRepository->getListFeatures($userId);
        $response = [];
        if (count($results)) {
            $index = 0;
            foreach ($results as $result) {
                if (isset($result[self::FEATURE_NAME]) && ! empty($result[self::FEATURE_NAME])) {
                    $response[strtolower(str_replace(" ", "_", $result[self::FEATURE_NAME]))] = (bool) $result[self::FIELD_CONNECTED];
                } else {
                    $response[FeatureService::$reponseFeatureKey[$index]] = (bool) $result[self::FIELD_CONNECTED];
                }
                $index ++;
            }
        }
        $this->logger->info(">>>>getFeatures Status By Org" );
        return $response;
    }
    
    private function updateReasonRoutingList($subCategory, $organization)
    {
        $reason = $this->referralRoutingRulesRepo->findOneBy(array(
            self::FIELD_ORGANIZATION => $organization,
            'activityCategory' => $subCategory['reason_id']
        ));
        if($reason)
        {
            if($subCategory[self::FIELD_PRIMARY_COORDINATOR])
            {
                $reason->setIsPrimaryCoordinator(true);
                $reason->setPerson(NULL);
                $reason->setIsPrimaryCampusConnection(false);
            }elseif($subCategory[self::FIELD_PRIMARY_CAMPUS_CONNECTION]){
                //Primary campus connection
                $reason->setIsPrimaryCoordinator(false);
                $reason->setPerson(NULL);                         
                $reason->setIsPrimaryCampusConnection(true);
                
            }else{
                $reason->setIsPrimaryCoordinator(false);
                $person = $this->personService->find($subCategory[self::FIELD_PERSONID]);
                $reason->setPerson($person);
                $reason->setIsPrimaryCampusConnection(false);
            }
        }
        $this->featureRepository->flush();
    }
    
    private function setReasonRoutingList($routingReason, $organization)
    {
        $reasonRouting = $this->referralRoutingRulesRepo->findOneBy(array(
            self::FIELD_ORGANIZATION => $organization,
            'activityCategory' => $routingReason['subitem_key']
        ));
        if(! $reasonRouting){
            $refRoutingRules = new ReferralRoutingRules();
            $actCategory = $this->activityCategoryRepository->findOneById($routingReason['subitem_key']);
            $refRoutingRules->setActivityCategory($actCategory);
            $refRoutingRules->setOrganization($organization);
            $refRoutingRules->setIsPrimaryCoordinator(true);
            $this->referralRoutingRulesRepo->persist($refRoutingRules);
        }
    }


    /**
     * This will do a check that should be used within a job. This check will see if the staff
     * has permission to create/view a feature for a given student.
     *
     * @param int $staffId
     * @param int $orgId
     * @param int $studentId
     * @param string $shareOptionPermission => gotten from AbstractService::getShareOptionPermission($asset, $assetName)
     *                              Looks like:
     *                                  <feature_name>-private-create
     *                                  <feature_name>-public-create
     *                                  <feature_name>-teams-create
     *                              For reason routed referrals, looks like:
     *                                   reason-routed-referrals-public-create
     * @param int $featureId
     * @param boolean $reasonRoutingFlag
     * @return bool
     */
    public function verifyFacultyAccessToStudentForFeature($staffId, $orgId, $studentId, $shareOptionPermission, $featureId, $reasonRoutingFlag = false)
    {
        // Job specific check
        $permissionsetsWithStudent = $this->orgGroupFacultyRepository->getPermissionsByFacultyStudent($staffId, $studentId);
        if (count($permissionsetsWithStudent) == 0) {
            // If the person does not have access to the feature for this student, return false
            return false;
        }

        // Unnest the permissionset ids from the return results above
        $permissionsetArray = array_column($permissionsetsWithStudent, 'org_permissionset_id');

        // what access does the person have to the students
        $featureAccess = $this->orgPermissionsetFeaturesRepository->getFeaturePermissions($permissionsetArray, $orgId, $featureId);

        // transform the feature type: <feature>-<private, public, team>-create into something comparable: <private, public, team>_create
        $comparableShareOptionPermissionArray = explode("-", $shareOptionPermission);

        if ($reasonRoutingFlag) {
            $comparableShareOptionPermission = $comparableShareOptionPermissionArray[0] . "_" . $comparableShareOptionPermissionArray[2] . "_" . $comparableShareOptionPermissionArray[3] . "_" . $comparableShareOptionPermissionArray[4];
        } else {
            $comparableShareOptionPermission = $comparableShareOptionPermissionArray[1] . "_" . $comparableShareOptionPermissionArray[2];
        }

        if ($featureAccess[$comparableShareOptionPermission] == 1) {
            return true;
        }

        return false;
    }


    /**
     * Get the feature_master_lang name value of the feature based on the passed in value.
     *
     * @param string $featureName
     * @return string
     */
    public function mapFeatureNames($featureName)
    {
        $featureNameMap = [
            'appointment' => 'Booking',
            'contact' => 'Log Contacts',
            'email' => 'Email',
            'note' => 'Notes',
            'referral' => 'Referrals'
        ];

        return $featureNameMap[$featureName];
    }

}