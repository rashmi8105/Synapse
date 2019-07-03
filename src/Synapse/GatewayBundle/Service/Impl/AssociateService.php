<?php
namespace Synapse\GatewayBundle\Service\Impl;

use GuzzleHttp\Client as HttpClient;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\GatewayBundle\Service\AssociateServiceInterface;

/**
 * @DI\Service("gateway_associate_service")
 */
class AssociateService extends AbstractService implements AssociateServiceInterface
{

    const SERVICE_KEY = 'gateway_associate_service';

    private $securityContext;

    /**
     *
     * @param
     *            $repositoryResolver
     * @param $logger @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "securityContext" = @DI\Inject("security.context"),
     *            "ebiConfigService" = @DI\Inject("ebi_config_service")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $securityContext, $ebiConfigService)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->securityContext = $securityContext;
        $this->ebiConfigService = $ebiConfigService;
    }

    /**
     * Create an association between a Gateway TP user ID and a Mapworks user
     * 
     * @param  string $token
     * @return string|bool
     */
    public function createAssociation($token)
    {
        $this->logger->debug("Create Association - getting user detail from token -" . $token);
        $user = $this->securityContext->getToken()->getUser();

        $client = new HttpClient();
        $response = $client->post($this->ebiConfigService->get('Gateway_Associate_URL'), [
            'exceptions' => false,
            'json' => [
                'associations' => [
                    [
                        'association_token' => $token,
                        'tool_provider_user_id' => $user->getId()
                    ]
                ]
            ],
            'auth' => [
                $this->ebiConfigService->get('Gateway_Key'),
                $this->ebiConfigService->get('Gateway_Secret')
            ]
        ]);
        $this->logger->debug("Create Association - creating association for user -" . $user->getId());
        $jsonResponse = json_decode($response->getBody());
        if ($response->getStatusCode() == 200 && ! $jsonResponse->error) {
            $this->logger->debug("Create Association - Association Status code is 200");
            return $this->ebiConfigService->get('Gateway_Associate_Form_URL');
        }

        return false;
    }
}
