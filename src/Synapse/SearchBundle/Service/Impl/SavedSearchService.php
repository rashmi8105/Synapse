<?php
namespace Synapse\SearchBundle\Service\Impl;

use GuzzleHttp\json_decode;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Validator;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Utility\SearchUtilityService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Util\Constants\SavedSearchConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SearchBundle\Entity\OrgSearch;
use Synapse\SearchBundle\EntityDto\SavedSearchesDto;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SearchBundle\Service\SavedSearchServiceInterface;

/**
 * @DI\Service("savedsearch_service")
 */
class SavedSearchService extends AbstractService implements SavedSearchServiceInterface
{

    const SERVICE_KEY = 'savedsearch_service';

    /**
     *
     * @var org_serivce
     */
    private $orgService;

    /**
     *
     * @var Container
     */
    private $container;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var SearchUtilityService
     */
    private $searchUtilityService;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     *
     * @var OrgSearchRepository
     */
    private $orgSearchServiceRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->validator = $this->container->get(SynapseConstant::VALIDATOR);

        $this->searchUtilityService = $this->container->get(SearchUtilityService::SERVICE_KEY);

        // Repositories
        $this->organizationRepository = $this->repositoryResolver->getRepository(OrganizationRepository::REPOSITORY_KEY);
        $this->orgSearchServiceRepository = $this->repositoryResolver->getRepository(OrgSearchRepository::REPOSITORY_KEY);
        $this->personRepository = $this->repositoryResolver->getRepository(PersonRepository::REPOSITORY_KEY);
    }

    /**
     * Create saved searches
     *
     * @param SaveSearchDto $saveSearchDto
     * @param int $loggedUserId
     * @throws SynapseValidationException
     * @return SaveSearchDto
     */
    public function createSavedSearches(SaveSearchDto $saveSearchDto, $loggedUserId)
    {
        $person = $this->personRepository->find($loggedUserId, new SynapseValidationException('Person Not Found.'));
        $organization = $this->organizationRepository->find($saveSearchDto->getOrganizationId(), new SynapseValidationException('Organization Not Found.'));

        if (trim($saveSearchDto->getSavedSearchName()) == "") {
            throw new ValidationException('Name field cannot be empty');
        }

        if (strlen($saveSearchDto->getSavedSearchName()) > 120) {
            throw new ValidationException('Name cannot be more than 120 character limit');
        }

        $orgSearch = new OrgSearch();
        $searchAttributes = $saveSearchDto->getSearchAttributes();
        $searchAttributesJson = json_encode($searchAttributes);

        if (isset($searchAttributes['risk_indicator_ids']) && !empty($searchAttributes['risk_indicator_ids'])) {
            $sqlRisk = $this->searchUtilityService->makeSqlQuery($searchAttributes['risk_indicator_ids'], ' and p.risk_level');
        }

        if (isset($searchAttributes['intent_to_leave_ids']) && !empty($searchAttributes['intent_to_leave_ids'])) {
            $sqlIntent = $this->searchUtilityService->makeSqlQuery($searchAttributes['intent_to_leave_ids'], 'intent_to_leave_ids');
        }

        $sqlGroups = $this->searchUtilityService->makeSqlQuery($searchAttributes['group_ids'], ' and ogs.org_group_id');
        $refValue = Helper::filterMap($searchAttributes['referral_status'], 'referralstatus');

        $contactValue = Helper::filterMap($searchAttributes['contact_types'], 'contacttypes');
        if ($contactValue) {
            $sqlContactTypes = $this->searchUtilityService->makeSqlQuery($contactValue, ' and (ct.parent_contact_types_id');
            $sqlReferral = $this->searchUtilityService->makeSqlQuery($refValue, ' or r.status') . ')';
        } else {
            $sqlContactTypes = $this->searchUtilityService->makeSqlQuery($contactValue, ' and ct.parent_contact_types_id');
            $sqlReferral = $this->searchUtilityService->makeSqlQuery($refValue, ' and r.status');
        }

        $baseQuery = Helper::BASEQUERY_CONST1 . $loggedUserId . Helper::BASEQUERY_CONST2 . $sqlGroups . Helper::BASEQUERY_CONST3;
        if (isset($sqlRisk)) {
            $baseQuery .= $sqlRisk;
        }

        if (isset($sqlIntent)) {
            $baseQuery .= $sqlIntent;
        }
        $closeFlag = 0;

        // Searching for Courses
        if (isset($searchAttributes['courses']) && !empty($searchAttributes['courses'])) {
            $courses = $this->coursesSearch($searchAttributes['courses']);
            if ($courses) {
                $baseQuery .= $courses;
            }
        }

        // Searching for ISP
        $ebiIspflag = 0;
        if (isset($searchAttributes['isps']) && !empty($searchAttributes['isps'])) {
            $ebiIspflag = 1;
            $isps = $this->ispSearch($searchAttributes['isps']);
            if ($isps) {
                $baseQuery .= 'AND ' . $isps;
                $closeFlag = 1;
            }
        }

        // Searching for EBI
        if (isset($searchAttributes['datablocks']) && !empty($searchAttributes['datablocks'])) {
            if (!empty($searchAttributes['datablocks'][0]['profile_block_id'])) {
                $ebiSearchQuery = $this->ebiSearch($searchAttributes['datablocks']);
                if ($ebiSearchQuery && $ebiIspflag) {
                    $baseQuery .= 'OR ' . $ebiSearchQuery;
                    $closeFlag++;
                } elseif ($ebiSearchQuery) {
                    $ebiIspflag = 1;
                    $baseQuery .= 'AND ' . $ebiSearchQuery;
                    $closeFlag++;
                }
            }
        }

        $baseQuery = $this->searchValidator($baseQuery);
        if ($closeFlag == 1) {
            $baseQuery .= " )";
        } elseif ($closeFlag == 2) {
            $baseQuery .= " ))";
        }

        $baseQuery .= $sqlContactTypes . $sqlReferral . ' and gf.person_id=' . $loggedUserId . ' group by (p.id)';

        $baseQuery = $this->getBaseQuery($searchAttributes, $loggedUserId);

        $orgSearch->setOrganization($organization);
        $orgSearch->setName($saveSearchDto->getSavedSearchName());
        $orgSearch->setPerson($person);
        $orgSearch->setQuery(substr($baseQuery, 0, 4999));
        $orgSearch->setJson($searchAttributesJson);
        $orgSearch->setEditedByMe(true);

        // Determine if this saved search has the same name as another saved search created by this person.
        $existingOrgSearch = $this->orgSearchServiceRepository->findOneBy([
            'organization' => $organization,
            'person' => $person,
            'name' => $saveSearchDto->getSavedSearchName(),
            'editedByMe' => true
        ]);

        // If the doctrine validator finds a duplicate based on Organization, Person, and Name - throw an exception
        // with the exception code org_search_duplicate_error, and the message as defined in the OrgSearch entity
        if (isset($existingOrgSearch)) {
            $errors = $this->validator->validate($orgSearch);
            $this->catchError($errors, 'org_search_duplicate_error');
        }

        $this->orgSearchServiceRepository->createOrgSearch($orgSearch);
        $this->orgSearchServiceRepository->flush();
        $saveSearchDto->setSavedSearchId($orgSearch->getId());
        $saveSearchDto->setPersonId($loggedUserId);
        return $saveSearchDto;
    }

    /**
     * Edit Savedsearches
     *
     * @param SaveSearchDto $saveSearchDto
     * @throws SynapseValidationException
     * @return SaveSearchDto
     */
    public function editSavedSearches(SaveSearchDto $saveSearchDto)
    {
        $orgSearch = $this->orgSearchServiceRepository->findOneBy(array(
            'id' => $saveSearchDto->getSavedSearchId(),
            'organization' => $saveSearchDto->getOrganizationId()
        ));

        $this->isObjectExist($orgSearch, 'Saved Query Not Found.', 'savedquery_not_found');

        if (trim($saveSearchDto->getSavedSearchName()) == "") {
            throw new ValidationException('Name field cannot be empty');
        }

        if (strlen($saveSearchDto->getSavedSearchName()) > 120) {
            throw new ValidationException('Name cannot be more than 120 character limit');
        }

        $person = $this->personRepository->find($saveSearchDto->getPersonId(), new SynapseValidationException('Person Not Found.'));
        $organization = $this->organizationRepository->find($saveSearchDto->getOrganizationId(), new SynapseValidationException('Organization Not Found.'));

        $searchAttributes = $saveSearchDto->getSearchAttributes();
        $searchAttributesJson = json_encode($searchAttributes);

        if (isset($searchAttributes['risk_indicator_ids']) && !empty($searchAttributes['risk_indicator_ids'])) {
            $sqlRisk = $this->searchUtilityService->makeSqlQuery($searchAttributes['risk_indicator_ids'], ' and p.risk_level');
        }

        if (isset($searchAttributes['intent_to_leave_ids']) && !empty($searchAttributes['intent_to_leave_ids'])) {
            $sqlIntent = $this->searchUtilityService->makeSqlQuery($searchAttributes['intent_to_leave_ids'], ' and p.intent_to_leave');
        }

        $sqlGroups = $this->searchUtilityService->makeSqlQuery($searchAttributes['group_ids'], ' and ogs.org_group_id');
        $refValue = Helper::filterMap($searchAttributes['referral_status'], 'referralstatus');

        $contactValue = Helper::filterMap($searchAttributes['contact_types'], 'contacttypes');

        if ($contactValue) {
            $sqlContactTypes = $this->searchUtilityService->makeSqlQuery($contactValue, ' and (ct.parent_contact_types_id');
            $sqlReferral = $this->searchUtilityService->makeSqlQuery($refValue, ' or r.status') . ')';
        } else {
            $sqlContactTypes = $this->searchUtilityService->makeSqlQuery($contactValue, ' and ct.parent_contact_types_id');
            $sqlReferral = $this->searchUtilityService->makeSqlQuery($refValue, ' and r.status');
        }

        $baseQuery = Helper::BASEQUERY_CONST1 . $saveSearchDto->getPersonId() . Helper::BASEQUERY_CONST2 . $sqlGroups . Helper::BASEQUERY_CONST3;

        if (isset($sqlRisk)) {
            $baseQuery .= $sqlRisk;
        }

        if (isset($sqlIntent)) {
            $baseQuery .= $sqlIntent;
        }

        $closeFlag = 0;

        // Searching for Courses
        if (isset($searchAttributes['courses']) && !empty($searchAttributes['courses'])) {
            $courses = $this->coursesSearch($searchAttributes['courses']);
            if ($courses) {
                $baseQuery .= $courses;
            }
        }

        // Searching for ISP
        $ebiIspflag = 0;

        if (isset($searchAttributes['isps']) && !empty($searchAttributes['isps'])) {
            $ebiIspflag = 1;
            $isps = $this->ispSearch($searchAttributes['isps']);
            if ($isps) {
                $baseQuery .= 'AND ' . $isps;
                $closeFlag = 1;
            }
        }

        // Searching for EBI
        if (isset($searchAttributes['datablocks']) && !empty($searchAttributes['datablocks'])) {
            if (!empty($searchAttributes['datablocks'][0]['profile_block_id'])) {
                $ebis = $this->ebiSearch($searchAttributes['datablocks']);

                if ($ebis && $ebiIspflag) {
                    $baseQuery .= 'OR ' . $ebis;
                    $closeFlag++;
                } elseif ($ebis) {
                    $ebiIspflag = 1;
                    $baseQuery .= 'AND ' . $ebis;
                    $closeFlag++;
                }
            }
        }

        $baseQuery = $this->searchValidator($baseQuery);

        if ($closeFlag == 1) {
            $baseQuery .= " )";
        } elseif ($closeFlag == 2) {
            $baseQuery .= " ))";
        }

        $baseQuery .= $sqlContactTypes . $sqlReferral . ' and gf.person_id=' . $saveSearchDto->getPersonId() . ' group by (p.id)';

        $baseQuery = $this->getBaseQuery($searchAttributes, $saveSearchDto->getPersonId());

        $orgSearch->setOrganization($organization);
        $orgSearch->setName($saveSearchDto->getSavedSearchName());
        $orgSearch->setPerson($person);
        $orgSearch->setQuery(substr($baseQuery, 0, 4999));
        $orgSearch->setJson($searchAttributesJson);

        if ($orgSearch->getEditedByMe()) {
            $errors = $this->validator->validate($orgSearch);
            $this->catchError($errors, 'org_search_duplicate_error');
        } else {
            $orgSearch->setEditedByMe(true);
        }

        $orgSearch->setFromSharedtab(null);
        $this->orgSearchServiceRepository->createOrgSearch($orgSearch);
        $this->orgSearchServiceRepository->flush();

        return $saveSearchDto;
    }

    public function cancelSavedsearch($searchId, $loggedUserId)
    {
        $this->logger->debug("Cancel Custom Saved Search for Search Id" . $searchId );
        $this->orgSearchServiceRepository = $this->repositoryResolver->getRepository(SavedSearchConstant::ORGSEARCH_REPO);
        
        $person = $this->container->get(SavedSearchConstant::PERSON_SERVICE)->findPerson($loggedUserId);
        $organization = $person->getOrganization();
        $orgSearch = $this->orgSearchServiceRepository->findOneBy(array(
            'id' => $searchId,
            SavedSearchConstant::ORGN => $organization->getId()
        ));
        $this->isObjectExist($orgSearch, SavedSearchConstant::SAVEDQUERY_ERROR, SavedSearchConstant::SAVEDQUERY_ERKEY);
        $this->orgSearchServiceRepository->deleteSaveSearch($orgSearch);
        $this->orgSearchServiceRepository->flush();
        $this->logger->info("Saved Search - Cancel Custom Saved Search for a Search Id ");
        return $searchId;
    }

    public function getSavedSearch($searchId, $orgId, $userId = null)
    {
        $this->logger->debug(" Get Saved Search for Search Id " . $searchId . "Organization Id" . $orgId);
        $this->orgService = $this->container->get(SavedSearchConstant::ORGANIZATION_SERVICE);
        $this->orgSearchServiceRepository = $this->repositoryResolver->getRepository(SavedSearchConstant::ORGSEARCH_REPO);
        $orgSearch = $this->orgSearchServiceRepository->findOneBy(array(
            'id' => $searchId,
            SavedSearchConstant::ORGN => $orgId,
            'person' => $userId
        ));
        $this->isObjectExist($orgSearch, SavedSearchConstant::SAVEDQUERY_ERROR, SavedSearchConstant::SAVEDQUERY_ERKEY);
        $saveSearchDto = new SaveSearchDto();
        $saveSearchDto->setOrganizationId($orgSearch->getOrganization()
            ->getId());
        $saveSearchDto->setSavedSearchId($orgSearch->getId());
        $saveSearchDto->setSavedSearchName($orgSearch->getName());
        $dateCreated = $orgSearch->getCreatedAt();
        $dateCreated = new \DateTime($dateCreated->format('Y-m-d H:i:s'));
        $organization = $this->orgService->find($orgSearch->getOrganization()
            ->getId());
        $myTimezone = $organization->getTimezone();
        $timezone = $this->repositoryResolver->getRepository(SavedSearchConstant::METADATA_REPO)->findByListName($myTimezone);
        if ($timezone) {
            $mTimezone = $timezone[0]->getListValue();
            Helper::setOrganizationDate($dateCreated, $mTimezone);
        }
        $saveSearchDto->setDateCreated($dateCreated);
        $saveSearchDto->setPersonId($orgSearch->getPerson()
            ->getId());
        
        $saveSearchDto->setSearchAttributes(json_decode($orgSearch->getJson(), true));
        $this->logger->info("Saved Search - Get Saved Search for Search Id  ");
        return $saveSearchDto;
    }

    public function listSavedSearch($loggedUser, $orgId, $myTimezone)
    {
        $this->logger->debug(" List Saved Search for Logged User of Organization Id " . $orgId );
        $this->orgService = $this->container->get(SavedSearchConstant::ORGANIZATION_SERVICE);
        $this->orgSearchServiceRepository = $this->repositoryResolver->getRepository(SavedSearchConstant::ORGSEARCH_REPO);
        $this->orgSearchSharedRepository = $this->repositoryResolver->getRepository(SavedSearchConstant::ORG_SEARCH_SHARED_REPO);
        
        $orgSearch = $this->orgSearchServiceRepository->findBy(array(
            'person' => $loggedUser,
            SavedSearchConstant::ORGN => $orgId,
            'editedByMe' => 1,
            'fromSharedtab' => null
        ), array(
            'createdAt' => 'desc'
        ));
        
        $orgSharedSearch = $this->orgSearchSharedRepository->findBy(array(
            'personIdSharedwith' => $loggedUser
        ));
        
        $sharedArray = [];
        if (! empty($orgSharedSearch)) {
            foreach ($orgSharedSearch as $oShared) {
                $sharedArray[] = $oShared->getOrgSearchIdDest()->getId();
            }
        }
        $timezone = $this->repositoryResolver->getRepository(SavedSearchConstant::METADATA_REPO)->findByListName($myTimezone);
        if ($timezone) {
            $mTimezone = $timezone[0]->getListValue();
        }
        $SaveSearchDto = new SaveSearchDto();
        if (! empty($orgSearch)) {
            $searches = [];
            foreach ($orgSearch as $oSearch) {
                if (! in_array($oSearch->getId(), $sharedArray)) {
                    $savedSearchesDto = new SavedSearchesDto();
                    $savedSearchesDto->setSavedSearchId($oSearch->getId());
                    $savedSearchesDto->setSearchName($oSearch->getName());
                    $dateCreated = $oSearch->getCreatedAt();
                    $dateCreated = new \DateTime($dateCreated->format('Y-m-d H:i:s'));
                    Helper::setOrganizationDate($dateCreated, $mTimezone);
                    $savedSearchesDto->setDateCreated($dateCreated);
                    $searches[] = $savedSearchesDto;
                }
            }
            if (count($searches) > 0) {
                $SaveSearchDto->setSavedSearches($searches);
            }
        }
        $this->logger->info("Saved Search - List Custom Saved Search for logged User ");
        return $SaveSearchDto;
    }

    private function isObjectExist($object, $message, $key)
    {
        if (! isset($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    private function catchError($errors, $key)
    {
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {
                
                $errorsString = $error->getMessage();
            }
            throw new ValidationException([
                $errorsString
            ], $errorsString, $key);
        }
    }

    private function ispSearch($isps)
    {
        $categoryArr = array();
        $dateArr = array();
        $numberArr = array();
        $ispArr = array();
        
        foreach ($isps as $isp) {
            if (isset($isp[SavedSearchConstant::CAT_TYPE]) && ! empty(($isp[SavedSearchConstant::CAT_TYPE]))) {
                if ($isp[SavedSearchConstant::META_DATA_TYPE] == 'S') {
                    $categoryArr[] = $isp;
                }
            }
            if ($isp[SavedSearchConstant::META_DATA_TYPE] == 'D') {
                $dateArr[] = $isp;
            }
            if ($isp[SavedSearchConstant::META_DATA_TYPE] == 'N') {
                $numberArr[] = $isp;
            }
        }
        $ispArr = array_merge($categoryArr, $dateArr, $numberArr);
        
        $base_query = "";
        foreach ($ispArr as $isp) {
            if (isset($isp[SavedSearchConstant::CAT_TYPE]) && ! empty(($isp[SavedSearchConstant::CAT_TYPE]))) {
                if ($isp[SavedSearchConstant::META_DATA_TYPE] == 'S') {
                    $finalValue = $this->getFinalValue($isp[SavedSearchConstant::CAT_TYPE]);
                    $base_query = $this->getMdatValQuery($finalValue, $base_query, Helper::BASEQUERY_CONST7);
                    $base_query .= "AND org_metadata_id=" . $isp['id'] . ")";
                }
            }
            
            if ($isp[SavedSearchConstant::META_DATA_TYPE] == 'N') {
                $base_query = $this->getNumberQuery($isp, $base_query, Helper::BASEQUERY_CONST7, SavedSearchConstant::WHERE_ORG_MDATAID);
            }
            
            if ($isp[SavedSearchConstant::META_DATA_TYPE] == 'D') {
                $base_query = $this->getDateQuery($isp, $base_query, Helper::BASEQUERY_CONST7, SavedSearchConstant::WHERE_ORG_MDATAID);
            }
        }
        return $base_query;
    }

    private function ebiSearch($ebi)
    {
        $categoryArr = array();
        $dateArr = array();
        $numberArr = array();
        $ebiArr = array();
        
        foreach ($ebi as $datablocks) {
            if (isset($datablocks[SavedSearchConstant::PROFILE_ITEMS]) && ! empty(($datablocks[SavedSearchConstant::PROFILE_ITEMS]))) {
                foreach ($datablocks[SavedSearchConstant::PROFILE_ITEMS] as $profileItems) {
                    if ($profileItems[SavedSearchConstant::META_DATA_TYPE] == 'S') {
                        $categoryArr[] = $profileItems;
                    } elseif ($profileItems[SavedSearchConstant::META_DATA_TYPE] == 'D') {
                        $dateArr[] = $profileItems;
                    } elseif ($profileItems[SavedSearchConstant::META_DATA_TYPE] == 'N') {
                        $numberArr[] = $profileItems;
                    }
                }
            }
        }
        
        $ebiArr = array_merge($categoryArr, $dateArr, $numberArr);
        
        $base_query = "";
        foreach ($ebiArr as $ebis) {
            if (isset($ebis[SavedSearchConstant::CAT_TYPE]) && ! empty(($ebis[SavedSearchConstant::CAT_TYPE]))) {
                if ($ebis[SavedSearchConstant::META_DATA_TYPE] == 'S') {
                    $finalValue = $this->getFinalValue($ebis[SavedSearchConstant::CAT_TYPE]);
                    $base_query = $this->getMdatValQuery($finalValue, $base_query, Helper::BASEQUERY_CONST6);
                    $base_query .= "AND ebi_metadata_id=" . $ebis['id'] . ")";
                }
            }
            
            if ($ebis[SavedSearchConstant::META_DATA_TYPE] == 'N') {
                $base_query = $this->getNumberQuery($ebis, $base_query, Helper::BASEQUERY_CONST6, SavedSearchConstant::WHERE_EBI_MDATAID);
            }
            
            if ($ebis[SavedSearchConstant::META_DATA_TYPE] == 'D') {
                $base_query = $this->getDateQuery($ebis, $base_query, Helper::BASEQUERY_CONST6, SavedSearchConstant::WHERE_EBI_MDATAID);
            }
        }
        return $base_query;
    }

    /**
     * Add course to search query
     *
     * @param array $courses
     * @return string
     */
    public function coursesSearch($courses)
    {
        $sqlCourses = '';
        if (!empty($courses['department_id'])) {
            $sqlCourses .= $this->searchUtilityService->makeSqlQuery($courses['department_id'], 'dept_code');
        }

        if (!empty($courses['subject_id'])) {
            $sqlCourses .= $this->searchUtilityService->makeSqlQuery($courses['subject_id'], 'OR subject_code');
        }

        if (!empty($courses['course_ids'])) {
            $sqlCourses .= $this->searchUtilityService->makeSqlQuery($courses['course_ids'], 'OR course_number');
        }

        if (!empty($courses['section_ids'])) {
            $sqlCourses .= $this->searchUtilityService->makeSqlQuery($courses['section_ids'], 'OR section_number');
        }

        if ($sqlCourses) {
            $sqlCourses = Helper::BASEQUERY_CONST4 . 'AND ' . "(" . $sqlCourses . ')))';
        }

        return $sqlCourses;
    }

    private function searchValidator($qry)
    {
        $qry = str_replace("AND AND", "AND", $qry);
        $qry = str_replace("AND OR", SavedSearchConstant::FIELD_AND . "(", $qry);
        $qry = str_replace("OR AND", "OR", $qry);
        $qry = str_replace("OR OR", "OR (", $qry);
        return $qry;
    }

    private function getFinalValue($dataArr)
    {
        $ansArr = array();
        $valArr = array();
        foreach ($dataArr as $qAns) {
            $ans = $qAns['answer'];
            $value = $qAns['value'];
            if (trim($ans) != "") {
                $ansArr[] = $ans;
            }
            if (trim($value) != "") {
                $valArr[] = $value;
            }
        }
        $ansText = "'" . implode("','", $ansArr) . "'";
        $valText = "'" . implode("','", $valArr) . "'";
        $finalValue = $ansText . ',' . $valText;
        return $finalValue;
    }

    private function getDateQuery($dataArr, $base_query, $baseConst, $whereFld)
    {
        if (isset($dataArr[SavedSearchConstant::FIELD_START_DATE]) && isset($dataArr[SavedSearchConstant::FIELD_END_DATE]) && ! empty($dataArr[SavedSearchConstant::FIELD_START_DATE]) && ! empty($dataArr[SavedSearchConstant::FIELD_END_DATE])) {
            $startDate = Helper::getUtcDate(new \DateTime($dataArr[SavedSearchConstant::FIELD_START_DATE]));
            $endDate = Helper::getUtcDate(new \DateTime($dataArr[SavedSearchConstant::FIELD_END_DATE]));
            if ($base_query) {
                $base_query .= SavedSearchConstant::FIELD_OR . $baseConst . $whereFld . $dataArr['id'] . SavedSearchConstant::META_DATA_VALUE . "'" . $startDate->format(SavedSearchConstant::DATE_YMD) . "'" . SavedSearchConstant::FIELD_AND . "'" . $endDate->format(SavedSearchConstant::DATE_YMD) . "'" . ')';
            } else {
                $base_query .= SavedSearchConstant::FIELD_AND . "(" . $baseConst . $whereFld . $dataArr['id'] . SavedSearchConstant::META_DATA_VALUE . "'" . $startDate->format(SavedSearchConstant::DATE_YMD) . "'" . SavedSearchConstant::FIELD_AND . "'" . $endDate->format(SavedSearchConstant::DATE_YMD) . "'" . ')';
            }
        }
        return $base_query;
    }

    private function getNumberQuery($dataArr, $base_query, $baseConst, $whereFld)
    {
        if (isset($ebis['is_single']) && ! empty($ebis[SavedSearchConstant::SING_VAL])) {
            if ($base_query) {
                $base_query .= SavedSearchConstant::FIELD_OR . $baseConst . $whereFld . $ebis['id'] . SavedSearchConstant::META_DATA_VALUE_EQUALS . "'" . $ebis[SavedSearchConstant::SING_VAL] . "'" . ')';
            } else {
                $base_query .= SavedSearchConstant::FIELD_AND . "(" . $baseConst . $whereFld . $ebis['id'] . SavedSearchConstant::META_DATA_VALUE_EQUALS . "'" . $ebis[SavedSearchConstant::SING_VAL] . "'" . ')';
            }
        } else {
            if (! empty($ebis[SavedSearchConstant::MIN_DIGITS]) && ! empty($ebis[SavedSearchConstant::MAX_DIGITS])) {
                if ($base_query) {
                    $base_query .= SavedSearchConstant::FIELD_OR . $baseConst . $whereFld . $ebis['id'] . SavedSearchConstant::META_DATA_VALUE . "'" . $ebis[SavedSearchConstant::MIN_DIGITS] . "'" . SavedSearchConstant::FIELD_AND . "'" . $ebis[SavedSearchConstant::MAX_DIGITS] . "'" . ')';
                } else {
                    $base_query .= SavedSearchConstant::FIELD_AND . "(" . $baseConst . $whereFld . $ebis['id'] . SavedSearchConstant::META_DATA_VALUE . "'" . $ebis[SavedSearchConstant::MIN_DIGITS] . "'" . SavedSearchConstant::FIELD_AND . "'" . $ebis[SavedSearchConstant::MAX_DIGITS] . "'" . ')';
                }
            }
        }
        return $base_query;
    }

    private function getMdatValQuery($finalValue, $base_query, $baseConst)
    {
        if ($base_query) {
            $base_query .= SavedSearchConstant::FIELD_OR . $baseConst . ' where metadata_value in(' . $finalValue . ')';
        } else {
            $base_query .= SavedSearchConstant::FIELD_AND . "(" . $baseConst . ' where metadata_value in(' . $finalValue . ')';
        }
    }

    /**
     * Get basequery
     *
     * @param array $searchAttributes
     * @param integer $loggedUserId
     * @return string
     */
    public function getBaseQuery($searchAttributes, $loggedUserId)
    {
        if (isset($searchAttributes['risk_indicator_ids']) && !empty($searchAttributes['risk_indicator_ids'])) {
            $sqlRisk = $this->searchUtilityService->makeSqlQuery($searchAttributes['risk_indicator_ids'], ' and p.risk_level');
        }

        if (isset($searchAttributes['intent_to_leave_ids']) && !empty($searchAttributes['intent_to_leave_ids'])) {
            $sqlIntent = $this->searchUtilityService->makeSqlQuery($searchAttributes['intent_to_leave_ids'], ' and p.intent_to_leave');
        }

        $refValue = '';
        if (array_key_exists('referral_status', $searchAttributes) && !empty($searchAttributes['referral_status'])) {
            $refValue = Helper::filterMap($searchAttributes['referral_status'], 'referralstatus');
        }

        $sqlGroups = $this->searchUtilityService->makeSqlQuery($searchAttributes['group_ids'], ' and ogs.org_group_id');

        $contactValue = '';
        if (array_key_exists('referral_status', $searchAttributes) && !empty($searchAttributes['referral_status'])) {
            $contactValue = Helper::filterMap($searchAttributes['contact_types'], 'contacttypes');
        }

        if ($contactValue) {
            $sqlContactTypes = $this->searchUtilityService->makeSqlQuery($contactValue, ' and (ct.parent_contact_types_id');
            $sqlReferral = $this->searchUtilityService->makeSqlQuery($refValue, ' or r.status') . ')';
        } else {
            $sqlContactTypes = $this->searchUtilityService->makeSqlQuery($contactValue, ' and ct.parent_contact_types_id');
            $sqlReferral = $this->searchUtilityService->makeSqlQuery($refValue, ' and r.status');
        }

        $base_query = Helper::BASEQUERY_CONST1 . $loggedUserId . Helper::BASEQUERY_CONST2 . $sqlGroups . Helper::BASEQUERY_CONST3;
        if (isset($sqlRisk)) {
            $base_query .= $sqlRisk;
        }
        if (isset($sqlIntent)) {
            $base_query .= $sqlIntent;
        }
        $closeFlag = 0;

        // Searching for Courses
        if (isset($searchAttributes['courses']) && !empty($searchAttributes['courses'])) {
            $courses = $this->coursesSearch($searchAttributes['courses']);
            if ($courses) {
                $base_query .= $courses;
            }
        }

        // Searching for ISP
        $ebiIspflag = 0;
        if (isset($searchAttributes['isps']) && !empty($searchAttributes['isps'])) {
            $ebiIspflag = 1;
            $isps = $this->ispSearch($searchAttributes['isps']);
            if ($isps) {
                $base_query .= 'AND ' . $isps;
                $closeFlag = 1;
            }
        }

        // Searching for EBI
        if (isset($searchAttributes['datablocks']) && !empty($searchAttributes['datablocks'])) {
            if (!empty($searchAttributes['datablocks'][0]['profile_block_id'])) {
                $ebis = $this->ebiSearch($searchAttributes['datablocks']);
                if ($ebis) {
                    if ($ebiIspflag) {
                        $base_query .= 'OR ' . $ebis;
                    } else {
                        $ebiIspflag = 1;
                        $base_query .= 'AND ' . $ebis;
                    }
                    $closeFlag++;
                }
            }
        }
        $base_query = $this->searchValidator($base_query);
        if ($closeFlag == 1) {
            $base_query .= " )";
        } elseif ($closeFlag == 2) {
            $base_query .= " ))";
        }

        $base_query .= $sqlContactTypes . $sqlReferral . ' and gf.person_id=' . $loggedUserId . ' group by (p.id)';

        return $base_query;
    }
}
