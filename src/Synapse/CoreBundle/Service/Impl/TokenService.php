<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use OAuth2\OAuth2AuthenticateException;
use Synapse\CoreBundle\Entity\AccessToken;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\RefreshToken;
use Synapse\CoreBundle\Exception\SynapseValidationException;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\CoreBundle\Repository\ClientRepository;
use Synapse\CoreBundle\Repository\AccessTokenRepository;
use Doctrine\ORM\EntityManager;

/**
 * @DI\Service("token_service")
 */
class TokenService extends AbstractService
{

    const SERVICE_KEY = 'token_service';

    // Scaffolding

    /**
     * @var entityManager
     */
    private $entityManager;



    // Services

    /**
     * @var PersonService
     */
    private $personService;


    // Repositories

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var AccessTokenRepository
     */
    private $accessTokenRepository;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "personService" = @DI\Inject("person_service"),
     *            "doctrine" = @DI\Inject("doctrine")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $personService, $doctrine)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->entityManager = $doctrine->getmanager();

        // Service changes
        $this->personService = $personService;

        // Uses parent repositoryResolver
        $this->accessTokenRepository = $this->repositoryResolver->getRepository(AccessTokenRepository::REPOSITORY_KEY);
        $this->clientRepository = $this->repositoryResolver->getRepository(ClientRepository::REPOSITORY_KEY);

    }

    public function generateToken($personId, $emailAuthType = false)
    {
        $this->logger->debug("Generate Token for Person Id " . $personId . "Email Auty Type" . $emailAuthType);
        $tokenString = substr(base64_encode(hash('sha256', microtime(true) . $personId)), 0, - 2);
        $client = $this->clientRepository->findOneById(1);

        /**
         *
         * @var Person $person
         */
        $person = $this->personService->find($personId);

        // Get the Inactivity Timeout setting...
        $inactivityTimeout = $person->getOrganization()->getInactivityTimeout() * 60;
        $inactivityTimeout =  ($inactivityTimeout == 0 ? 3600 : $inactivityTimeout);

        $accessToken = new AccessToken();
        $accessToken->setToken($tokenString);
        $accessToken->setClient($client);
        $accessToken->setUser($person);
        if (! $emailAuthType) {
            $accessToken->setExpiresAt(time() + $inactivityTimeout);
        } else {
            $accessToken->setExpiresAt(0);
        }
        $accessToken->setScope('user');
        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();

        $this->logger->info("Generate Token for Person Id and Email Auth Type ");
        return $accessToken;
    }

    /**
     * Sets the ExpiresAt variable for a token.
     *
     * @param $personId
     * @param $token
     */
	public function expireToken($personId, $token)
	{
		$tokenObject = $this->accessTokenRepository->findOneBy(['token' => $token, 'user' => $personId], new SynapseValidationException('Token not Found'));
		$tokenObject->setExpiresAt(-1);
		$this->accessTokenRepository->flush();
	}
	
	public function regenerateToken($apiToken,$userId){
	    
	    $this->accessTokenRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:AccessToken");
	    $tokenObject = $this->accessTokenRepository->findOneBy(['token' => $apiToken, 'user' => $userId]);
	    
	    $client = $tokenObject->getClient();
	    $accessToken = $this->generateToken($userId);
	    $person = $this->personService->find($userId);
	    
	    $inactivityTimeout = $person->getOrganization()->getInactivityTimeout() * 60;
	    $inactivityTimeout =  ($inactivityTimeout == 0 ? 3600 : $inactivityTimeout);
	    
	    /*
	     * Generating refreshToken
	    */
	    $refreshTokenString = substr(base64_encode(hash('sha256', microtime(true) . $userId)), 0, - 2);
	    $refreshToken = new RefreshToken();
	    $refreshToken->setToken($refreshTokenString);
	    $refreshToken->setClient($client);
	    $refreshToken->setUser($person);
	    $refreshToken->setExpiresAt(time() + $inactivityTimeout);
	    $refreshToken->setScope('user');
	    $this->entityManager->persist($refreshToken);
	    $this->entityManager->flush();
	    /*
	     * End of Generating refreshToken
	     * 
	    */
	    
	    /*
	     * Expire the old token
	     * 
	     */
        $this->expireToken($userId, $apiToken);
	    
	    $finalArr = array(
	        'access_token' => $accessToken->getToken(),
	        'expires_in' => $inactivityTimeout ,
	        'token_type' => "bearer",
	        'scope' => "user",
	        'refresh_token'=>$refreshTokenString
	    );
	    return $finalArr;
	}


    /**
     * Validate client Ids  used to login from web app and webapp admin
     *
     * @param string $clientId
     * @param integer $organizationId
     * @return void
     * @throws OAuth2AuthenticateException
     */
    public function validateClientIds($clientId, $organizationId)
    {
        $clientArray = explode("_", $clientId);
        $clientId = $clientArray[1];

        switch ($clientId) {

            case SynapseConstant::WEB_APP:
                if ($organizationId < 0) {
                    throw new OAuth2AuthenticateException(400, 'password', 'realm', "invalid_grant");
                }
                break;
            case SynapseConstant::WEB_APP_ADMIN:
                if ($organizationId != -1) {
                    throw new OAuth2AuthenticateException(400, 'password', 'realm', "invalid_grant");
                }
                break;
            case SynapseConstant::ART_DATA_POPULATION:
                if ($organizationId != -2) {
                    throw new OAuth2AuthenticateException(400, 'password', 'realm', "invalid_grant");
                }
                break;
            default:
                throw new OAuth2AuthenticateException(400, 'password', 'realm', "invalid_grant");
                break;
        }
    }
	
}