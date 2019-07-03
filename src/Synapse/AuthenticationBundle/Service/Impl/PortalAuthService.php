<?php
namespace Synapse\AuthenticationBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\Util\Helper;

/**
 * @DI\Service("portal_auth_service")
 */
class PortalAuthService extends AbstractService
{

    const SERVICE_KEY = 'portal_auth_service';

    const ACCESS_TOKEN = '?access_token=';


    // Services

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var PersonService
     */
    private $personService;

    /**
     * @var TokenService
     */
    private $tokenService;

    /**
     * PortalAuthService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "personService" = @DI\Inject("person_service"),
     *            "ebiConfigService" = @DI\Inject("ebi_config_service"),
     *            "tokenService" = @DI\Inject("token_service")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $personService
     * @param $ebiConfigService
     * @param $tokenService
     */
    public function __construct($repositoryResolver, $logger, $personService, $ebiConfigService, $tokenService)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);

        // Services
        $this->ebiConfigService = $ebiConfigService;
        $this->personService = $personService;
        $this->tokenService = $tokenService;
    }


    /**
     * get the redirect url to the user dashboard based on organization
     *
     * @param string $orgToken
     * @param string $personToken
     * @return bool|string
     */
    public function getAuth($orgToken, $personToken)
    {
        try {
            $orgId = Helper::decrypt(base64_decode($orgToken));
            $personData = explode('::', Helper::decrypt(base64_decode($personToken)));
            $externalId = $personData[0];
            $intent = $personData[1];

            $personObj = $this->personService->findOneByExternalIdOrg($externalId, $orgId);

            if ($personObj) {
                $personId = $personObj->getId();

                $token = $this->tokenService->generateToken($personId)->getToken();

                switch ($intent) {
                    case 'student':
                        $url = $this->ebiConfigService->generateCompleteUrl('Gateway_Student_Landing_Page', $orgId) . self::ACCESS_TOKEN . $token;
                        break;

                    case 'faculty':
                        $url = $this->ebiConfigService->generateCompleteUrl('Gateway_Staff_Landing_Page', $orgId) . self::ACCESS_TOKEN . $token;
                        break;

                    default:
                        $url = false;
                }
            } else {
                $url = false;
            }

            return $url;

        } catch (\Exception $e) {
            return false;
        }
    }
}