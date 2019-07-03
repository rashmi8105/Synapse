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
use Synapse\CoreBundle\Util\Helper;
use JMS\Serializer\Serializer;

/**
 * @DI\Service("tier_service")
 */
class TierService extends TierHelperService implements TierServiceInterface
{

    const SERVICE_KEY = 'tier_service';

    /**
     *
     * @var TierRepository
     */
    private $tierRepository;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "langService" = @DI\Inject("lang_service"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $langService, $container)
    {
        parent::__construct($repositoryResolver, $logger, $langService, $container);
        $this->langService = $langService;
        $this->container = $container;
    }

    public function find($id)
    {
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $tier = $this->tierRepository->find($id);
        if (! $tier) {
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        return $tier;
    }

    /**
     *
     * @param Tier $tier            
     * @throws InvalidArgumentException
     * @return Tier tier created
     */
    public function createTier(TierDto $tierDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($tierDto);
        $this->logger->debug("Create Primary | Secondary Tier " . $logContent);
        
        return $tier = $tierDto->getTierLevel() == TierConstant::PRIMARY_TIER ? $this->createPrimaryTier($tierDto, $this->langService) : $this->createSecondaryTier($tierDto, $this->langService);
    }

    public function updateTier(TierDto $tierDto)
    {
        $logContent = $this->container->get('loggerhelper_service')->getLog($tierDto);
        $this->logger->debug("Update Primary | Update Tier " . $logContent);
        
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $tierObj = array();
        $tierObj = $this->tierDetailsRepository->findOneBy(array(
            'organization' => $tierDto->getId()
        ));
        if (isset($tierObj)) {
            $tier = ($tierDto->getTierLevel() == TierConstant::PRIMARY_TIER) ? $this->updatePrimaryTier($tierDto) : $this->updateSecondaryTier($tierDto);
        } else {
            throw new ValidationException([
                TierConstant::TIER_NOT_FOUND
            ], TierConstant::ERROR_TIER_NOT_FOUND, TierConstant::ERROR_TIER_NOT_FOUND_KEY);
        }
        $this->logger->info("Updated Primary Tier | Secondary Tier");
        return $tier;
    }

    public function viewTier($tierId, $tierlevel)
    {
        $this->logger->debug("View Primary | View Secondary Tier Having Tier Id - " . $tierId . "Tier Level -" . $tierlevel);
        return $tier = ($tierlevel == TierConstant::PRIMARY_TIER) ? $this->viewPrimaryTier($tierId) : $this->viewSecondaryTier($tierId);
    }

    public function listTier($tierId, $tierlevel)
    {
        $this->logger->debug("List Primary | List Secondary Tier Having Tier Id - " . $tierId . "Tier Level -" . $tierlevel);
        return $tier = ($tierlevel == TierConstant::PRIMARY_TIER) ? $this->listPrimaryTier($tierId = null) : $this->listSecondaryTier($tierId);
    }

    public function deleteSecondaryTier($tierId, $tierlevel)
    {
        $this->logger->debug("Delete Secondary Tier Having Tier Id - " . $tierId . "Tier Level -" . $tierlevel);
        $this->tierRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->tierDetailsRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_DET_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        $tierinfo = $this->tierRepository->findOneBy(array(
            'id' => $tierId,
            'tier' => 2
        ));
        
        if (! empty($tierinfo) && $tierlevel == TierConstant::SECONDARY_TIER) {
            
            $tierdetails = $this->tierDetailsRepository->findOneBy(array(
                'organization' => $tierId
            ));
            
            $campusdetails = $this->tierRepository->findOneBy(array(
                'parentOrganizationId' => $tierId,
                'tier' => '3'
            ));
            
            if (count($campusdetails)) {
                throw new ValidationException([
                    "Tier can not be deleted"
                ], "Tier can not be deleted", 'tier_delete_error');
            } else {
                if (! is_null($tierdetails)) {
                    $this->tierDetailsRepository->remove($tierdetails);
                }
                $this->tierRepository->remove($tierinfo);
                $this->tierRepository->flush();
            }
        }
        $this->logger->info("Deleted Secondary Tier");
    }
}