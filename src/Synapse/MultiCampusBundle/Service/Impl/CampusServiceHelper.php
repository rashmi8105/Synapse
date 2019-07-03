<?php
namespace Synapse\MultiCampusBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\OrganizationLang;
use Synapse\MultiCampusBundle\Service\CampusServiceInterface;
use Synapse\CoreBundle\Util\Constants\TierConstant;
use Synapse\MultiCampusBundle\EntityDto\CampusDto;
use Synapse\MultiCampusBundle\EntityDto\ListCampusDto;
use Synapse\MultiCampusBundle\EntityDto\PrimaryTierDto;
use Synapse\MultiCampusBundle\EntityDto\TierDto;
use Synapse\MultiCampusBundle\EntityDto\CampusTypesDto;
use Synapse\MultiCampusBundle\EntityDto\SecondaryTiersDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\MultiCampusBundle\EntityDto\UsersDto;
use Synapse\MultiCampusBundle\Entity\OrgConflict;

class CampusServiceHelper extends CampusServiceBaseHelper
{

    protected function listHierarchyCampuses($tierId, $campusType, $filter)
    {
        $this->campusRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->campuslangRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $tierDetail = $this->campusRepository->findOneBy([
            'id' => $tierId
        ]);
        $this->isEmpty($tierDetail, 'Secondary Tier Not Found');
        $parentId = $tierDetail->getParentOrganizationId();
        $primaryTier = $this->campusRepository->findOneBy([
            'id' => $parentId
        ]);
        $this->isEmpty($tierDetail, 'Primary Tier Not Found');
        $primaryTierLang = $this->campuslangRepository->findOneBy([
            TierConstant::FIELD_ORGANIZATION => $primaryTier->getId()
        ]);
        $value = '';
        $parentDto = new TierDto();
        if (($campusType == 'all' || $campusType == 'hierarchy') && ! empty($primaryTierLang)) {
            $primaryTierDto = new PrimaryTierDto();
            $primaryTierDto->setId($primaryTierLang->getOrganization()
                ->getId());
            $primaryTierDto->setPrimaryTierId($primaryTierLang->getOrganization()
                ->getCampusId());
            $primaryTierDto->setPrimaryTierName($primaryTierLang->getOrganizationName());
            $parentDto->setHierarchyCampuses($primaryTierDto);
            $secondaryTiers = $this->campusRepository->listCampuses($parentId, '2');
            if (! empty($secondaryTiers)) {
                $secondaryTierDetails = array();
                $secondaryTierIds = array_column($secondaryTiers, TierConstant::ORGID);
                $secondaryTierIds = $this->arrayPop($tierId, $secondaryTierIds);
                $campuses = $this->campusRepository->searchHierarchyCampuses('hierarchy', $filter, $secondaryTierIds);
                $campusList = $this->getGroup($campuses);
                foreach ($secondaryTiers as $secondaryTier) {
                    $secondaryTierDto = new SecondaryTiersDto();
                    $tierId = $secondaryTier[TierConstant::ORGID];
                    $campusDetails = '';
                    if (isset($campusList[$tierId]) && count($campusList[$tierId]) > 0) {
                        $secondaryTierDto->setId($tierId);
                        $secondaryTierDto->setSecondaryTierId($secondaryTier[TierConstant::CAMPUSID]);
                        $secondaryTierDto->setSecondaryTierName($secondaryTier[TierConstant::ORGNAME]);
                        $secondaryTierDetails[] = $secondaryTierDto;
                        $campusDetails = $this->generateHierarchyCampusDto($campusList[$tierId]);
                        $value = TierConstant::EXISTS;
                    }
                    $secondaryTierDto->setCampuses($campusDetails);
                }
                $primaryTierDto->setSecondaryTiers($secondaryTierDetails);
            }
        }
        $grandParentDto = $this->listStandalone($campusType, $filter, $parentDto, $value);
        return $grandParentDto;
    }

    private function listStandalone($campusType, $filter, $parentDto, $value)
    {
        $grandParentDto = new CampusTypesDto();
        if ($campusType == 'all' || $campusType == 'standalone') {
            $soloCampuses = $this->campusRepository->searchHierarchyCampuses('solo', $filter);
            $soloCampusDetails = $this->generateSoloCampusDto($soloCampuses);
            $parentDto->setSoloCampuses($soloCampusDetails);
            $value = $this->isSoloCampusDisplay($soloCampusDetails, $value);
        }
        if ($value == TierConstant::EXISTS) {
            $grandParentDto->setCampusList($parentDto);
        }
        return $grandParentDto;
    }

    private function isSoloCampusDisplay($soloCampusDetails, $value)
    {
        $value = (! empty($soloCampusDetails)) ? TierConstant::EXISTS : $value;
        return $value;
    }

    private function arrayPop($tierId, $secondaryTierIds)
    {
        if (($key = array_search($tierId, $secondaryTierIds)) !== false) {
            unset($secondaryTierIds[$key]);
        }
        return $secondaryTierIds;
    }

    private function generateHierarchyCampusDto($campusList)
    {
        $this->orgRoleRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrganizationRole');
        $campusDetails = array();
        if (! empty($campusList)) {
            foreach ($campusList as $campus) {
                $campusDto = new CampusDto();
                $campusDto->setId($campus[TierConstant::ORGID]);
                $campusDto->setCampusName($campus[TierConstant::ORGNAME]);
                $campusDto->setCampusId($campus[TierConstant::CAMPUSID]);
                $users = $this->orgRoleRepository->getCoordinators($campus[TierConstant::ORGID]);
                $campusDto->setCountUsers(count($users));
                $campusDetails[] = $campusDto;
            }
        }
        return $campusDetails;
    }

    private function generateSoloCampusDto($soloCampuses)
    {
        $this->orgRoleRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:OrganizationRole');
        $soloCampusDetails = array();
        if (! empty($soloCampuses)) {
            foreach ($soloCampuses as $soloCampus) {
                $soloCampusDto = new CampusDto();
                $soloCampusDto->setId($soloCampus[TierConstant::ORGID]);
                $soloCampusDto->setCampusName($soloCampus[TierConstant::ORGNAME]);
                $soloCampusDto->setCampusId($soloCampus[TierConstant::CAMPUSID]);
                $users = $this->orgRoleRepository->getCoordinators($soloCampus[TierConstant::ORGID]);
                $soloCampusDto->setCountUsers(count($users));
                $soloCampusDetails[] = $soloCampusDto;
            }
        }
        return $soloCampusDetails;
    }

    protected function tierUsersBinding($tierUsers, $role)
    {
        $this->campusRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->orgLangRepo = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $usersArray = [];
        foreach ($tierUsers as $tierUser) {
            $contacts = $tierUser->getPerson()->getContacts();
            $userDto = new UsersDto();
            $userDto->setUserId($tierUser->getPerson()
                ->getId());
            $userDto->setFirstName($tierUser->getPerson()
                ->getFirstName());
            $userDto->setLastName($tierUser->getPerson()
                ->getLastName());
            $userDto->setTitle($tierUser->getPerson()
                ->getTitle());
            $userDto->setRole($role);
            if ($tierUser->getOrganization()->getTier() == 1) {
                $primary = $this->orgLangRepo->findOneBy(array(
                    TierConstant::FIELD_ORGANIZATION => $tierUser->getOrganization()
                ));
                $userDto->setPrimaryTierName($primary->getOrganizationName());
            } else {
                $parent = $this->campusRepository->getTierLevelOrder($tierUser->getOrganization()
                    ->getId());
                $userDto->setPrimaryTierName($parent[0]['primaryName']);
                $userDto->setSecondaryTierName($parent[0]['secondaryName']);
            }
            
            if (isset($contacts)) {
                $userDto->setEmail($this->checkNullResponse($contacts[0]->getPrimaryEmail()));
                if ($contacts[0]->getPrimaryMobile()) {
                    $userDto->setPhone($this->checkNullResponse($contacts[0]->getPrimaryMobile()));
                } else {
                    $userDto->setPhone($this->checkNullResponse($contacts[0]->getHomePhone()));
                }
            }
            $usersArray[] = $userDto;
        }
        return $usersArray;
    }

    protected function getOrganization($campusId)
    {
        $this->campusLangRepo = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $campus = $this->campusLangRepo->findOneBy(array(
            TierConstant::FIELD_ORGANIZATION => $campusId
        ));
        return $campus->getOrganizationName();
    }

    protected function getRole($coordinator, $campus)
    {
        $this->orgRoleRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_ROLE_REPO);
        $this->roleLangRepository = $this->repositoryResolver->getRepository(TierConstant::ROLE_LANG_REPO);
        $roleName = '';
        $coordinators = $this->orgRoleRepository->findBy(array(
            'organization' => $campus,
            'person' => $coordinator
        ));
        if (! empty($coordinators)) {
            $roleId = $coordinators[0]->getRole()->getId();
            $roles = $this->roleLangRepository->findBy(array(
                'role' => $roleId
            ));
            if (isset($roles)) {
                foreach ($roles as $role) {
                    $roleName = $role->getRolename();
                }
            }
        }
        return $roleName;
    }

    private function checkNullResponse($input)
    {
        return $input ? $input : '';
    }

    protected function removeExistingHomeCampus($person)
    {
        $this->personStudentRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_PERSON_STUDENT_REPO);
        $personStudent = $this->personStudentRepository->findBy(array(
            'person' => $person,
            'isHomeCampus' => '1'
        ));
        if (! empty($personStudent)) {
            foreach ($personStudent as $student) {
                $student->setIsHomeCampus(NULL);
            }
            $this->personStudentRepository->flush();
        }
    }
}