<?php
namespace Synapse\CoreBundle\Service\Impl;

use FOS\OAuthServerBundle\Util\Random;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\Repository\AuthCodeRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\RestBundle\Entity\AuthCodeDto;


/**
 * @DI\Service("auth_code_service")
 */
class AuthCodeService extends AbstractService{


    const SERVICE_KEY = "auth_code_service";

    //scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var RepositoryResolver
     */
    protected $repositoryResolver;


    // Repositories

    /**
     * @var AuthCodeRepository
     */
    private $authCodeRepository;


    /**
     * AuthCodeService Constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "container" = @DI\Inject("service_container"),
     *            "logger" = @DI\Inject("logger")
     *            })
     *
     * @param $repositoryResolver
     * @param $container
     * @param $logger
     */
    public function __construct($repositoryResolver, $container, $logger)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        //Repositories
        $this->authCodeRepository = $this->repositoryResolver->getRepository(AuthCodeRepository::REPOSITORY_KEY);
    }

    /**
     * Regenerating a new authorization code for a service account
     *
     * @param AuthCodeDto $authCodeDto
     * @param integer $coordinatorOrganizationId
     * @return AuthCodeDto
     * @throws AccessDeniedException
     */
    public function reGenerateAuthorizationCode(AuthCodeDto $authCodeDto, $coordinatorOrganizationId)
    {
        $personId = $authCodeDto->getPersonId();
        $organizationId = $authCodeDto->getOrganizationId();
        if ($coordinatorOrganizationId != $organizationId) {
            throw new AccessDeniedException();
        } else {
            $authCodeObject = $this->authCodeRepository->findOneBy([
                'user' => $personId,
                'organization' => $organizationId
            ]);
            // generate New AuthToken
            if (!$authCodeObject) {
                throw new SynapseValidationException("No existing authorization code found for the user");
            } else {
                $newAuthToken = Random::generateToken();
                $authCodeObject->setToken($newAuthToken);
                $this->authCodeRepository->flush();
                $authCodeDto->setClientId($authCodeObject->getClientId());
                $authCodeDto->setClientSecret($authCodeObject->getClientId() . "_" . $authCodeObject->getClient()->getRandomId());
                $authCodeDto->setClientSecret($authCodeObject->getClient()->getSecret());
                $authCodeDto->setAuthCode($newAuthToken);
                return $authCodeDto;
            }
        }
    }


    /**
     * Reinstating the authorization code once it is marked deleted after generating the accessToken
     *
     * @param string $authorizationCode
     * @return void
     */
    public function reInstateAuthorizationCode($authorizationCode){

        $authCodeObject = $this->authCodeRepository->findOneByIncludingDeletedRecords(['token' => $authorizationCode]);

        if($authCodeObject){
            $authObjectClone = clone $authCodeObject; // create a clone of the Authorization Code object
            $token  = $authCodeObject->getToken();
            $renameToken = $token."_".time()."_expired";
            $authCodeObject->setToken($renameToken); // Rename the expired token with , so that we can create a entry in the AuthCode Table with the same Authorization Code
            $this->authCodeRepository->flush();
            $authObjectClone->setId(null);
            $authObjectClone->setDeletedAt(null);
            $this->authCodeRepository->persist($authObjectClone); // Creating the same authorization code again.
        }
    }

}