<?php
namespace Synapse\MultiCampusBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrganizationLang;
use Synapse\MultiCampusBundle\Service\TierServiceInterface;
use Synapse\MultiCampusBundle\EntityDto\TierDto;
use Synapse\MultiCampusBundle\EntityDto\PrimaryTierDto;
use Synapse\MultiCampusBundle\EntityDto\SecondaryTiersDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Constants\TierConstant;
use Synapse\MultiCampusBundle\EntityDto\UsersDto;
use Synapse\MultiCampusBundle\EntityDto\PermissionDto;

class TierHelperService extends AbstractService
{

    /**
     *
     * @var TierUsersRepository
     */
    private $tierUsersRepository;

    /**
     *
     * @var TierRepository
     */
    private $tierRepository;

    protected function createPrimaryTier(TierDto $tierDto, $langService)
    {
        if (trim($tierDto->getPrimaryTierName() == "")) {
            throw new ValidationException([
                TierConstant::NAMEFIELD_EMPTY
            ], TierConstant::NAMEFIELD_EMPTY, TierConstant::NAMEFIELD_EMPTY);
        }
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $language = $langService->getLanguageById($tierDto->getLangid());
        $tierlevel = '1';
        $tier = new Organization();
        $tier->setCampusId($tierDto->getPrimaryTierId());
        $tier->setTier($tierlevel);
        $this->validateEntity($tier);
        $this->tierRepository->createOrganization($tier);
        $tierDetails = new OrganizationLang();
        $tierDetails->setOrganizationName($tierDto->getPrimaryTierName());
        $this->logger->info("Primary Tier to be created - " . $tierDetails->getOrganizationName());
        $tierDetails->setDescription($tierDto->getDescription());
        $tierDetails->setOrganization($tier);
        $tierDetails->setLang($language);
        $this->validateEntity($tierDetails);
        // call create tierDetails
        $this->tierDetailsRepository->createOrganizationLang($tierDetails);
        $this->tierDetailsRepository->flush();
        $tierData = new TierDto();
        $tierData->setId($tier->getId());
        $tierData->setTierLevel($tierDto->getTierLevel());
        $tierData->setPrimaryTierName($tierDetails->getOrganizationName());
        $tierData->setPrimaryTierId($tier->getCampusId());
        $tierData->setDescription($tierDetails->getDescription());
        $tierData->setLangid($tierDetails->getLang()
            ->getId());
        $this->tierRepository->flush();
        $this->logger->info("Primary Tier is created successfully");
        return $tierData;
    }

    protected function createSecondaryTier(TierDto $tierDto, $langService)
    {
        if (trim($tierDto->getSecondaryTierName() == "")) {
            throw new ValidationException([
                TierConstant::NAMEFIELD_EMPTY
            ], TierConstant::NAMEFIELD_EMPTY, TierConstant::NAMEFIELD_EMPTY);
        }
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $language = $langService->getLanguageById($tierDto->getLangid());
        $tierlevel = '2';
        $tier = new Organization();
        
        $tierdata = $this->tierRepository->find($tierDto->getPrimaryTierId());
        if (isset($tierdata)) {
            $tier->setParentOrganizationId($tierdata->getId());
        } else {
            $this->logger->error( " Multi Campus Bundle - Campus Service Base Helper - Create Secondary Tier " . TierConstant::TIER_NOT_FOUND . TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $this->logger->info("Create Secondary Tier");
        $tier->setCampusId($tierDto->getSecondaryTierId());
        $tier->setTier($tierlevel);
        $this->validateEntity($tier);
        $this->tierRepository->createOrganization($tier);
        
        $tierDetails = new OrganizationLang();
        
        $tierDetails->setOrganizationName($tierDto->getSecondaryTierName());
        $this->logger->info("Secondary Tier to be created" . $tierDetails->getOrganizationName());
        $tierDetails->setDescription($tierDto->getDescription());
        $tierDetails->setOrganization($tier);
        $tierDetails->setLang($language);
        $this->validateEntity($tierDetails);
        // call create tierDetails
        $this->tierDetailsRepository->createOrganizationLang($tierDetails);
        $this->tierDetailsRepository->flush();
        $tierDto->setId($tier->getId());
        $tierDto->setTierLevel($tierDto->getTierLevel());
        $tierDto->setSecondaryTierName($tierDetails->getOrganizationName());
        $tierDto->setSecondaryTierId($tier->getCampusId());
        $tierDto->setDescription($tierDetails->getDescription());
        $tierDto->setLangid($tierDetails->getLang()
            ->getId());
        $this->tierRepository->flush();
        $this->logger->info("Secondary Tier is created successfully");
        return $tierDto;
    }

    protected function updatePrimaryTier($tierDto)
    {
        if (trim($tierDto->getPrimaryTierName() == "")) {
            throw new ValidationException([
                TierConstant::NAMEFIELD_EMPTY
            ], TierConstant::NAMEFIELD_EMPTY, TierConstant::NAMEFIELD_EMPTY);
        }
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $tierObj = array();
        $tierObj = $this->tierDetailsRepository->findOneBy(array(
            TierConstant::FIELD_ORGANIZATION => $tierDto->getId()
        ));
        $this->logger->info("Primary Tier to be updated" . $tierDto->getId());
        $tier = $tierObj->getOrganization();
        $campusId = $tierDto->getPrimaryTierId();
        $tier->setCampusId($campusId);
        $this->validateEntity($tier);
        $tierObj->setOrganizationName($tierDto->getPrimaryTierName());
        $this->logger->info("Primary Tier to be updated" . $tierObj->getOrganizationName());
        $tierObj->setDescription($tierDto->getDescription());
        $this->validateEntity($tierObj);
        $this->tierDetailsRepository->flush();
        $this->logger->info("Primary Tier is updated successfully");
        return $this->viewPrimaryTier($tierDto->getId(), $tierDto->getTierLevel());
    }

    protected function updateSecondaryTier($tierDto)
    {
        if (trim($tierDto->getSecondaryTierName() == "")) {
            throw new ValidationException([
                TierConstant::NAMEFIELD_EMPTY
            ], TierConstant::NAMEFIELD_EMPTY, TierConstant::NAMEFIELD_EMPTY);
        }
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $tierObj = array();
        $tierObj = $this->tierDetailsRepository->findOneBy(array(
            TierConstant::FIELD_ORGANIZATION => $tierDto->getId()
        ));
        $tier = $tierObj->getOrganization();
        $campusId = $tierDto->getSecondaryTierId();
        $tier->setCampusId($campusId);
        $this->validateEntity($tier);
        $tierObj->setOrganizationName($tierDto->getSecondaryTierName());
        $this->logger->info("Secondary Tier to be updated" );
        $tierObj->setDescription($tierDto->getDescription());
        $this->validateEntity($tierObj);
        $this->tierDetailsRepository->flush();
        $this->logger->info("Secondary Tier is updated successfully");
        return $this->viewSecondaryTier($tierDto->getId(), $tierDto->getTierLevel());
    }

    protected function viewPrimaryTier($tierId)
    {
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $tierDetails = $this->tierDetailsRepository->findOneBy([
            TierConstant::FIELD_ORGANIZATION => $tierId
        ]);
        $this->logger->info("View Primary Tier Details" . $tierId);
        if (isset($tierDetails)) {
            $tierDto = new TierDto();
            $tierLevel = 'primary';
            $tierDto->setId($tierDetails->getOrganization()
                ->getId());
            
            $tierDto->setTierLevel($tierLevel);
            $tierDto->setPrimaryTierName($tierDetails->getOrganizationName());
            $tierDto->setPrimaryTierId($tierDetails->getOrganization()
                ->getCampusId());
            $tierDto->setDescription($tierDetails->getDescription());
        } else {
            $this->logger->error( " Multi Campus Bundle - Campus Service Base Helper - viewPrimaryTier " . TierConstant::TIER_NOT_FOUND . TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $this->logger->info("Primary Tier details are listed");
        return $tierDto;
    }

    protected function viewSecondaryTier($tierId)
    {
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $tierDetails = $this->tierDetailsRepository->findOneBy([
            TierConstant::FIELD_ORGANIZATION => $tierId
        ]);
        $this->logger->info("View Secondary Tier Details ");
        if (isset($tierDetails)) {
            $tierDto = new TierDto();
            $tierLevel = 'secondary';
            $tierDto->setId($tierDetails->getOrganization()
                ->getId());
            
            $tierDto->setTierLevel($tierLevel);
            $tierDto->setSecondaryTierName($tierDetails->getOrganizationName());
            $tierDto->setSecondaryTierId($tierDetails->getOrganization()
                ->getCampusId());
            $tierDto->setPrimaryTierId($tierDetails->getOrganization()
                ->getParentOrganizationId());
            $tierDto->setDescription($tierDetails->getDescription());
        } else {
            $this->logger->error( "MultiCampus Bundle - Tier Helper Service - viewSecondaryTier" . TierConstant::TIER_NOT_FOUND . TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $this->logger->info("Secondary Tier details are listed");
        return $tierDto;
    }

    protected function listPrimaryTier($tierId = null)
    {
        $this->logger->info("List all Primary Tiers");
        $this->tiersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->orgUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $tiersinfo = $this->tiersRepository->listPrimaryTiers();
        $primaryTier = [];
        $listTierDto = new PrimaryTierDto();
        if (isset($tiersinfo)) {
            foreach ($tiersinfo as $tier) {
                $tierDto = new TierDto();
                $tierId = $tier[TierConstant::ORG_ID];
                $tierDto->setId($tierId);
                $tierDto->setPrimaryTierId($this->checkNullResponse($tier['campusId']));
                $tierDto->setPrimarytierName($this->checkNullResponse($tier['organizationName']));
                $primaryTier[] = $tierDto;
                $secondary = $this->tiersRepository->listCampuses($tierId, '2');
                $campusId = array_column($secondary, TierConstant::ORG_ID);
                $campus = $this->tiersRepository->listCampuses($campusId, '3');
                $tierDto->setTotalSecondaryTiers(count($secondary));
                $tierDto->setTotalCampus(count($campus));
                $users = $this->orgUsersRepository->usersCount($tierId);
                $tierDto->setTotalUsers($users);
            }
            $listTierDto->setPrimaryTiers($primaryTier);
        } else {
            $this->logger->error( " MultiCampus Bundle - Tier Helper Service - viewSecondaryTier " . TierConstant::TIER_NOT_FOUND . TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $this->logger->info("All Primary Tiers are listed");
        return $listTierDto;
    }

    protected function listSecondaryTier($tierId)
    {
        $this->logger->info("List all Secondary Tiers");
        $this->tiersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->orgUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $secondaryTier = [];
        $tierinfo = $this->tiersRepository->listCampuses($tierId, '2');
        $listTierDto = new SecondaryTiersDto();
        $listTierDto->setPrimaryTierId($tierId);
        if (isset($tierinfo)) {
            foreach ($tierinfo as $tierinfodetails) {
                $listTierDto->setTotalSecondaryTiers(count($tierinfo));
                $tierDto = new TierDto();
                $secondaryTierId = $tierinfodetails[TierConstant::ORG_ID];
                $tierDto->setId($secondaryTierId);
                $tierDto->setSecondaryTierId($this->checkNullResponse($tierinfodetails['campusId']));
                $tierDto->setSecondaryTierName($this->checkNullResponse($tierinfodetails['organizationName']));
                $campus = $this->tiersRepository->listCampuses($secondaryTierId, '3');
                $users = $this->orgUsersRepository->usersCount($secondaryTierId);
                $tierDto->setTotalCampus(count($campus));
                $tierDto->setTotalUsers($users);
                
                $secondaryTier[] = $tierDto;
            }
            $listTierDto->setSecondaryTiers($secondaryTier);
        } else {
            $this->logger->error( " Multi Campus Bundle - Tier Helper Service - listSecondaryTier - " . TierConstant::TIER_NOT_FOUND . TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $this->logger->info("All Secondary Tiers are listed");
        return $listTierDto;
    }

    protected function listPrimaryTierUser($tierId)
    {
        $this->logger->info("List all Primary Tier users");
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->tierUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        $tierPrimaryInfo = $this->tierRepository->findOneBy(array(
            'id' => $tierId,
            'tier' => '1'
        ));
        if (! empty($tierPrimaryInfo)) {
            $primaryTierUsers = [];
            
            $tierDto = new TierDto();
            
            $tierUsers = $this->tierUsersRepository->findBy([
                TierConstant::FIELD_ORGANIZATION => $tierId
            ]);
            $tierDto->setTotalPrimaryTierUsers(count($tierUsers));
            $tierDto->setPrimaryTierId($tierId);
            
            if (! empty($tierUsers)) {
                foreach ($tierUsers as $tierUser) {
                    $contacts = $tierUser->getPerson()->getContacts();
                    $userDto = new UsersDto();
                    $userDto->setUserId($tierUser->getPerson()
                        ->getId());
                    $userDto->setFirstName($this->checkNullResponse($tierUser->getPerson()
                        ->getFirstName()));
                    $userDto->setLastName($this->checkNullResponse($tierUser->getPerson()
                        ->getLastName()));
                    $userDto->setTitle($this->checkNullResponse($tierUser->getPerson()
                        ->getTitle()));
                    $this->setContactsDetails($contacts, $userDto);
                    $primaryTierUsers[] = $userDto;
                }
            }
            $tierDto->setUsers($primaryTierUsers);
        } else {
            $this->logger->error( " Multi Campus Bundle - Tier Helper Service - listPrimaryTierUser - " . TierConstant::TIER_NOT_FOUND . TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $this->logger->info("Primary Tier users are listed");
        return $tierDto;
    }

    private function setContactsDetails($contacts, $userDto)
    {
        if (isset($contacts)) {
            $userDto->setEmail($this->checkNullResponse($contacts[0]->getPrimaryEmail()));
            if ($contacts[0]->getPrimaryMobile()) {
                $userDto->setPhone($this->checkNullResponse($contacts[0]->getPrimaryMobile()));
            } else {
                $userDto->setPhone($this->checkNullResponse($contacts[0]->getHomePhone()));
            }
        }
    }

    protected function listSecondaryTierUser($tierId)
    {
        $this->logger->info("List all Secondary Tier users");
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->tierUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        $tierSecondaryInfo = $this->tierRepository->findOneBy(array(
            'id' => $tierId,
            'tier' => '2'
        ));
        if (! empty($tierSecondaryInfo)) {
            $secondaryTierUsers = [];
            $tierDto = new TierDto();
            
            $tierUsers = $this->tierUsersRepository->findBy([
                'organization' => $tierId
            ]);
            $tierDto->setTotalSecondaryTierUsers(count($tierUsers));
            $tierDto->setPrimaryTierId($tierSecondaryInfo->getParentOrganizationId());
            $tierDto->setSecondaryTierId($tierId);
            
            if (! empty($tierUsers)) {
                foreach ($tierUsers as $tierUser) {
                    $contacts = $tierUser->getPerson()->getContacts();
                    $userDto = new UsersDto();
                    $userDto->setUserId($tierUser->getPerson()
                        ->getId());
                    $userDto->setFirstName($this->checkNullResponse($tierUser->getPerson()
                        ->getFirstName()));
                    $userDto->setLastName($this->checkNullResponse($tierUser->getPerson()
                        ->getLastName()));
                    $userDto->setTitle($this->checkNullResponse($tierUser->getPerson()
                        ->getTitle()));
                    $this->setContactsDetails($contacts, $userDto);
                    $secondaryTierUsers[] = $userDto;
                }
            }
            $tierDto->setUsers($secondaryTierUsers);
        } else {
            $this->logger->error( "Multi Campus Bundle - Tier Helper Service - listSecondaryTierUser - " . TierConstant::TIER_NOT_FOUND . TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $this->logger->info("Secondary Tier users are listed");
        return $tierDto;
    }

    protected function deletePrimaryTierUser($personId, $tierId)
    {
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->tierUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $tierPrimaryInfo = $this->tierRepository->findOneBy(array(
            'id' => $tierId,
            'tier' => '1'
        ));
        if (! empty($tierPrimaryInfo)) {
            
            $tierUsers = $this->tierUsersRepository->findOneBy([
                TierConstant::FIELD_ORGANIZATION => $tierId,
                TierConstant::FIELD_PERSON => $personId
            ]);
            $this->logger->info("Primary Tier user has to be deleted - ");
            if (count($tierUsers) > 0) {
                $this->tierUsersRepository->remove($tierUsers);
                $this->tierUsersRepository->flush();
            }
        } else {
            $this->logger->error( " Multi Campus Bundle - Tier Helper Service - deletePrimaryTierUser - " . TierConstant::TIER_NOT_FOUND . TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $this->logger->info("Primary Tier user is deleted");
    }

    protected function deleteSecondaryTierUser($personId, $tierId)
    {
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->tierUsersRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_ORG_USERS);
        $tierSecondaryInfo = $this->tierRepository->findOneBy(array(
            'id' => $tierId,
            'tier' => '2'
        ));
        if (! empty($tierSecondaryInfo)) {
            
            $tierUsers = $this->tierUsersRepository->findOneBy([
                TierConstant::FIELD_ORGANIZATION => $tierId,
                TierConstant::FIELD_PERSON => $personId
            ]);
            $this->logger->info(" Secondary Tier user has to be deleted - ");
            if (count($tierUsers) > 0) {
                $this->tierUsersRepository->remove($tierUsers);
                $this->tierUsersRepository->flush();
            }
        } else {
            $this->logger->error( " Multi Campus Bundle - Tier Helper Service - deleteSecondaryTierUser - " . TierConstant::TIER_NOT_FOUND . TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $this->logger->info("Secondary Tier user is deleted");
    }

    protected function deleteSecondaryTier($personId, $tierId)
    {
        $this->tierRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Organization');
        $this->tierUsersRepository = $this->repositoryResolver->getRepository('SynapseMultiCampusBundle:OrgUsers');
        $tierSecondaryInfo = $this->tierRepository->findOneBy(array(
            'id' => $tierId,
            'tier' => '2'
        ));
        if (! empty($tierSecondaryInfo)) {
            $tierUsers = $this->tierUsersRepository->findOneBy([
                TierConstant::FIELD_ORGANIZATION => $tierId,
                TierConstant::FIELD_PERSON => $personId
            ]);
            $this->logger->info("Secondary Tier user has to be deleted - ");
            if (count($tierUsers)) {
                $this->tierUsersRepository->remove($tierUsers);
            }
            $this->tierUsersRepository->flush();
        } else {
            $this->logger->error( " Multi Campus Bundle - Tier Helper Service - deleteSecondaryTier - " . TierConstant::TIER_NOT_FOUND . TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $this->logger->info("Secondary Tier user is deleted");
    }

    /**
     * Validate entity
     */
    private function validateEntity($entity)
    {
        $validator = $this->container->get('validator');
        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            $errorsString = "";
            $errorsString = $errors[0]->getMessage();
            $this->logger->error( " Multi Campus Bundle - Tier Helper Service - validateEntity - " . $errorsString . 'entity_validation');
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'entity_validation');
        }
    }

    /**
     * return Empty Value for null value
     */
    private function checkNullResponse($input)
    {
        return $input ? $input : '';
    }

    protected function getFacultyPermissions($personPermission)
    {
        $id = array();
        $permissions = array();
        foreach ($personPermission as $groupPermission) {
            if (! empty($groupPermission->getOrgPermissionset())) {
                $permissionSetId = $groupPermission->getOrgPermissionset()->getId();
                if ($groupPermission->getOrgPermissionset() && $groupPermission->getOrgPermissionset() != null && ! in_array($permissionSetId, $id)) {
                    $permissionsDto = new PermissionDto();
                    $permissionsDto->setPermissionId($permissionSetId);
                    $permissionsDto->setPermissionName($groupPermission->getOrgPermissionset()
                        ->getPermissionsetName());
                    $permissions[] = $permissionsDto;
                    $id[] = $permissionSetId;
                }
            }
        }
        return $permissions;
    }
}