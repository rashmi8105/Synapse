<?php
namespace Synapse\AuthenticationBundle\Service\Impl;

use Symfony\Component\DependencyInjection\Container;
use Synapse\AuthenticationBundle\Repository\OrgSamlConfigRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\PersonService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\AuthenticationBundle\Exception\MetadataException;
use Synapse\AuthenticationBundle\Exception\UnconfiguredOrganizationException;

/**
 * @DI\Service("saml_auth_service")
 */
class SAMLAuthService extends AbstractService
{

    const SERVICE_KEY = 'saml_auth_service';

    // Scaffolding
    /**
     * @var Container
     */
    private $container;

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

    // Repositories

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var OrgSamlConfigRepository
     */
    private $samlConfigRepository;

    /**
     *
     * @param $repositoryResolver
     * @param $logger @DI\InjectParams({
     *        "repositoryResolver" = @DI\Inject("repository_resolver"),
     *        "logger" = @DI\Inject("logger"),
     *        "container" = @DI\Inject("service_container")
     * })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //Scaffolding
        $this->container = $container;

        //Services
        $this->ebiConfigService = $this->container->get("ebi_config_service");
        $this->personService = $this->container->get("person_service");
        $this->tokenService = $this->container->get("token_service");

        //Repositories
        $this->personRepository = $this->repositoryResolver->getRepository('SynapseCoreBundle:Person');
        $this->samlConfigRepository = $this->repositoryResolver->getRepository('SynapseAuthenticationBundle:OrgSamlConfig');
    }

    public function getMetadata($orgId)
    {
        $settings = new \OneLogin_Saml2_Settings($this->getSettings($orgId), true);
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);
        if ($errors) {
            throw new MetadataException($orgId, $errors);     
        }

        return $metadata;
    }

    /**
     * Returns SSO URL.
     *
     * @param int $orgId
     * @return string
     */
    public function getRedirectURLForSAMLAuthentication($orgId)
    {
        $systemUrl = $this->ebiConfigService->get('System_URL');
        $settings = $this->getSettings($orgId);
        $auth = new \OneLogin_Saml2_Auth($settings);

        if (!isset($_SESSION['samlUserdata'])) {
            $auth->login();
        } else {
            $this->logger->info('SAML USERDATA: ' . json_encode($_SESSION['samlUserdata']));
            $person = $this->personRepository->findOneBy(['username' => $_SESSION['samlUserdata']['emailAddress']]);

            $token = $this->tokenService->generateToken($person->getId())->getToken();
            return $systemUrl . '?access_token=' . $token;
        }
    }

    /**
     * Returns complete login URL for SAML Consume Organization.
     *
     * @param int $orgId
     * @return string
     */
    public function getRedirectURLForSAMLConsume($orgId)
    {
        $this->logger->info('SAML CONSUME ORG: ' . $orgId . ' | DATA: ' . json_encode($_POST));
        $systemUrl = $this->ebiConfigService->get('System_URL');
        $redirect = $systemUrl . '#/login';

        if (isset($_POST['SAMLResponse'])) {
            $this->logger->info('SAML RESPONSE: ' . json_encode($_POST['SAMLResponse']));
            $samlSettings = new \OneLogin_Saml2_Settings($this->getSettings($orgId));
            $samlResponse = new \OneLogin_Saml2_Response($samlSettings, $_POST['SAMLResponse']);
            $username = $samlResponse->getNameId();

            $this->logger->info('SAML VALID?: ' . $samlResponse->isValid());
            file_put_contents('/var/www/synapse-backend/app/logs/saml_auth.log', $username . PHP_EOL, FILE_APPEND);

            if ($username) {
                $this->logger->info('SAML LOGIN FROM: ' . $username);
                $person = $this->personRepository->findOneBy(['username' => $username, 'organization' => $orgId]);
                if ($person) {
                    $token = $this->tokenService->generateToken($person->getId())->getToken();
                    $redirect = $systemUrl . '#/?access_token=' . $token;
                }
            }
        }

        return $redirect;
    }

    /**
     * Returns Url for Single Log out.
     *
     * @param int $orgId
     * @return string
     * @throws \OneLogin_Saml2_Error
     */
    public function getRedirectURLForSAMLLogout($orgId)
    {
        $systemUrl = $this->ebiConfigService->get('System_URL');
        $settings = new \OneLogin_Saml2_Settings($this->getSettings($orgId));
        $idpData = $settings->getIdPData();

        if (isset($idpData['singleLogoutService']) && isset($idpData['singleLogoutService']['url'])) {
            $sloUrl = $idpData['singleLogoutService']['url'];
        } else {
            return $systemUrl . '#/login';
        }

        if (isset($_SESSION['IdPSessionIndex']) && !empty($_SESSION['IdPSessionIndex'])) {
            $logoutRequest = new \OneLogin_Saml2_LogoutRequest($settings, null, $_SESSION['IdPSessionIndex']);
        } else {
            $logoutRequest = new \OneLogin_Saml2_LogoutRequest($settings);
        }
        
        $samlRequest = $logoutRequest->getRequest();
        $parameters = array('SAMLRequest' => $samlRequest);
        $url = \OneLogin_Saml2_Utils::redirect($sloUrl, $parameters, true);
        return $url;
    }

    private function getSettings($organization)
    {
        $samlConfig = $this->samlConfigRepository->findOneByOrganization($organization);

        if (!$samlConfig) {
            throw new UnconfiguredOrganizationException($organization);
        }

        $spBaseUrl = $this->ebiConfigService->get('System_API_URL');

        $settings = [
            'strict' => true,
            'debug' => false,
            'sp' => [
                'entityId' => $spBaseUrl . 'api/v1/saml/sso',
                'assertionConsumerService' => [
                    'url' => $spBaseUrl . 'api/v1/saml/consume/' . $organization,
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                ],
                'singleLogoutService' => [
                    'url' => $spBaseUrl . 'api/v1/saml/slo/' . $organization,
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ],
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
                'x509cert' => $this->ebiConfigService->get('SP_Public_Key'),
                'privateKey' => $this->ebiConfigService->get('SP_Private_Key'),
            ],
            'idp' => [
                'entityId' => $samlConfig->getEntityId(),
                'singleSignOnService' => [
                    'url' => $samlConfig->getSsoUrl(),
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ],
                'singleLogoutService' => [
                    'url' => $samlConfig->getLogoutUrl(),
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ],
                'x509cert' => $samlConfig->getPublicKeyFile(),
            ],
            'security' => [
                'requestedAuthnContext' => [
                    'urn:oasis:names:tc:SAML:2.0:ac:classes:unspecified',
                    'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport',
                    'urn:oasis:names:tc:SAML:2.0:ac:classes:Password',
                    'urn:oasis:names:tc:SAML:2.0:ac:classes:Kerberos'
                ]
            ]
        ];

        if ($samlConfig->getSettingsOverride()) {
            $overrides = json_decode($samlConfig->getSettingsOverride(),TRUE);
            $settings = array_merge($settings, $overrides);
        }

        return $settings;
    }
}
