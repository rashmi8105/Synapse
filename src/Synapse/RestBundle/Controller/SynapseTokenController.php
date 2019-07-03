<?php
namespace Synapse\RestBundle\Controller;

use FOS\OAuthServerBundle\Propel\Token;
use GuzzleHttp\json_decode;
use JMS\DiExtraBundle\Annotation as DI;
use Monolog\Logger;
use OAuth2\OAuth2;
use OAuth2\OAuth2AuthenticateException;
use Snc\RedisBundle\Doctrine\Cache\RedisCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Synapse\AuthenticationBundle\Service\Impl\AuthConfigService;
use Synapse\AuthenticationBundle\Service\Impl\LDAPAuthService;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\UnauthorizedException;
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Synapse\CoreBundle\Service\Impl\AuthCodeService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\SynapseConstant;
use Synapse\UploadBundle\Util\Constants\UploadConstant;

/**
 * Class SynapseTokenController
 *
 * @package Synapse\RestBundle\Controller
 *
 */
class SynapseTokenController
{
    //scaffolding


    /**
     * @var Manager
     *
     * @DI\Inject(Manager::SERVICE_KEY)
     */
    private $rbacManager;


    //services

    /**
     * @var AuthCodeService
     *
     *      @DI\Inject(AuthCodeService::SERVICE_KEY)
     */
    private $authCodeService;

    /**
     * @var AuthConfigService
     *
     *      @DI\Inject(AuthConfigService::SERVICE_KEY)
     */
    private $authConfigService;

    /**
     * @var RedisCache
     *
     *      @DI\Inject(SynapseConstant::REDIS_CLASS_KEY)
     */
    private $cache;

    /**
     * @var LDAPAuthService
     *
     *      @DI\Inject(LDAPAuthService::SERVICE_KEY)
     */
    private $ldapAuthService;

    /**
     * @var OAuth2
     *
     *      @DI\Inject(SynapseConstant::OAUTH_SERVICE_KEY)
     */
    private $oAuthService;


    /**
     * @var PersonService
     *
     *      @DI\Inject(PersonService::SERVICE_KEY)
     */
    private $personService;

    /**
     * @var TokenService
     *
     *      @DI\Inject(TokenService::SERVICE_KEY)
     */
    private $tokenService;


    // class variables


    /**
     * @var array
     */
    private $inputData;

    /**
     * @var Organization
     */
    private $organization;

    /**
     * @var Person
     */
    private $person;


    public static $cacheKeys = [
        'resource_ids-profile',
        'resource_ids-surveyquestion',
        'resource_ids-surveyfactor',
        'resource_ids-isp',
        'resource_ids-isq',
        'resource_ids-questionbank',
        'campus_ids'
    ];


    /**
     * Used for generating access token for the api's to be used
     *
     * resource = true,
     * description = "Generate Api Access Token",
     * section = "Synapse Token",
     * statusCodes = {
     *                  200 = "Request was successful. Representation of Resource(s) was returned",
     *                  400 = "Validation error has occurred",
     *                  403 = "Access Denied",
     *                  404 = "Not Found",
     *                  500 = "Internal server error",
     *                  504 = "Request has timed out"
     * },
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function tokenAction(Request $request)
    {

        if ($request->getMethod() === 'POST') {
            $this->inputData = $request->request->all();
        } else {
            $this->inputData = $request->query->all();
        }

        $this->person = $this->personService->getPersonFromAuthenticationVariables($this->inputData);
        $personId = $this->person->getId();
        $this->organization = $this->person->getOrganization();
        $organizationId = $this->person->getOrganization()->getId();

        $inactivityTimeout = $this->organization->getInactivityTimeout();
        if (is_null($inactivityTimeout)) {
            $inactivityTimeout = SynapseConstant::DEFAULT_INACTIVITY_TIMEOUT;
        } else {
            $inactivityTimeout = $inactivityTimeout * 60; // Inactivity time in database is stored in minutes , converting it to seconds
        }

        $this->oAuthService->setVariable(OAuth2::CONFIG_ACCESS_LIFETIME, $inactivityTimeout);
        $this->oAuthService->setVariable(OAuth2::CONFIG_ENFORCE_INPUT_REDIRECT, false);
        // check if the proper client id is used for logging in through Web app or web app admin

        if ($this->inputData['grant_type'] == "password") {
            $this->tokenService->validateClientIds($this->inputData['client_id'], $organizationId); // if the login method is password , check if proper client ids are used "
        }

        // Get Ldap Configurations

        if ($this->person && $this->organization) {
            $authConfigObject = $this->authConfigService->getAuthConfigForOrganization($organizationId);
            if ($authConfigObject && ($authConfigObject->getLdapStudentEnabled() || $authConfigObject->getLdapStaffEnabled())) {
                $personRoles = $this->personService->getPerson($personId);
                $roles = explode(',', $personRoles['person_type']);
                if (extension_loaded('newrelic')) {
                    newrelic_ignore_transaction();
                }
                $this->ldapAuthService->ldapAuth($this->person, $roles, $this->inputData);
            }
        }

        // @todo - figure out what is this used for
        if ($this->person) {
            $requestData = $request->request->all();
            $requestData['username'] = $this->person->getUsername();
            $request = $request->duplicate(null, $requestData);
        }

        try {
            $authorizedTokenArray = $this->oAuthService->grantAccessToken($request);
            if ($this->inputData['grant_type'] == "authorization_code") {
                $this->authCodeService->reInstateAuthorizationCode($this->inputData['code']);
            }
        } catch (\Exception $e) {
            throw new UnauthorizedException('Invalid Authentication', "Invalid user name or password", 'invalid_grant', 401);
        }

        if ($this->inputData['grant_type'] == 'password' && ($authContent = $authorizedTokenArray->getContent())) {
            $tokenData = json_decode($authContent);
            if ($tokenData->access_token && $personId) {
                // if user is logged in successfully , refresh the cache
                $this->rbacManager->refreshPermissionCache($personId);
                $this->removeCachedData();
            }
        }

        return $authorizedTokenArray;
    }

    /**
     * Method to clean up the Redis cache keys
     */
    private function removeCachedData()
    {
        foreach (self::$cacheKeys as $cacheKey) {
            $this->cache->delete($cacheKey);
        }
    }

}