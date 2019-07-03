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

class CampusServiceBaseHelper extends AbstractService
{

    protected function getGroup($arraySet)
    {
        $arrayGroup = [];
        foreach ($arraySet as $array) {
            $key = $array['parentId'];
            $arrayGroup[$key][] = $array;
        }
        return $arrayGroup;
    }

    protected function isEmpty($record, $error)
    {
        if (empty($record)) {
            throw new ValidationException([
                $error
            ], $error, 'tier_not_found');
        }
    }

    protected function isExists($object, $error, $key)
    {
        if (empty($object)) {
            throw new ValidationException([
                $error
            ], $error, $key);
        }
    }

    protected function validateTier($tierId, $tier_level)
    {
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $tierLevel = ($tier_level == 'primary') ? 1 : 2;
        $tierDetails = $this->tierRepository->findOneBy(array(
            'id' => $tierId,
            'tier' => $tierLevel
        ));
        if (empty($tierDetails)) {
            $this->logger->error( " Multi Campus Bundle - Campus Service Base Helper - Validate Tier " . TierConstant::INVALID_PARENT_TIER . TierConstant::ERROR_TIER_NOT_FOUND_KEY);
            throw new ValidationException([
                TierConstant::INVALID_PARENT_TIER
            ], TierConstant::INVALID_PARENT_TIER, TierConstant::INVALID_PARENT_TIER_KEY);
        }
    }

    protected function validateParentTier($tierId, $campusId)
    {
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $tierDetails = $this->tierRepository->findOneBy(array(
            'id' => $campusId
        ));
        if (! empty($tierDetails) && $tierDetails->getParentOrganizationId() != $tierId) {
            $this->logger->error( " Multi Campus Bundle - Campus Service Base Helper - ValidateParentTier " . TierConstant::INVALID_PARENT_TIER . TierConstant::INVALID_PARENT_TIER_KEY);
            throw new ValidationException([
                TierConstant::INVALID_PARENT_TIER
            ], TierConstant::INVALID_PARENT_TIER, TierConstant::INVALID_PARENT_TIER_KEY);
        }
    }

    protected function isCampus($campusId, $errorMessage = 'Invalid Campus')
    {
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $tierDetails = $this->tierRepository->findOneBy(array(
            'id' => $campusId
        ));
        if ($tierDetails->getTier() != 3) {
            $this->logger->error( " Multi Campus Bundle - Campus Service Base Helper - Is Campus " . $errorMessage . 'invalid_campus_error');
            throw new ValidationException([
                $errorMessage
            ], $errorMessage, 'invalid_campus_error');
        }
    }

    protected function isCampusExists($campus)
    {
        if (! isset($campus)) {
            $this->logger->error( " Multi Campus Bundle - Campus Service Base Helper - Is Campus Exists " . TierConstant::CAMPUS_NOT_EXIST . TierConstant::CAMPUS_NOT_EXIST_ERROR);
            throw new ValidationException([
                TierConstant::CAMPUS_NOT_EXIST
            ], TierConstant::CAMPUS_NOT_EXIST, TierConstant::CAMPUS_NOT_EXIST_ERROR);
        }
    }
}