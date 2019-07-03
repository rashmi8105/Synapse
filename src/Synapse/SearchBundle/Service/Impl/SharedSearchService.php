<?php
namespace Synapse\SearchBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\MetadataListValuesRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\AlertNotificationsService;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\Util\Constants\SharedSearchConstant;
use Synapse\CoreBundle\Util\Helper;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\SearchBundle\Entity\OrgSearch;
use Synapse\SearchBundle\Entity\OrgSearchSharedBy;
use Synapse\SearchBundle\Entity\OrgSearchSharedWith;
use Synapse\SearchBundle\EntityDto\SaveSearchDto;
use Synapse\SearchBundle\EntityDto\SharedByUsersArrayDto;
use Synapse\SearchBundle\EntityDto\SharedSearchDto;
use Synapse\SearchBundle\EntityDto\SharedSearchListResponseDto;
use Synapse\SearchBundle\EntityDto\SharedWithUsersArrayDto;
use Synapse\SearchBundle\Repository\OrgSearchRepository;
use Synapse\SearchBundle\Repository\OrgSearchSharedByRepository;
use Synapse\SearchBundle\Repository\OrgSearchSharedWithRepository;
use Synapse\SearchBundle\Service\SharedSearchServiceInterface;

/**
 * @DI\Service("sharedsearch_service")
 */
class SharedSearchService extends AbstractService implements SharedSearchServiceInterface
{

    const SERVICE_KEY = 'sharedsearch_service';

    /**
     * @var OrganizationRepository
     */
    private $orgRepository;

    /**
     * @var OrgSearchRepository
     */
    private $orgSearchRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Manager
     */
    public $rbacManager;

    private $validator;

    /**
     * @var LoggerHelperService
     */
    private $loggerHelperService;

    /**
     * @var OrgSearchSharedWithRepository
     */
    private $orgSearchSharedWithRepository;

    /**
     * @var OrgSearchSharedByRepository
     */
    private $orgSearchSharedByRepository;

    /**
     * @var SavedSearchService
     */
    private $savedSearchService;

    /**
     * @var MetadataListValuesRepository
     */
    private $metaDataListValuesRepository;

    /**
     * @var AlertNotificationsService
     */
    private $alertNotificationService;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container"),
     *            "rbacManager" = @DI\Inject("tinyrbac.manager")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container, $rbacManager)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;
        $this->rbacManager = $rbacManager;
        $this->validator = $this->container->get('validator');
        $this->loggerHelperService = $this->container->get('loggerhelper_service');
        $this->savedSearchService = $this->container->get(SharedSearchConstant::SAVED_SEARCH_SERV);
        $this->alertNotificationService = $this->container->get('alertNotifications_service');
        $this->orgSearchSharedWithRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_SHARED_WITH_REPO);
        $this->orgSearchSharedByRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_SHARED_BY_REPO);
        $this->orgRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_REPO);
        $this->metaDataListValuesRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::METADATA_LIST_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::PERSON_REPO);
        $this->orgSearchRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_REPO);
    }

    /**
     * Create Shared Search
     * @param SharedSearchDto $sharedSearchDto
     * @return SharedSearchDto
     */
    public function create(SharedSearchDto $sharedSearchDto)
    {

        // Check shared with Person access to organization

        $this->rbacManager->checkAccessToOrganizationUsingPersonId($sharedSearchDto->getsharedWithPersonIds());

        // Check shared with Person access to organization - END

        $this->logger->debug(" Creating Shared Custom Search " . $this->loggerHelperService->getLog($sharedSearchDto));

        $savedSearch = $this->orgSearchRepository->find($sharedSearchDto->getSavedSearchId());

        // Direct custom search sharing

        if ($sharedSearchDto->getSavedSearchId() == -1) {
            $searchAttributes = $sharedSearchDto->getSearchAttributes();
            $searchAttributesJson = json_encode($searchAttributes);
            $query = $this->savedSearchService->getBaseQuery($searchAttributes, $sharedSearchDto->getSharedByPersonId());
        } else {
            $this->isObjectExist($savedSearch, SharedSearchConstant::ORG_SEARCH_NOT_FOUND, SharedSearchConstant::ORG_SEARCH_NOT_FOUND_KEY);
            $searchAttributesJson = $savedSearch->getJson();
            $query = $savedSearch->getQuery();
        }
        $organization = $this->orgRepository->find($sharedSearchDto->getOrganizationId());
        $this->isObjectExist($organization, SharedSearchConstant::ORG_NOT_FOUND, SharedSearchConstant::ORG_NOT_FOUND_KEY);
        $timezone = $this->metaDataListValuesRepository->findByListName($organization->getTimezone());
        if ($timezone) {
            $timeZone = $timezone[0]->getListValue();
        }

        $sharedOrgSearch = new OrgSearch();
        $sharedOrgSearch->setOrganization($organization);
        $sharedOrgSearch->setName($sharedSearchDto->getSavedSearchName());
        $sharedByPerson = $this->personRepository->find($sharedSearchDto->getSharedByPersonId());
        $sharedByPersonWith = $this->personRepository->find($sharedSearchDto->getsharedWithPersonIds());
        $sharedOrgSearch->setPerson($sharedByPerson);


        // Saving in OrgSearchSharedWith

        $orgSearchSharedWith = new OrgSearchSharedWith();


        // Saving in OrgSearchSharedBy

        $orgSearchSharedBy = new OrgSearchSharedBy();

        $errors = $this->validator->validate($sharedOrgSearch);
        $currentDate = new \DateTime('now');
        $sharedOnDate = Helper::getUtcDate($currentDate);
        $orgSharedBy = $this->orgSearchSharedByRepository->findBy(array(
            'orgSearchSource' => $sharedSearchDto->getSavedSearchId(),
            'personSharedBy' => $sharedSearchDto->getSharedByPersonId()
        ));
        if ($sharedSearchDto->getSavedSearchId() == -1) {

            // Sharing search from Custom Search Tab

            if (count($errors) > 0) {
                if ($sharedSearchDto->getSavedSearchId() == -1) {
                    $message = $errors[0]->getMessage();
                    throw new ValidationException([
                        $message
                    ], $message, $message);
                }
            } else {

                // Saving a record in OrgSearch for shared by person if name is different

                $sharedOrgSearch->setQuery(substr($query, 0, 4999));
                $sharedOrgSearch->setJson($searchAttributesJson);
                $sharedOrgSearch->setEditedByMe(true);
                $sharedOrgSearch->setFromSharedtab(1);
                $sharedSearchInst = $this->orgSearchRepository->createSharedSearch($sharedOrgSearch);
                $orgSearchSharedWith->setOrgSearch($sharedSearchInst);
                $orgSearchSharedBy->setOrgSearchSource($sharedSearchInst);
            }
        } else {

            // Sharing search from Shared Tab

            if (!empty($orgSharedBy)) {
                if (count($errors) > 0) {
                    $orgSharedWith = $this->orgSearchSharedWithRepository->findBy(array(
                        'orgSearch' => $sharedSearchDto->getSavedSearchId(),
                        'personSharedwith' => $sharedSearchDto->getsharedWithPersonIds()
                    ));
                    if (!empty($orgSharedWith)) {
                        $message = $errors[0]->getMessage();
                        throw new ValidationException([
                            $message
                        ], $message, $message);
                    }
                    $orgSearchSharedWith->setOrgSearch($savedSearch);
                    $orgSearchSharedBy->setOrgSearchSource($savedSearch);
                } else {

                    // Saving a record in OrgSearch for shared by person if name is different *

                    $sharedOrgSearch->setQuery(substr($query, 0, 4999));
                    $sharedOrgSearch->setJson($searchAttributesJson);
                    $sharedOrgSearch->setEditedByMe(true);
                    $sharedOrgSearch->setFromSharedtab(1);
                    $sharedSearchInst = $this->orgSearchRepository->createSharedSearch($sharedOrgSearch);
                    $orgSearchSharedWith->setOrgSearch($sharedSearchInst);
                    $orgSearchSharedBy->setOrgSearchSource($sharedSearchInst);
                }
            } else {
                // Sharing search from Saved Tab for Logged in person
                $orgSearchSharedWith->setOrgSearch($sharedOrgSearch);
                $orgSearchSharedBy->setOrgSearchSource($sharedOrgSearch);
                $sharedOrgSearch->setQuery(substr($query, 0, 4999));
                $sharedOrgSearch->setJson($searchAttributesJson);
                if (count($errors) > 0) {
                    $sharedOrgSearch->setEditedByMe(false);
                    $sharedOrgSearch->setFromSharedtab(null);
                } else {
                    $sharedOrgSearch->setEditedByMe(true);
                    $sharedOrgSearch->setFromSharedtab(1);
                }
                $sharedSearchInst = $this->orgSearchRepository->createSharedSearch($sharedOrgSearch);
                $orgSearchSharedWith->setOrgSearch($sharedSearchInst);
                $orgSearchSharedBy->setOrgSearchSource($sharedSearchInst);
            }
        }

        // Saving a record in OrgSearch for shared with person

        $sharedOrgSearchWith = new OrgSearch();
        $sharedOrgSearchWith->setOrganization($organization);
        $sharedOrgSearchWith->setName($sharedSearchDto->getSavedSearchName());
        $sharedOrgSearchWith->setPerson($sharedByPersonWith);
        $errorsPersonWith = $this->validator->validate($sharedOrgSearchWith);

        // Error if same search is shared with same name with same person
        if (count($errorsPersonWith) > 0) {
            $this->isObjectExist(null, SharedSearchConstant::SEARCH_ALREADY_SHARED, SharedSearchConstant::SEARCH_ALREADY_SHARED);
        }
        $sharedOrgSearchWith->setQuery(substr($query, 0, 4999));
        $sharedOrgSearchWith->setJson($searchAttributesJson);
        $sharedOrgSearchWithInst = $this->orgSearchRepository->createSharedSearch($sharedOrgSearchWith);
        $orgSearchSharedWith->setOrgSearchDest($sharedOrgSearchWithInst);
        $orgSearchSharedWith->setPersonSharedwith($sharedByPersonWith);
        $orgSearchSharedWith->setSharedOn($sharedOnDate);
        $orgSearchSharedBy->setOrgSearch($sharedOrgSearchWithInst);
        $orgSearchSharedBy->setPersonSharedBy($sharedByPerson);
        $orgSearchSharedBy->setSharedOn($sharedOnDate);

        $this->orgSearchRepository->flush();
        $this->orgSearchSharedWithRepository->create($orgSearchSharedWith);
        $this->orgSearchSharedByRepository->create($orgSearchSharedBy);
        $this->orgSearchSharedWithRepository->flush();
        $this->alertNotificationService->createNotification("SHARED_SEARCH", $sharedSearchDto->getSavedSearchName(), $sharedByPersonWith, null, null, $sharedOrgSearchWithInst);
        $sharedSearchDto->setId($sharedOrgSearchWithInst->getId());
        $this->logger->info("Shared Search - Create Shared  Search");
        return $sharedSearchDto;
    }

    public function getSharedSearches($loggedInUser, $timezone, $orgId)
    {
        $this->logger->debug(" Get Shared Searches for Logged User Organization Id " . $orgId );
        $this->orgSearchRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_REPO);
        $this->orgSearchSharedWithRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_SHARED_WITH_REPO);
        $this->orgSearchSharedByRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_SHARED_BY_REPO);
        $rsUsershared = $this->orgSearchRepository->getUsersSharedList($loggedInUser, $orgId);
        // Setting the response dto
        $searchList = array();
        $searchWithList = array();
        $searchWith = $this->orgSearchSharedWithRepository->getSharedSearchWith();
        if (isset($searchWith) && count($searchWith) > 0) {
            foreach ($searchWith as $sWith) {
                $searchWithList[$sWith[SharedSearchConstant::ORG_SEARCH]][] = $sWith;
            }
        }
        $timez = $this->repositoryResolver->getRepository('SynapseCoreBundle:MetadataListValues')->findByListName($timezone);

        foreach ($rsUsershared as $resp) {
            $sharedSearchList = new SharedSearchListResponseDto();
            $sharedSearchList->setSavedSearchId($resp['saved_search_id']);
            $sharedSearchList->setSearchName($resp['name']);

            $sharedOnByDate = $resp['shared_on'];
            if ($sharedOnByDate) {
                $sharedOnByDate = new \DateTime($resp['shared_on']);
            }
            $sharedBy = array();
            $sharedWith = array();
            if ($resp['flag'] == 'with') {
                foreach ($searchWithList[$resp['saved_search_id']] as $sWList) {
                    $sharedWithUsers = new SharedWithUsersArrayDto();
                    $sharedOnDate = $sWList['dateShared'];
                    $sharedPerson = (isset($sWList['personIdSharedwith'])) ? $sWList['personIdSharedwith'] : null;
                    $sharedWithUsers->setSharedWithPersonId($sharedPerson);
                    $sharedWithUsers->setSharedSearchId($sWList['shared_search_id']);
                    $sharedWithUsers->setSharedWithFirstName($sWList['firstname']);
                    $sharedWithUsers->setSharedWithLastName($sWList['lastname']);
                    $sharedWithUsers->setDateShared($sharedOnDate);
                    $sharedWith[] = $sharedWithUsers;
                }
            } else {
                $sharedByUsers = new SharedByUsersArrayDto();
                $sharedByUsers->setSharedByPersonId($resp['person_id']);
                $sharedByUsers->setSharedSearchId($resp['saved_search_id']);
                $sharedByUsers->setSharedByFirstName($resp['firstname']);
                $sharedByUsers->setSharedByLastName($resp['lastname']);
                $sharedByUsers->setDateShared($sharedOnByDate);
                $sharedBy[] = $sharedByUsers;
                $sharedSearchList->setSavedSearchId($resp['shared_search_id']);
            }

            $sharedSearchList->setSharedWithUsers($sharedWith);
            $sharedSearchList->setSharedByUsers($sharedBy);
            $searchList['shared_searches'][] = $sharedSearchList;
        }
        $this->logger->info("Shared Get Shared Searches for Logged User");
        return $searchList;
    }

    public function delete($searchId, $shared_search_id = null, $shared_by_user_id = null)
    {
        $this->logger->debug(" Delete Shared Search for Search Id" . $searchId . "Shared Search Id" . $shared_search_id . "Shared By User Id" . $shared_by_user_id);
        if (! empty($shared_by_user_id) && $shared_by_user_id > 0) {
            $this->orgSearchRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_REPO);
            $this->orgSearchSharedByRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_SHARED_BY_REPO);
            $orgSharedWith = $this->orgSearchSharedByRepository->findOneBy([
                SharedSearchConstant::ORG_SEARCH => $shared_search_id,
                'personSharedBy' => $shared_by_user_id,
                'orgSearchSource' => $searchId
            ]);
            $this->isObjectExist($orgSharedWith, SharedSearchConstant::ORG_SEARCH_NOT_FOUND, SharedSearchConstant::SEARCH_NOT_FOUND_KEY);
            if ($orgSharedWith) {
                $this->orgSearchSharedByRepository->deleteSharedSearchBy($orgSharedWith);
            }
            $this->orgSearchSharedByRepository->flush();
        } else {
            $this->orgSearchSharedWithRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_SHARED_WITH_REPO);
            $orgSharedWith = $this->orgSearchSharedWithRepository->findBy([
                SharedSearchConstant::ORG_SEARCH => $searchId
            ]);
            $this->isObjectExist($orgSharedWith, SharedSearchConstant::ORG_SEARCH_NOT_FOUND, SharedSearchConstant::SEARCH_NOT_FOUND_KEY);
            if ($orgSharedWith) {
                $this->deleteOrgSharedWith($orgSharedWith);
            }
            $this->orgSearchSharedWithRepository->flush();
        }
        $this->logger->info("Shared Search - Delete Shared Search by SearchId");
        return $shared_search_id;
    }

    public function edit(SaveSearchDto $saveSearchDto, $loggedInUser)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($saveSearchDto);
        $this->logger->debug(" Editing Shared Custom Search " . $logContent );

        $this->orgSearchRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_REPO);
        $this->orgRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_REPO);
        $this->orgSearchSharedWithRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_SHARED_WITH_REPO);

        $savedSearchID = $saveSearchDto->getSavedSearchId();

        $orgSearchShared = $this->orgSearchRepository->findOneBy([
            'id' => $savedSearchID
        ]);
        $this->isObjectExist($orgSearchShared, SharedSearchConstant::ORG_SEARCH_NOT_FOUND, SharedSearchConstant::SEARCH_NOT_FOUND_KEY);

        $organization = $this->orgRepository->find($saveSearchDto->getOrganizationId());
        $this->isObjectExist($organization, SharedSearchConstant::ORG_NOT_FOUND, SharedSearchConstant::ORG_NOT_FOUND_KEY);
        // Find the editing shared search is owner's or recipient's
        $shareWith = $this->orgSearchRepository->findSearchSharedWith($savedSearchID, $loggedInUser);

        $orgSearch = new OrgSearch();
        $orgSearch->setOrganization($organization);
        $orgSearch->setName($saveSearchDto->getSavedSearchName());
        $this->personRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::PERSON_REPO);
        $sharedByPerson = $this->personRepository->find($loggedInUser);
        $orgSearch->setPerson($sharedByPerson);

        if (isset($shareWith) && count($shareWith) > 0) {
            // Edit by owner
            $validator = $this->container->get('validator');
            $errors = $validator->validate($orgSearch);
            if (count($errors) > 0) {
                // Edit with out changing name;
                $orgSharedWith = $this->orgSearchSharedWithRepository->findBy([
                    SharedSearchConstant::ORG_SEARCH => $savedSearchID
                ]);
                if ($orgSharedWith) {
                    /*
                     * foreach ($orgSharedWith as $shareWith) { $this->orgSearchSharedWithRepository->deleteSharedSearchWith($shareWith); }
                     */
                    //$this->deleteOrgSharedWith($orgSharedWith);
                }
                $savedSearch = $this->container->get(SharedSearchConstant::SAVED_SEARCH_SERV)->editSavedSearches($saveSearchDto);
            } else {
                // Edit with changing the name
                $savedSearch = $this->container->get(SharedSearchConstant::SAVED_SEARCH_SERV)->createSavedSearches($saveSearchDto, $loggedInUser);
            }
        } else {
            // edit by recipient
            $orgSearchSharedBy = $this->orgSearchRepository->findOneBy([
                'id' => $savedSearchID
            ]);
            if (isset($orgSearchSharedBy) && count($orgSearchSharedBy) > 0) {
                $orgSearchSharedBy->setEditedByMe(true);
            }
            $this->orgSearchSharedByRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_SHARED_BY_REPO);
            $orgSharedBy = $this->orgSearchSharedByRepository->findOneBy([
                SharedSearchConstant::ORG_SEARCH => $savedSearchID
            ]);
            if ($orgSharedBy) {
                $this->orgSearchSharedByRepository->deleteSharedSearchBy($orgSharedBy);
            }

            $orgSharedWith = $this->orgSearchSharedWithRepository->findBy([
                SharedSearchConstant::ORG_SEARCH => $savedSearchID
            ]);
            if ($orgSharedWith) {
                /*
                 * foreach ($orgSharedWith as $shareWith) { $this->orgSearchSharedWithRepository->deleteSharedSearchWith($shareWith); }
                 */
                $this->deleteOrgSharedWith($orgSharedWith);
            }
            $savedSearch = $this->container->get(SharedSearchConstant::SAVED_SEARCH_SERV)->editSavedSearches($saveSearchDto);
        }
        $this->logger->info("Shared Search - Edit Shared Search for Logged In User ");
        return $orgSearchShared;
    }

    private function isObjectExist($object, $message, $key)
    {
        if (! ($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    private function getTimezoneDt($timez, $dt)
    {
        if ($timez) {
            $mTimezone = $timez[0]->getListValue();
            Helper::setOrganizationDate($dt, $mTimezone);
        }
        return $dt;
    }

    private function deleteOrgSharedWith($orgSharedWith)
    {
        $this->orgSearchSharedWithRepository = $this->repositoryResolver->getRepository(SharedSearchConstant::ORG_SEARCH_SHARED_WITH_REPO);
        foreach ($orgSharedWith as $shareWith) {
            $this->orgSearchSharedWithRepository->deleteSharedSearchWith($shareWith);
        }
    }
}