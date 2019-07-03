<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\ProxyServiceInterface;
use Synapse\RestBundle\Entity\ProxyDto;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Entity\ProxyLog;
use Synapse\CoreBundle\Util\Constants\ProxyConstant;
use Synapse\CoreBundle\Util\Helper;

/**
 * @DI\Service("proxy_service")
 */
class ProxyService extends AbstractService implements ProxyServiceInterface
{

    const SERVICE_KEY = 'proxy_service';

    /**
     *
     * @var container
     */
    private $container;

    private $proxyLogRep;

    private $personRep;

    private $ebiUserRep;

    private $organizationRepo;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            
     *            
     *            "container" = @DI\Inject("service_container")
     *            
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        
        $this->container = $container;
    }

    /**
     * Create Proxy Link
     *
     * @param ProxyDto $proxyDto            
     * @return void|\Synapse\RestBundle\Entity\Error
     */
    public function createProxy(ProxyDto $proxyDto)
    {
        $this->logger->debug(ProxyConstant::PROXY_ESPRJ_1817);
        $this->organizationRepo = $this->repositoryResolver->getRepository(ProxyConstant::ORG_REPO);
        $this->personRep = $this->repositoryResolver->getRepository(ProxyConstant::PERSON_REPO);
        $this->proxyLogRep = $this->repositoryResolver->getRepository(ProxyConstant::PROXY_LOG_REPO);
        
        $organization = $this->validateOrganization($proxyDto->getCampusId());
        $this->checkLoggedInUserAccess($proxyDto->getUserId(), $proxyDto->getCampusId());
        $person = $this->validatePerson($proxyDto->getProxyUserId());
        $personId = $this->validatePerson($proxyDto->getUserId());
        // $this->checkUserAlreadyProxied($proxyDto->getUserId());
        
        try {
            $proxy = new ProxyLog();
            $proxy->setOrganizationId($organization);
            // $proxy->setEbiUsersId($ebiUser);
            $proxy->setPersonId($personId);
            $proxy->setPersonIdProxiedFor($person);
            $this->validateEntity($proxy);
            $this->proxyLogRep->create($proxy);
            $this->proxyLogRep->flush();
            $proxyDto->setId($proxy->getId());
            
            return $proxyDto;
        } catch (ValidationException $valExp) {
            throw $valExp;
        } catch (\Exception $e) {
            $this->logger->error( "Proxy Service - createProxy - " . ProxyConstant::PROXY_DB_EXCEPTION . " " . ProxyConstant::PROXY_DB_EXCEPTION);
            throw new ValidationException([
                ProxyConstant::PROXY_DB_EXCEPTION . $e->getMessage()
            ], ProxyConstant::PROXY_DB_EXCEPTION, ProxyConstant::PROXY_DB_EXCEPTION_CODE);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Synapse\CoreBundle\Service\ProxyServiceInterface::deleteProxy()
     */
    public function deleteProxy($userId, $proxiedUserId)
    {
        $this->logger->debug(ProxyConstant::PROXY_ESPRJ_1817);
        $this->personRep = $this->repositoryResolver->getRepository(ProxyConstant::PERSON_REPO);
        $this->proxyLogRep = $this->repositoryResolver->getRepository(ProxyConstant::PROXY_LOG_REPO);
        
        $user = $this->validatePerson($userId);
        $this->checkLoggedInUserAccess($userId, $user->getOrganization());
        $proxyUser = $this->validatePerson($proxiedUserId);
        
        $delProxy = $this->proxyLogRep->findOneBy([
            ProxyConstant::FIELD_PERSON_ID => $user,
            ProxyConstant::FIELD_PERSON_ID_PROXIED_FOR => $proxyUser
        ]);
        if (! $delProxy) {
            throw new ValidationException([
                ProxyConstant::PROXY_RECORD_NOT_FOUND
            ], ProxyConstant::PROXY_RECORD_NOT_FOUND, ProxyConstant::PROXY_RECORD_NOT_FOUND_CODE);
        }
        $this->proxyLogRep->remove($delProxy);
        $this->proxyLogRep->flush();
        return;
    }

    /**
     * Check $ebiUserId is available in Person Table
     */
    private function checkUserAlreadyProxied($userId)
    {
        $this->proxyLogRep = $this->repositoryResolver->getRepository(ProxyConstant::PROXY_LOG_REPO);
        $checkUser = $this->proxyLogRep->findBy(array(
            ProxyConstant::FIELD_PERSON_ID => $userId
        ));
        if ($checkUser) {
            $this->logger->error("Proxy Service - checkUserAlreadyProxied - " . ProxyConstant::USER_ALREADY_PROXIED . " " . ProxyConstant::USER_ALREADY_PROXIED_KEY);
            throw new ValidationException([
                ProxyConstant::USER_ALREADY_PROXIED
            ], ProxyConstant::USER_ALREADY_PROXIED, ProxyConstant::USER_ALREADY_PROXIED_KEY);
        }
        return;
    }

    /**
     * Check $orgId is available in Organization Table
     */
    private function validateOrganization($orgId)
    {
        $this->organizationRepo = $this->repositoryResolver->getRepository(ProxyConstant::ORG_REPO);
        $organization = $this->organizationRepo->find($orgId);
        if (! $organization) {
            $this->logger->error( "Proxy Service - Validate Organization - " . ProxyConstant::ORGANIZATION_NOT_FOUND . " " . ProxyConstant::ORGANIZATION_NOT_FOUND_CODE);
            throw new ValidationException([
                ProxyConstant::ORGANIZATION_NOT_FOUND
            ], ProxyConstant::ORGANIZATION_NOT_FOUND, ProxyConstant::ORGANIZATION_NOT_FOUND_CODE);
        }
        return $organization;
    }

    /**
     * Check $userId is available in Person Table
     */
    private function validatePerson($userId)
    {
        $this->personRepository = $this->repositoryResolver->getRepository(ProxyConstant::PERSON_REPO);
        $person = $this->personRepository->find($userId);
        if (! $person) {
            $this->logger->error( "Proxy Service - Validate Person - " . ProxyConstant::ERROR_PERSON_NOT_FOUND . " " . ProxyConstant::ERROR_PERSON_NOT_FOUND_KEY);
            throw new ValidationException([
                ProxyConstant::ERROR_PERSON_NOT_FOUND
            ], ProxyConstant::ERROR_PERSON_NOT_FOUND, ProxyConstant::ERROR_PERSON_NOT_FOUND_KEY);
        }
        return $person;
    }

    /**
     * Check $ebiUserId is available in EbiUser Table
     */
    
    /*
     * private function validateEbiUser($ebiUserId) { $this->ebiUserRep = $this->repositoryResolver->getRepository(ProxyConstant::EBI_USER_REPO); $ebiUser = $this->ebiUserRep->find($ebiUserId); if (! $ebiUser) { throw new ValidationException([ ProxyConstant::EBI_USER_NOT_FOUND ], ProxyConstant::EBI_USER_NOT_FOUND, ProxyConstant::EBI_USER_NOT_FOUND_KEY); } return $ebiUser; }
     */
    
    /**
     * Check $ebiUserId is available in Person Table
     */
    private function checkLoggedInUserAccess($userId, $campusId)
    {
        $this->personRep = $this->repositoryResolver->getRepository(ProxyConstant::PERSON_REPO);
        $this->orgRoleRepo = $this->repositoryResolver->getRepository(ProxyConstant::ORG_ROLE_REPO);
        
        //$isCoordinator = $this->orgRoleRepo->getUserCoordinatorRole($campusId, $userId);
        // check if user is primary Coordinator
        $isCoordinator = $this->orgRoleRepo->findOneBy(array(
            'organization' => $campusId,
            'person' => $userId,
            'role' => array(1, 2, 3)
        ));
        
        $isEbiUser = $this->personRep->findOneBy(array(
            ProxyConstant::ID => $userId,
            'organization' => '-1'
        ));
        if (! $isEbiUser && ! $isCoordinator) {
            $this->logger->error( "Proxy Service - checkLoggedInUserAccess - " . ProxyConstant::USER_ACCESS_DENIED . " " . ProxyConstant::USER_ACCESS_DENIED_KEY);
            throw new ValidationException([
                ProxyConstant::USER_ACCESS_DENIED
            ], ProxyConstant::USER_ACCESS_DENIED, ProxyConstant::USER_ACCESS_DENIED_KEY);
        }
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
            $this->logger->error( "Proxy Service - validateEntity - " . $errorsString);
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'entity_validation');
            return;
        }
    }
}