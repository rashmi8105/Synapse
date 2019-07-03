<?php
namespace Synapse\CoreBundle\Security\Authorization\TinyRbac;

use Doctrine\Common\Cache\RedisCache;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OwnableAssetEntityInterface;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\OrgPermissionsetRepository;
use Synapse\CoreBundle\Repository\OrgPersonStudentYearRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\TeamMembersRepository;
use Synapse\CoreBundle\Repository\TeamsRepository;
use Synapse\CoreBundle\Service\Impl\OrgPermissionsetService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\UserManagementService;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\RestBundle\Entity\OrganizationDTO;


/**
 * TinyRbac\Manager interfaces between Synapse and TinyRbac\Rbac.
 *
 * It handles the creating, saving, loading, and caching of the users'
 * AccessTrees, while also dispatching RBAC authorization checks to
 * the TinyRbac\Rbac object.
 *
 * It caches each user's AccessTree in two locations: First [primary]
 * in the user's session object, and secondarily in Redis.
 *
 * @package Synapse\CoreBundle\Security\Authorization\TinyRbac
 */
class Manager
{
    const SERVICE_KEY = 'tinyrbac.manager';

    const SESSION_KEY = 'tinyrbac.accessTree';

    const BLOCKID = 'blockId';

    const PERMISSION_TEMP = 'permission_templates';

    const ACCESSLEVEL = 'accessLevel';

    const FEATURES = 'features';

    const SHARE = 'Share';

    const GROUPS = 'groups';

    const COURSESACCESS = 'coursesAccess';

    const VIEWCOURSES = 'viewCourses';

    const CREATEVIEWACADEMICUPDATE = 'createViewAcademicUpdate';

    const VIEWALLACADEMICUPDATECOURSES = 'viewAllAcademicUpdateCourses';

    const VIEWALLFINALGRADES = 'viewAllFinalGrades';

    const STUDENTS = 'students';

    const ORG_FEATURES = 'org_features';

    // User Options
    /**
     *
     * @var bool
     */
    protected $authEnabled = true;

    protected static $cacheTTL = 600;

    protected static $longerCacheTTL = 1800;

    // Scaffolding

    /**
     *
     * @var RedisCache
     */
    protected $cache;

    /**
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     *
     * @var EntityManager
     */
    protected $em;

    /**
     *
     * @var Rbac
     */
    protected $rbac;

    /**
     *
     * @var Session
     */
    protected $session;

    /**
     *
     * @var SecurityContext
     */
    protected $securityContext;


    // Services

    /**
     * @var AcademicYearService
     */
    private $academicYearService;

    /**
     *
     * @var OrgPermissionsetService
     */
    private $orgPermissionsetService;

    /**
     * @var PersonService
     */
    private $personService;
    
    /**
     * @var UserManagementService
     */
    private $userManagementService;


    // Repositories

    /**
     * @var OrganizationRoleRepository
     */
    private $organizationRoleRepository;

    /**
     * @var OrgPermissionsetRepository
     */
    private $orgPermissionsetRepository;

    /**
     * @var OrgPersonStudentYearRepository
     */
    private $orgPersonStudentYearRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;


    private $accessMap = array(
        0
    );

    /**
     *
     * @param EntityManager $em
     * @param Session $session
     * @param SecurityContext $securityContext
     * @param $cache
     * @param $orgPermissionsetService
     * @param Rbac $rbac
     * @param array $rbacOptions
     */
    public function __construct(EntityManager $em, Session $session, SecurityContext $securityContext, $cache, $orgPermissionsetService, $container, Rbac $rbac, array $rbacOptions = null)
    {
        // Scaffolding
        $this->handleOptions($rbacOptions);
        $this->cache = $cache;
        $this->container = $container;
        $this->em = $em;
        $this->rbac = $rbac;
        $this->session = $session;
        $this->securityContext = $securityContext;

        // Services
        $this->orgPermissionsetService = $orgPermissionsetService;

        // Repositories
        $this->organizationRoleRepository = $this->em->getRepository(OrganizationRoleRepository::REPOSITORY_KEY);
        $this->orgPermissionsetRepository = $this->em->getRepository(OrgPermissionsetRepository::REPOSITORY_KEY);
        $this->orgPersonStudentYearRepository = $this->em->getRepository(OrgPersonStudentYearRepository::REPOSITORY_KEY);
        $this->personRepository = $this->em->getRepository(PersonRepository::REPOSITORY_KEY);


        // If this is a Resque job then we want to disable authorization since there is no real logged in user
        // and all authorization in this class is in reference to the logged in user.
        if ($this->isThisProcessAResqueJob()){
            $this->setAuthEnabled(false);
        }

    }

    /**
     * Returns true if this function is called from code where the 'Resque_Job' class is found in its
     * stack trace.
     *
     */
    private function isThisProcessAResqueJob()
    {
        // I researched to confirm that there was no significant performance hit when using "debug_backtrace()".  I found
        // some references to it actually being faster than using reflection, but nothing to definitively substantiate performance
        // one way or the other.  So, I looked at the code for ths debug_backtrace() function and confirmed the function is
        // only formatting data that is already available in memory.  It is not generating anything new.
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);        
        foreach ($trace as $step) {            
            if (isset($step['class']) && $step['class'] === 'Resque_Job') {
                return true;
            }
        }

        return false;
    }

    protected function handleOptions($rbacOptions)
    {
        if (empty($rbacOptions) || ! is_array($rbacOptions)) {
            return;
        }

        if (isset($rbacOptions['enable_auth'])) {
            $this->authEnabled = $rbacOptions['enable_auth'];
        }

        if (isset($rbacOptions['cache_ttl'])) {
            self::$cacheTTL = $rbacOptions['cache_ttl'];
        }
        if (isset($rbacOptions['longer_cache_ttl'])) {
            self::$longerCacheTTL = $rbacOptions['longer_cache_ttl'];
        }
    }

    public function setAuthEnabled($status)
    {
        $this->authEnabled = $status;
    }

    /**
     *
     * @return Rbac
     */
    public function getRbac()
    {
        return $this->rbac;
    }

    /**
     *
     * @param
     *            $userId
     * @return array|mixed|null
     */
    public function initializeForUser($userId = null)
    {
        $cacheDataClosure = function ($userId)
        {
            // Fetch the user's group permissions from Doctrine.
            $permissionsInfo = $this->orgPermissionsetService->getPermissionSetsByUser($userId);
            $accessTree = $this->createAccessTree($permissionsInfo, $userId);
            $this->cacheUserAccessTree($userId, $accessTree);

            return $accessTree;
        };
        if (! $userId) {
            $userId = $this->grabCurrentUserId();
            if (! $userId) {
                return false;
            }
        }

        $accessTree = self::fetchUserAccessTreeFromCache($userId, $this->session, $this->cache);
        if (empty($accessTree) || ! is_array($accessTree)) {
            $accessTree = $cacheDataClosure($userId);
        }

        // If accessMap is equal to array(0), it will tell us it was never fetched from a cache.
        // By keeping it an array (as opposed to null [false-positive], it also will not break
        // other parts of the app that expect arrays or null [which setting it to false would do.
        if ($this->accessMap === array(
            0
        )) {
            $cacheKey = self::getCacheKey($userId);
            $this->accessMap =\json_decode($this->cache->fetch($cacheKey . '-map'), true);
            // Added a check for redis being cleared but session persisting.
            if (! $this->accessMap) {
                $accessTree = $cacheDataClosure($userId);
            }
        }

        $this->rbac->setAccessTree($accessTree);

        $accessMap = $this->getAccessMap($userId);

        return $accessTree;
    }

    public function refreshPermissionCache($userId = null)
    {
        if (! $userId) {
            $userId = $this->grabCurrentUserId();
            if (! $userId) {
                return false;
            }
        }
        //delete user cache in next login
        $cacheKey = self::getCacheKey($userId);
        $this->cache->delete($cacheKey);
        $this->cache->delete($cacheKey . '-map');
        // Fetch the user's group permissions from Doctrine.
        $permissionsInfo = $this->orgPermissionsetService->getPermissionSetsByUser($userId);
        $accessTree = $this->createAccessTree($permissionsInfo, $userId);
        $this->cacheUserAccessTree($userId, $accessTree);
        return $accessTree;
    }

    /**
     *
     * @param
     *            $userId
     * @param Session $session
     * @param \Redis $redisCache
     * @return mixed|null
     */
    public static function fetchUserAccessTreeFromCache($userId, Session $session, $redisCache = null)
    {
        $accessTree = null;
        $cacheKey = self::getCacheKey($userId);

        // Fetch from redis, if available.
        if (! $redisCache) {
            return null;
        }

        // Fetch from redis, if available.
        $accessTree =\json_decode($redisCache->fetch($cacheKey), true);

        return $accessTree;
    }

    /**
     * Converts the complex permissionsInfo object into a RBAC-parseable nested array.
     *
     * @param array $permissionsInformation
     * @param int $userId
     * @return array
     */
    public function createAccessTree(array $permissionsInformation, $userId)
    {
        $students = [];
        $personObject = $this->personRepository->find($userId);
        // Get User Organization Id
        $personOrganizationId = $personObject->getOrganization()->getId();
        // get all student for the staff
        if ($userId) {
            $this->academicYearService = $this->container->get(AcademicYearService::SERVICE_KEY); // was not declared in constructor for circular reference
            $currentOrgAcademicYearId = $this->academicYearService->getCurrentOrgAcademicYearId($personOrganizationId);
            $students = $this->orgPermissionsetRepository->getStudentsForStaff($userId, $currentOrgAcademicYearId);
        }
        $groupsIds = [];
        //getting org features
        $organizationFeatures = $this->orgPermissionsetService->getOrgFeatures($userId);
        $orgFeatureArray = array();
        foreach ($organizationFeatures as $feature) {
            if ($feature['featureName'] && $feature['connected']) {
                $orgFeatureArray[$feature['featureName']] = $feature['connected'];
            }
        }
        // Initialize the accessMap.
        $this->accessMap = [];
        // Initialize the blank variables for the closure...
        $accessTree = [
            '/' => []
        ];
        $accessTree['/']['organizations'][$personOrganizationId] = '*';
        $template = [];
        $parsePermissionClosure = function ($permissionType) use(&$accessTree, &$template)
        {
            if (! empty($template[$permissionType])) {

                foreach ($template[$permissionType] as $innerIndex => $data) {
                    $blockId = '';
                    if (isset($permissionType) && isset($data["blockId"])) {
                        $permissionName = $permissionType . '-' . $data["blockId"];
                        $blockId = $data["blockId"];
                        $blockName = (isset($data['blockName'])) ? $data['blockName'] : '';
                    } else {
                        $blockName = '';
                        $permissionName = $permissionType . '-' . $data['id'];
                        $blockId = $data['id'];
                        if(array_key_exists('itemLabel', $data)){
                            if(isset($data['itemLabel'])){
                                $blockName = $data['itemLabel'];
                            }

                        }else if (array_key_exists('name', $data)){
                            $blockName = $data['name'];
                        }
                    }
                    if(isset($data['blockSelection'])){
                    $permissionValue = ($data['blockSelection']) ? '*' : '';
                    }
                    else{
                        $permissionValue = ($data['selection']) ? '*' : '';
                    }
                    if ($permissionValue != '*' && isset($accessTree['/'][$permissionType][$permissionName])) {
                        // Always favor Access over Denial.
                        if ($accessTree['/'][$permissionType][$permissionName] === '*') {
                            $permissionValue = '*';
                        } elseif ($accessTree['/'][$permissionType][$permissionName] === '-') {
                            // Favor Denial over No Opinion.
                            $permissionValue = '-';
                        } else {
                            $permissionValue = '';
                        }
                    } else {
                        if(isset($data['blockSelection'])){
                            $permissionValue = ($data['blockSelection']) ? '*' : '';
                        }
                        else{
                            $permissionValue = ($data['selection']) ? '*' : '';
                        }
                    }
                    $accessTree['/'][$permissionType][$permissionName] = $permissionValue;
                    if(isset($data['shortCode'])){
                        $this->accessMap[$permissionType][$innerIndex] = array(
                            'id' => $blockId,
                            'name' => $blockName,
                            'value' => $permissionValue,
                            'shortCode' =>$data['shortCode']
                        );
                    }
                    else {
                        $this->accessMap[$permissionType][$innerIndex] = array(
                            'id' => $blockId,
                            'name' => $blockName,
                            'value' => $permissionValue
                        );
                    }
                    // Help GC do its thing.
                    unset($template[$permissionType][$innerIndex]);
                }
            }
        };
        // To set no any permission will return an empty array
        if (! empty($permissionsInformation) || (isset($permissionsInformation["permission_templates"]) && ! empty($permissionsInformation["permission_templates"]))) {

            $permissionTemplates = $permissionsInformation["permission_templates"];
            // We're going to handle the memory management here. Faster, and safer. If you remember to unset the foreach stuff...
            //gc_disable();

            $encoders = [
                new JsonEncoder()
            ];
            $normalizers = [
                new GetSetMethodNormalizer()
            ];
            $serializer = new Serializer($normalizers, $encoders);

            $featuresMap = [];
            $this->accessMap["coursesAccess"]["viewCourses"] = false;
            $accessTree['/']["coursesAccess"]["viewCourses"] = '';
            $this->accessMap["coursesAccess"]["createViewAcademicUpdate"] = false;
            $accessTree['/']["coursesAccess"]["createViewAcademicUpdate"] = '';
            $this->accessMap["coursesAccess"]["viewAllAcademicUpdateCourses"] = false;
            $accessTree['/']["coursesAccess"]["viewAllAcademicUpdateCourses"] = '';
            $this->accessMap["coursesAccess"]["viewAllFinalGrades"] = false;
            $accessTree['/']["coursesAccess"]["viewAllFinalGrades"] = '';

            foreach ($permissionTemplates as $loopIndex => $template) {
                $template = $serializer->serialize($template, 'json');
                $template = json_decode($template, true);

                // Load access levels
                if (! empty($template["accessLevel"])) {
                    foreach ($template["accessLevel"] as $accessLevel => $isOn) {
                        if ($isOn) {
                            $accessTree['/']["accessLevel"][$accessLevel] = '*';
                        }
                        // Help the GC *and* increase the efficiency of the parsing by a great deal.
                        unset($template["accessLevel"][$accessLevel]);
                    }
                }
                if(isset($template['organizationId'])){
                    $accessTree['/']['organizations'][$template['organizationId']] = "*";
                }
                if ($template['riskIndicator']) {
                    $accessTree['/']['riskIndicator'] = '*';
                }
                if ($template['intentToLeave']) {
                    $accessTree['/']['intentToLeave'] = '*';
                }

                if ($template['retentionCompletion']) {
                    $accessTree['/']['retentionCompletion'] = '*';
                }


                $parsePermissionClosure('reportsAccess');
                $parsePermissionClosure('profileBlocks');
                $parsePermissionClosure('isp');
                $parsePermissionClosure('isq');
                $parsePermissionClosure('surveyBlocks');

                if (! empty($template["coursesAccess"])) {
                    if ($template["coursesAccess"]["viewCourses"] && $accessTree['/']["coursesAccess"]["viewCourses"] != '*') {
                        $accessTree['/']["coursesAccess"]["viewCourses"] = '*';
                        $this->accessMap["coursesAccess"]["viewCourses"] = true;
                    }

                    if ($template["coursesAccess"]["createViewAcademicUpdate"] && $accessTree['/']["coursesAccess"]["createViewAcademicUpdate"] != '*') {
                        $accessTree['/']["coursesAccess"]["createViewAcademicUpdate"] = '*';
                        $this->accessMap["coursesAccess"]["createViewAcademicUpdate"] = true;
                    }

                    if ($template["coursesAccess"]["viewAllAcademicUpdateCourses"] && $accessTree['/']["coursesAccess"]["viewAllAcademicUpdateCourses"] != '*') {
                        $accessTree['/']["coursesAccess"]["viewAllAcademicUpdateCourses"] = '*';
                        $this->accessMap["coursesAccess"]["viewAllAcademicUpdateCourses"] = true;
                    }

                    if ($template["coursesAccess"]["viewAllFinalGrades"] && $accessTree['/']["coursesAccess"]["viewAllFinalGrades"] != '*') {
                        $accessTree['/']["coursesAccess"]["viewAllFinalGrades"] = '*';
                        $this->accessMap["coursesAccess"]["viewAllFinalGrades"] = true;
                    }
                }

                if (! empty($template["features"]) && (($accessTree['/']["accessLevel"]["individualAndAggregate"]) && $accessTree['/']["accessLevel"]["individualAndAggregate"] == '*')) {
                    // Only add features permissions if they do NOT have the "aggregate-only" permission set to true.
                    $featureCount = 0;
                    foreach ($template["features"] as $index => $feature) {
                        $featureNameKey = str_replace(' ', '_', strtolower($feature['name']));

                        if (isset($orgFeatureArray[$feature['name']]) && ! $orgFeatureArray[$feature['name']]) {
                            continue;
                        }
                        $featureId = $feature['id'];
                        //Process it for referral
                        if($feature['id'] == 1){

                            foreach ($feature as $key => $value) {
                                $mapKey = $key;
                                if($key == 'directReferral' || $key == 'reasonRoutedReferral'){
                                    $referralShareType = $key;
                                    $referralShare = $value;
                                   //processing permission for referral share
                                    foreach ($referralShare as $index => $referralSharePermissionValue) {

                                    if (strpos($index, "Share")) {

                                        $mapKey = str_replace("Share", '_share', $index);
                                        $featureName = $featureNameKey . '-' . str_replace("Share", '', $index);
                                        if($referralShareType == 'reasonRoutedReferral'){
                                            $featureName = 'reason-routed-'.$featureName;
                                            $mapRefKey = 'reason_routed_referral';
                                        }
                                        else {
                                            $mapRefKey = 'direct_referral';
                                        }
                                        $featureNameView = $featureName . '-view';
                                        $featureNameCreate = $featureName . '-create';

                                        $viewPerm = ($referralSharePermissionValue['view']) ? '*' : '';
                                        $createPerm = ($referralSharePermissionValue['create']) ? '*' : '';

                                        if (isset($accessTree['/']["features"][$featureNameView]) && isset($accessTree['/']["features"][$featureNameCreate])) {
                                            if ($accessTree['/']["features"][$featureNameView] != '*') {
                                                $accessTree['/']["features"][$featureNameView] = $viewPerm;
                                            }
                                            if ($accessTree['/']["features"][$featureNameCreate] != '*') {

                                                $accessTree['/']["features"][$featureNameCreate] = $createPerm;
                                            }
                                        } else {
                                            $accessTree['/']["features"][$featureNameCreate] = $createPerm;
                                            $accessTree['/']["features"][$featureNameView] = $viewPerm;
                                        }

                                        // Always grant view access if create is also granted.
                                        if ($accessTree['/']["features"][$featureNameCreate] === '*') {
                                            $accessTree['/']["features"][$featureNameView] = '*';
                                            $referralSharePermissionValue['view'] = true;
                                        }

                                        $featuresMap[$featureId][$mapRefKey][$mapKey] = [
                                        'view' => ($accessTree['/']["features"][$featureNameView] === '*'),
                                        'create' => ($accessTree['/']["features"][$featureNameCreate] === '*')
                                        ];
                                    }
                                    }
                                } else
                                    if ($key == 'receiveReferrals') {
                                        $featureName = strtolower((substr($key, 0, strpos($key, 'Referrals')) . "_" . (substr($key, strpos($key, 'Referrals')))));
                                        if (isset($accessTree['/']["features"][$featureName])) {
                                            if ($accessTree['/']["features"][$featureName] != '*') {
                                                $accessTree['/']["features"][$featureName] = ($value) ? '*' : '';
                                            }
                                        } else {
                                            $accessTree['/']["features"][$featureName] = ($value) ? '*' : '';
                                        }

                                        $featuresMap[$featureId]['receiveReferrals'] = $value;
                                    } else {
                                        if (! empty($value)) {
                                            $featuresMap[$featureId][$mapKey] = $value;
                                        }
                                    }
                            }
                        }
                        else {
                        foreach ($feature as $key => $value) {
                            $mapKey = $key;
                            if (strpos($key, "Share")) {
                                $mapKey = str_replace("Share", '_share', $key);
                                $featureName = $featureNameKey . '-' . str_replace("Share", '', $key);
                                $featureNameView = $featureName . '-view';
                                $featureNameCreate = $featureName . '-create';

                                $viewPermission = ($value['view']) ? '*' : '';
                                $createPermission = ($value['create']) ? '*' : '';

                                if (isset($accessTree['/']["features"][$featureNameView]) && isset($accessTree['/']["features"][$featureNameCreate])) {
                                    if ($accessTree['/']["features"][$featureNameView] != '*') {
                                        $accessTree['/']["features"][$featureNameView] = $viewPermission;
                                    }
                                    if ($accessTree['/']["features"][$featureNameCreate] != '*') {

                                        $accessTree['/']["features"][$featureNameCreate] = $createPermission;
                                    }
                                } else {
                                    $accessTree['/']["features"][$featureNameCreate] = $createPermission;
                                    $accessTree['/']["features"][$featureNameView] = $viewPermission;
                                }

                                // Always grant view access if create is also granted.
                                if ($accessTree['/']["features"][$featureNameCreate] === '*') {
                                    $accessTree['/']["features"][$featureNameView] = '*';
                                    $value['view'] = true;
                                }

                                $featuresMap[$featureId][$mapKey] = [
                                    'view' => ($accessTree['/']["features"][$featureNameView] === '*'),
                                    'create' => ($accessTree['/']["features"][$featureNameCreate] === '*')
                                ];
                            }  else {
                                    if (! empty($value)) {
                                        $featuresMap[$featureId][$mapKey] = $value;
                                    }
                                }
                        }
                        }
                        ++ $featureCount;
                    }
                }

                if (! empty($template["groups"])) {
                    foreach ($template["groups"] as $groupId => $groupName) {
                        // Groups should *definitely* be in a higher-level than, say, profile_blocks,
                        // mainly to avoid duplicity.
                        $accessTree['/']["groups"][$groupId] = $groupName;
                        $groupsIds["groups"][$groupId] = $groupName;
                        // Help the GC *and* increase the efficiency of the parsing by a great deal.
                        unset($template["groups"][$groupId]);
                    }
                }

                // Do the GC now (istead of once per iteration) *and* increase
                // the efficiency of the parsing by a great deal.
                //gc_collect_cycles();
            }
        }

        $accessTree['/']["students"] = $students;
        $accessTree['/']["org_features"] = array_keys($orgFeatureArray);
        // For the love of all that is good and holy, do not remove this.
        //gc_enable();

        $this->accessMap["features"] = $featuresMap;
        $this->accessMap["students"] = $students;
        $this->accessMap["org_features"] = array_keys($orgFeatureArray);
        $this->accessMap["groups"] = isset($groupsIds["groups"]) ? $groupsIds["groups"] : '';

        return $accessTree;
    }

    public function getAccessMap($userId = null)
    {
        if ($userId) {
            $cacheKey = self::getCacheKey($userId);
        } else {
            $userId = $this->grabCurrentUserId();
            $cacheKey = self::getCacheKey($userId);
        }
        $map = $this->cache->fetch($cacheKey . '-map');
        if (! empty($map)) {
            return $this->accessMap = \json_decode($map, true);
        } else {
            return array();
        }
    }

    /**
     * Caches the user accessTree.
     *
     * @param
     *            $userId
     * @param
     *            $accessTree
     */
    public function cacheUserAccessTree($userId, $accessTree)
    {
        $cacheKey = self::getCacheKey($userId);
        $this->cache->save($cacheKey, json_encode($accessTree), self::$cacheTTL);
        $this->cache->save($cacheKey . '-map', json_encode($this->accessMap), self::$cacheTTL);
    }

    public function formatNameUpperCase($featureName)
    {
        $name = '';
        if ($featureName) {
            $name = (strpos($featureName, '_')) ? ucwords(str_replace('_', ' ', $featureName)) : ucwords($featureName);
        }
        return $name;
    }

    /**
     * Check organization feature
     *
     * @param unknown $permission
     * @return boolean
     */
    private function checkOrgFeature($permission)
    {
        $accessTree = $this->rbac->getAccessTree();

        $orgFeatures = (isset($accessTree['/'][self::ORG_FEATURES])) ? $accessTree['/'][self::ORG_FEATURES] : array();

        if (! is_array($permission)) {
            $featureName = $this->formatNameUpperCase($permission);
        } else {
            $perm = explode('-', $permission[0]);
            $featureName = $this->formatNameUpperCase($perm[0]);
        }

        if (in_array($featureName, $orgFeatures)) {
            return true;
        }
        return false;
    }

    /**
     * Dispatches requests to TinyRbac/Rbac.
     * Main integration point for Synapse.
     *
     * @param  $permission
     * @return bool
     */
    public function hasAccess($permission)
    {
        if (! $this->authEnabled) {
            return true;
        }
        // Check for access only coordinator setup tab
        if (in_array('coordinator-setup', $permission)) {
            // Check if they are the organization's coordinator.
            // $orgFeatureAccess = $this->checkOrgFeature($permission);
            if ($this->hasCoordinatorAccess()) {
                return true;
            }
            return false;
        }

        if (! is_array($permission)) {
            return $this->rbac->hasAccess($permission);
        }

        foreach ($permission as $perm) {
            if ($this->rbac->hasAccess($perm)) {
                // If it got this far, it has access.
                return true;
            }
        }
        // If it got this far, it does not have access.
        return false;
    }

    /**
     * Checks to see if the logged in user has access to the organization that the passed in $personId is associated with.
     * Throws an AccessDeniedException if they do not.
     *
     * Because the organization for the passed in person must be looked up in the database, a call to this function can
     * incur the cost of a database hit.  To alleviate this, this method caches the orgId retrieved from the
     * database for the passed in $personId for about 30 minutes.
     *
     * All in all, it will always be cheaper and thus better to call the checkAccessToOrganization() method and pass in
     * an OrgId directly if one is available.
     *
     * @param $personId
     */
    function checkAccessToOrganizationUsingPersonId($personId=null){

        if (! $this->authEnabled) {
            return;
        }

        if($personId){
        	$personId = (is_object($personId))?$personId->getId():$personId;
        }else{
        	$personId = $this->grabCurrentUserId();
        }
        // Checking to see if it is in the cache already.
        $cacheKey = "tinyrbac:personOrgIdFor:$personId";
        $personOrgId = $this->cache->fetch($cacheKey);
        if (empty($personOrgId)) {
            // Not in the cache so look it up
            $personObj = $this->em->getRepository(PersonConstant::PERSON_REPO)->find($personId);
            if (isset($personObj)){
                $personOrgId = $personObj->getOrganization()->getId();
                // Set it back in the cache for a while
                $this->cache->save($cacheKey, $personOrgId, self::$longerCacheTTL);
            } else {
                $personOrgId = -1;
            }
        }

        $this->checkAccessToOrganization($personOrgId);

    }

    /**
     * This function will attempt to find an organization id in the passed in $asset.  If it finds one, it will
     * call the checkAccessToOrganization($orgId) method.  If it does not, it will attempt to find a person id in the
     * passed in $asset and use that to obtain the organization id.
     *
     * If it doesn't find an organization id, IT WILL NOT CHECK ACCESS and will return.
     *
     * @param BaseEntity $asset
     */
    private function checkAccessToOrganizationUsingAsset(BaseEntity $asset)
    {

        if (! $this->authEnabled) {
            return;
        }

        $orgId = null;

        try {
            //We do this this way because somewhere along the line someone started having a few "getOrganization()"
            //methods return org ids and not objects.
            $orgId = $asset->getOrganization();
            if (is_object($orgId) && ($orgId instanceof Organization || $orgId instanceof OrganizationDTO)) {
                $orgId = $orgId->getId();
            }
        } catch (Exception $e){
            /*silently ignore exception*/}

        if (isset($orgId) && is_numeric($orgId)) {
            $this->checkAccessToOrganization($orgId);
        } else {
            //Couldn't find an organization Id.  Let's try for a student Id.
            //We do this a back up attempt because checking via a student Id is a touch more expensive then checking via an org id.
            try {
                $studentId = $asset->getPersonIdStudent()->getId();
                $this->checkAccessToOrganizationUsingPersonId($studentId);
            } catch (Exception $e){
                /*silently ignore exception*/}
        }
    }


    /**
     * Checks to see if the logged in user has access to the organization that is passed in.
     * Throws an AccessDeniedException if they do not.
     *
     * WARNING!!! OBVIOUSLY... one should make sure the passed in $organizationId
     * was NOT obtained by pulling the organizationId of the logged in person!! The passed in $organizationId MUST instead
     * be obtained from the person, student, note, team, asset, etc that the logged in person is trying access.
     *
     * @param $organizationId - make sure it is the id of the asset being accessed (see explanation above)
     */
    function checkAccessToOrganization($organizationId){

        if (! $this->authEnabled) {
            return;
        }

        if (! $this->rbac->hasComplexAccess("/organizations/".$organizationId)){
            //$this->logger->info("Tiny_RBAC: User does not have access to organization. Try to access OrgId: ".$organizationId);
            throw new AccessDeniedException('Unauthorized access to organization: '.$organizationId);
        }

    }

    /**
     *
     * @return Person|null
     */
    protected function grabCurrentUser()
    {
        // If not provided, fetch the userId from the session.
        $token = $this->securityContext->getToken();
      // User doesn't have an active session.
        if (empty($token)) {
            return null;
        }

        $user = $token->getUser();
     // The User isn't logged in.
        /*
         * Fixed an edge case where a visitor could be both authenticated (and pass the $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') check) but still be not logged in. In such case, the $token->getUser() method literally returns 'anon', not an object as expected. The instanceof checks for that.
         */
        if (! $user || ! ($user instanceof Person)) {
            return null;
        }
        $organization = $user->getOrganization()->getId();

        if ($organization === -1 && $this->container->has('proxy_user')) {
            $user = $this->container->get('proxy_user');
        }

        return $user;
    }

    /**
     *
     * @return int|null
     */
    protected function grabCurrentUserId()
    {
        $user = $this->grabCurrentUser();
        if (! $user) {
            return null;
        }

        return $user->getId();
    }

    /**
     * Returns true if the passed in $userId has public access to the passed in $asset.
     *
     * @param BaseEntity $asset
     * @return bool
     */
    private function hasPublicAccess(BaseEntity $asset)
    {

        if ($asset->getAccessPublic()) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if the passed in $userId has private access to the passed in $asset.
     * If the passed in $userId is null or not provided, then the currently logged in user's id is used.
     *
     * @param BaseEntity $asset
     * @param int $userId
     * @return bool
     */
    public function hasPrivateAccess(BaseEntity $asset, $userId = null)
    {

        if (! $this->authEnabled) {
            return true;
        }

        $this->checkAccessToOrganizationUsingAsset($asset);   //If the hasPrivateAccess() method is made private, we can get rid of this line

        if (! $userId) {
            $userId = $this->grabCurrentUserId();
            if (! $userId) {
                return false;
            }
        }
        if (! $asset->getAccessPrivate()) {
            return false;
        }
        //check if assign to loged in person
        if(method_exists($asset, 'getPersonAssignedTo')) {
            $assignedId = $asset->getPersonAssignedTo()->getId();
            if($userId == $assignedId){
                return true;
            }
        }
        // Private access only makes sense on Ownable assets.
        if (! ($asset instanceof OwnableAssetEntityInterface)) {
            throw new \LogicException('Can only check for private access on ownable assets.');
        }
        // If they're the faculty member responsible, grant access.
        if ($userId == $asset->getPersonIdFaculty()->getId()) {
            return true;
        }
        // If they're the student responsible, grant access.
        if ($asset->getPersonIdStudent()->getId() == $userId) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the passed in $userId has team access to the passed in $asset.
     * If the passed in $userId is null or not provided, then the currently logged in user's id is used.
     *
     * @param BaseEntity $asset
     * @param $userId
     * @return bool
     */
    public function hasTeamAccess(BaseEntity $asset, $userId = null)
    {
        if (! $this->authEnabled) {
            return true;
        }

        $this->checkAccessToOrganizationUsingAsset($asset);   //If the hasTeamAccess() method is made private, we can get rid of this line

        if (! $userId) {
            $userId = $this->grabCurrentUserId();
            if (! $userId) {
                return false;
            }
        }
        if (! $asset->getAccessTeam()) {
            return false;
        }
        //check if assign to loged in person
        if(method_exists($asset, 'getPersonAssignedTo')) {
            $assignedId = $asset->getPersonAssignedTo()->getId();
            if($userId == $assignedId){
                return true;
            }
        }

        /**
         *
         * @var TeamsRepository $teamsRepo
         */
        $teamsRepo = $this->em->getRepository('SynapseCoreBundle:Teams');
        $assetTeams = $teamsRepo->getTeamsByAsset($asset);

        // See if the asset even has teams assigned.
        if (empty($assetTeams)) {
            return false;
        }

        /**
         *
         * @var TeamMembersRepository $teamMembersRepo
         */
        $teamMembersRepo = $this->em->getRepository('SynapseCoreBundle:TeamMembers');
        $userTeams = $teamMembersRepo->getTeams($userId);

        // See if the user is assigned to any teams.
        if (empty($userTeams)) {
            return false;
        }

        // See if this user's team belongs to any of the teams that have access to the asset.
        // It only needs one.
        foreach ($userTeams as $userTeam) {
            $userTeamId = $userTeam['team_id'];
            if (! empty($assetTeams[$userTeamId])) {

                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the passed in $user has coordinator access.
     * If the $user is null or not included in the function call, then uses the currently logged in user.
     *
     * @param Person $user
     * @return bool
     */
    public function hasCoordinatorAccess(Person $user = null)
    {
        if (! $this->authEnabled) {
            return true;
        }

        if (! $user) {
            $user = $this->grabCurrentUser();
            if (! ($user instanceof Person)) {
                return false;
            }
        }
        $userId = $user->getId();

        // Caching this for efficiency sake, as this is heavy and called every time and is very static.
        $cacheKey = "[tinyrbac] isCoordinator-$userId";
        $isCoordinator = $this->cache->fetch($cacheKey);
        if (! empty($isCoordinator)) {
            // NOTE: RedisCache::get() returns FALSE on not-found, so substitute with strings.
            return ($isCoordinator === 'true');
        }

        $organizationId = $user->getOrganization()->getId();
        $isCoordinator = $this->organizationRoleRepository->getUserCoordinatorRole($organizationId, $userId);

        // RedisCache::get() returns FALSE on not-found, so substitute with strings.
        $cacheValue = ($isCoordinator) ? 'true' : 'false';
        $this->cache->save($cacheKey, $cacheValue, self::$cacheTTL);

        return ! empty($isCoordinator);
    }

    /**
     * Checks whether the logged in person has access to the student (and their organization) that is passed in as part
     * of the $asset (or passed in via the $studentId parameter if present)
     * AND whether the logged in user student has access to the permissions passed in in the $permissions parameter.  If all of this is
     * true then returns true.
     *
     * If the passed in $asset does not have an associated student/studentId, one MUST be provided via the optional $studentId parameter.
     *
     * @param Person $user
     * @return bool
     */
    public function hasStudentAccess($permissions, $asset, $studentId = null, $loggedinUserId = null)
    {

        if (! $this->authEnabled) {
            return true;
        }

        if($studentId == null) {
            $studentId = $asset->getPersonIdStudent()->getId();
        }
        if (is_object($studentId)) {
            $studentId = $studentId->getId();
        }

        $this->checkAccessToOrganizationUsingPersonId($studentId);   //If hasStudentAccess() method is made private, we can get rid of this line

        /*if ($this->hasCoordinatorAccess()) {
            return true;
        }*/

        //check if faculty has access to the student
        $allowedStudent = $this->rbac->getAccessTree();
        $allowedStudents = isset($allowedStudent['/'][self::STUDENTS]) ? $allowedStudent['/'][self::STUDENTS] : [];

        if (! in_array($studentId, $allowedStudents)) {
            return false;
        }

        //check if the student has access to the passed in $permissions
        if(!$loggedinUserId) {
            $loggedinUserId = $this->grabCurrentUserId();
        }

        $checkStudentAccess = $this->orgPermissionsetService->checkStudentPermission($studentId,$permissions,$loggedinUserId);
        if ($checkStudentAccess) {
            return true;
        }

        return false;
    }


    /**
     * Returns true if the passed in $facultyId has access to the passed in $studentId based on the passed in $permissions.
     * If the passed in $facultyId is null (or not included in the function call) then uses the Id of the currently logged in user.
     * If the passed in $studentId is null (or not included in the function call) then the passed in $asset is used to get the studentId from.
     *
     * @param $permissions
     * @param $asset
     * @param null $studentId
     * @param null $facultyId
     * @return bool
     */
    public function hasStudentAppointmentAccess($permissions, $asset, $studentId = null, $facultyId = null)
    {

        if (!$this->authEnabled) {
            return true;
        }

        if ($studentId == null) {
            $studentId = $asset->getPersonIdStudent()->getId();
        }
        if (is_object($studentId)) {
            $studentId = $studentId->getId();
        }

        $this->checkAccessToOrganizationUsingPersonId($studentId);

        if ($facultyId) {
            $facultyId = $facultyId;
        } else {
            $facultyId = $this->grabCurrentUserId();
        }

        //check if the student has access to the passed in $permissions
        $checkStudentAccess = $this->orgPermissionsetService->checkStudentPermission($studentId, $permissions, $facultyId);
        if ($checkStudentAccess) {
            return true;
        }
        return false;
    }


    /**
     * Returns true if the OrgPermissionsetService->checkUsersPermission with the passed in parameters and returns
     * the result of that call.
     *
     * @param $permissions
     * @param $personId
     * @return bool
     */
    public function checkUserPermission($permissions, $personId) {

        $checkUserAccess = $this->orgPermissionsetService->checkUsersPermission($permissions, $personId);
        if ($checkUserAccess) {
            return true;
        }
        return false;
	}

    /**
     * Returns true if the passed in $userId has access to the passed in $asset and can therefore "own" it.
     *
     * @param $asset
     * @param $userId
     * @return bool
     */
    public function checkIfOwnableAsses($asset, $userId){

        //check if assest created by logged in person
        if ($userId == $asset->getPersonIdFaculty()->getId()) {
            return true;
        }

        //Check appointment faculty from ARS
        if(is_a($asset,'Synapse\CoreBundle\Entity\Appointments')){
            $appointmentId = $asset->getId();
            $ars = $this->em->getRepository('SynapseCoreBundle:AppointmentRecepientAndStatus');
            $getFacultyId = $ars->getAppointmentFaculty($appointmentId);
            if ($userId == $getFacultyId) {
                return true;
            }
        }

        //check if referral assign to logged in person
        if(method_exists($asset, 'getPersonAssignedTo')) {
            $referralId = $asset->getId();
            $referral = $this->em->getRepository('SynapseCoreBundle:Referrals');
            $referralPersons = $referral->getAllReferralOwnablePerson($referralId);
            if(in_array($userId, $referralPersons)){
                return true;
            }
        }

        return false;
    }


    /**
     * Checks whether the logged in person has access to the organization associated with the passed in $asset.  Throws an exception if not.
     *
     * Then checks to see if the logged in user is a coordinator (i.e., has all access) and returns true if they are.
     *
     * Then checks to see if the logged in user has access to the student associated with the passed in $asset and
     * returns false if they don't.
     *
     * Then checks to see if the logged in user has authority to execute the actions passed in via $permissions
     * against/for the student associated with the passed in $asset.  Returns false if the faculty member does not.
     *
     * Then checks to see the logged in user has public access to the passed in asset and returns true
     * if they do.
     *
     * Then checks to see the logged in user has private access to the passed in asset and returns true
     * if they do.
     *
     * Then checks to see the logged in user has team access to the passed in asset and returns true
     * if they do.
     *
     * If NONE of the above has caused the function to return, it then returns false.
     *
     *
     * @param array $permissions - Indicates what action(s) the logged in user is trying to execute against/for
     *                             the student associated with the passed in $asset.  Each string in this array should be in the format
     *                             of "<asset>-<accessLevel>-<action>" (e.g., "notes-public-view", "referral-private-create", etc.)
     * @param BaseEntity $asset
     * @param int $userId - optional. If not provided then retrieved from the user currently logged in.
     *
     * @return bool - see explanation above for when true and false are returned.
     */
    public function hasAssetAccess(array $permissions, BaseEntity $asset, $userId = null, $appointmentAccess = FALSE)
    {

        if (! $this->authEnabled) {
            return true;
        }

        if (! $userId) {
            $userId = $this->grabCurrentUserId();
        }

        $this->checkAccessToOrganizationUsingAsset($asset);

        // 1. See if they are the organization's coordinator.
        /*
         * if ($this->hasCoordinatorAccess()) { return true; }
         */

        // Check if assest created by logged in person, interested parties and central coordinator
        if($this->checkIfOwnableAsses($asset, $userId)) {
             return true;
        }
        // 2. See if faculty has acces to the student and access to execute the desired actions passed in via $permissions.
        // Do not check student access if appointment
        if (! $appointmentAccess && ! $this->hasStudentAccess($permissions, $asset)) {
            return false;
        }

        // 3. See if they have role-based access.
        if ($this->hasPublicAccess($asset, $userId)) {
            return true;
        }
        // 4. See if they have private access.
        if ($this->hasPrivateAccess($asset, $userId)) {
            return true;
        }
        // 5. See if they have access via teams, as well.
        if ($this->hasTeamAccess($asset, $userId)) {
            return true;
        }
        return false;
    }


    /**
     *
     * @param string $userId
     * @return boolean
     */
    public function checkIfCoordinator($userId = null)
    {
        return $this->hasCoordinatorAccess();
    }

    protected static function getCacheKey($userId)
    {
        $cacheKey = self::SESSION_KEY . ":$userId";
        return $cacheKey;
    }

    public function setSecurity($obj)
    {
        $this->securityContext = $obj;
    }

    /**
     * Check Access of particular student for a staff in current academic year
     *
     * @param int $studentId
     * @param int|null $userId
     * @param SynapseException|null $exception
     * @return bool
     */
    public function checkAccessToStudent($studentId, $userId = null , $exception = null)
    {
        if (! $this->authEnabled) {
            return true;
        }
        $staffId = ($userId) ? $userId : $this->grabCurrentUserId();
        $accessToStudent = $this->orgPermissionsetRepository->checkAccessToStudent($staffId, $studentId);

        $this->userManagementService = $this->container->get(UserManagementService::SERVICE_KEY);
        $currentAcademicYearStudent = $this->userManagementService->isStudentMemberOfCurrentAcademicYear($studentId);

        $finalAccessToStudent = ($accessToStudent && $currentAcademicYearStudent);
        if($exception && !$finalAccessToStudent){
            throw $exception;
        }else{
            return $finalAccessToStudent;
        }
    }
    
    public function delegateAcccessForStaff($staffId = null)
    {

        if (! $this->authEnabled) {
            return true;
        }

        $loggedUserId = $this->grabCurrentUserId();
    	$delegateAcccessRepo = $this->em->getRepository('SynapseCoreBundle:CalendarSharing');
    	$delegateAcccessList = $delegateAcccessRepo->listManagedUser($loggedUserId);
    	if($delegateAcccessList){
        	$delegateUsers = array_column($delegateAcccessList, 'managed_person_id');
        	if (! in_array($staffId, $delegateUsers)) {
        		return false;
        	}else{
        		return true;
        	}
    	}else{
    	    return false;
    	}
    }

    public function getAccessTree() {
        $accessTree = $this->rbac->getAccessTree();
        return $accessTree;
    }


    /**
     * If any of the students is a non participant for current academic year, throws an access denied exception
     *
     * @param array $studentIds
     * @param int|null $loggedInUserId
     * returns void
     * @throws AccessDeniedException
     */
    public function assertPermissionToEngageWithStudents($studentIds, $loggedInUserId = null)
    {
        $this->academicYearService = $this->container->get('academicyear_service'); // was not declared in constructor for circular reference
        $this->personService = $this->container->get('person_service');

        $loggedInPerson = !empty($loggedInUserId) ? $this->personService->findPerson($loggedInUserId) : $this->grabCurrentUser();
        $organizationId = $loggedInPerson->getOrganization()->getId();
        $currentAcademicYear = $this->academicYearService->findCurrentAcademicYearForOrganization($organizationId);
        if (isset($currentAcademicYear['org_academic_year_id'])) {
            $orgAcademicYearId = $currentAcademicYear['org_academic_year_id'];
        } else {
            throw new AccessDeniedException("Academic year is not active");
        }

        $participantArr = $this->orgPersonStudentYearRepository->getParticipantStudentsFromStudentList($studentIds, $organizationId, $orgAcademicYearId);

        switch (count($studentIds)) {
            case 1 :
                $errorMsg = "This student is a non participant for the current academic year";
                break;
            case 2 :
                $errorMsg = "Both the students are non participants for the current academic year";
                break;
            default :
                $errorMsg = "All of the students are non participants for the current academic year";
        }

        if (count($participantArr) == 0) {
            throw new AccessDeniedException($errorMsg);
        }

        // List of students that are non participant
        $nonParticipantStudents = array_diff($studentIds, $participantArr);
        $noOfNonParticipants = count($nonParticipantStudents);

        if ($noOfNonParticipants > 0) {
            throw new AccessDeniedException("$noOfNonParticipants out of " . count($studentIds) . " students are non participants for the current academic year, hence cannot perform this activity");
        }
    }


    /**
     * For Security: Verifying passed personId through API to loggedInPersonId
     * If its not matched, throw an access denied exception
     *
     * @param int $personId
     * @return bool
     * @throws AccessDeniedException
     */
    public function validateUserAsAuthorizedAppointmentUser($personId)
    {
        $personId = (int)$personId;
        $loggedUserId = $this->grabCurrentUserId();

        // Check that person has delegate access
        $delegateAccess = $this->delegateAcccessForStaff($personId);

        if (($loggedUserId != $personId) && !$delegateAccess) {
            throw new AccessDeniedException('Access denied, user/delegate validation failed');
        }
        return true;
    }
}
