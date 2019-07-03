<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\OrganizationRepository;

/**
 * @DI\Service("ebi_config_service")
 */
class EbiConfigService extends AbstractService
{
    const SERVICE_KEY = 'ebi_config_service';

    /**
     * @var Container
     */
    private $container;

    /**
     *
     * @var EbiConfigRepository
     */
    private $ebiConfigRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        $this->ebiConfigRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:EbiConfig");
        $this->organizationRepository = $this->repositoryResolver->getRepository("SynapseCoreBundle:Organization");
    }

    public function get($key)
    {
        $this->logger->debug(">>>>Get Key" . $key);
        $configObj = $this->ebiConfigRepository->findOneByKey($key);
        if ($configObj) {
            return $configObj->getValue();
        }
        return "";
    }

    /**
     * Gets the full system URL for an organization depending on whether or not that organization has LDAP/SAML enabled.
     *
     * @param int|null $organizationId
     * @return string $systemUrl
     */
    public function getSystemUrl($organizationId = null)
    {
        $systemUrlConfigEntry = $this->ebiConfigRepository->findOneBy(['key' => 'System_URL']);
        $systemUrl = $systemUrlConfigEntry->getValue();

        if ($organizationId) {
            $organization = $this->organizationRepository->find($organizationId);

            if ($organization) {
                if ($organization->getIsLdapSamlEnabled()) {
                    $requestProtocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                    $environmentKernelParameter = $this->container->getParameter('kernel.environment');

                    if (!$environmentKernelParameter || $environmentKernelParameter == 'prod') {
                        $urlPartForEnvironment = '';
                    } else {
                        $urlPartForEnvironment = '-' . $environmentKernelParameter;
                    }
                    
                    $systemUrl = $requestProtocol . '://' . $organization->getSubdomain() . $urlPartForEnvironment . '.skyfactor.com/';
                }
            }
        }

        return $systemUrl;
    }

    /**
     * takes the config key and organizationId as input and returns the complete url
     *
     * @param string $key
     * @param int|null $organizationId
     * @return string
     */
    public function generateCompleteUrl($key, $organizationId = null)
    {
        $relativeUrl = $this->get($key);
        $systemUrl = $this->getSystemUrl($organizationId);
        return $systemUrl . $relativeUrl;
    }
}
