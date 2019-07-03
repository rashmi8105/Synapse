<?php
namespace Synapse\GatewayBundle\Service\Impl;

use GuzzleHttp\Client as HttpClient;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\GatewayBundle\Service\LaunchServiceInterface;

/**
 * @DI\Service("gateway_launch_service")
 */
class LaunchService extends AbstractService implements LaunchServiceInterface
{

    const SERVICE_KEY = 'gateway_launch_service';

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
     * LaunchService constructor.
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "personService" = @DI\Inject("person_service"),
     *            "ebiConfigService" = @DI\Inject("ebi_config_service"),
     *            "tokenService" = @DI\Inject("token_service")
     * })
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
     * Incoming Gateway launch request. Since we don't need anything special (ie: course information)
     * from the gateway, we just ask it to redirect the user.
     *
     * @param  int $personId
     * @return array
     */
    public function createLaunch($personId)
    {
        $this->logger->debug("Create Launch - redirecting to user - " . $personId);
        return [
            'action' => 'Success',
            'redirectURI' => $this->ebiConfigService->get('System_API_URL') . 'api/v1/launch/redirect'
        ];
    }

    /**
     * If the Gateway access token is provided (ie: it actually came from the Gateway)
     * redirect the user to the appropriate endpoint.
     *
     * @param int $personId
     * @param string $accessToken
     * @return string
     */
    public function redirectLaunch($personId, $accessToken)
    {
        if (!$this->validateAccessToken($accessToken)) {
            $this->logger->error("Redirect Launch - Could not find access token");
            throw new AccessDeniedException();
        }

        $person = $this->personService->getPerson($personId);
        $this->logger->debug("Redirect Launch - getting redirect uri for user - " . $personId);
        $roles = explode(',', $person['person_type']);
        $orgId = $person['organization_id'];
        $token = $this->tokenService->generateToken($personId)->getToken();

        // If the user is in any way a student we should always take them to the student dasboard
        // Otherwise take them to the staff dashboard
        if (in_array('Student', $roles)) {
            $url = $this->ebiConfigService->generateCompleteUrl('Gateway_Student_Landing_Page', $orgId);
        } else {
            $url = $this->ebiConfigService->generateCompleteUrl('Gateway_Staff_Landing_Page', $orgId);
        }

        $url .= self::ACCESS_TOKEN . $token;

        return $url;
    }

    /**
     * Make an API request to the Gateway token verification API to make sure the token we were
     * given isn't bogus
     *
     * @param  string $accessToken
     * @return bool
     */
    private function validateAccessToken($accessToken)
    {
        $this->logger->debug("Launch service- validate access token - " . $accessToken);
        $client = new HttpClient();
        $response = $client->get($this->ebiConfigService->get('Gateway_Verify_URL'), [
            'exceptions' => false,
            'query' => [
                'access_token' => $accessToken
            ],
            'auth' => [
                $this->ebiConfigService->get('Gateway_Key'),
                $this->ebiConfigService->get('Gateway_Secret')
            ]
        ]);

        $jsonResponse = json_decode($response->getBody());

        if ($response->getStatusCode() == 200 && !$jsonResponse->error) {
            $this->logger->debug("Launch service- validate access token status - 200 ");
            return true;
        }

        return false;
    }
}
