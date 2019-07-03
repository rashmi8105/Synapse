<?php

namespace Synapse\CoreBundle\Service\Utility;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\AccessDeniedException;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\TokenService;


/**
 * @DI\Service("url_utility_service")
 */
class URLUtilityService extends AbstractService
{
    const SERVICE_KEY = 'url_utility_service';

    // Scaffolding

    /**
     * @var Container
     */
    private $container;

    /**
     * @var EbiConfigService
     */
    private $ebiConfigService;

    /**
     * @var TokenService
     */
    private $tokenService;

    /**
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        // Scaffolding
        parent::__construct($repositoryResolver, $logger);
        $this->container = $container;

        // Services
        $this->ebiConfigService = $this->container->get(EbiConfigService::SERVICE_KEY);
        $this->tokenService = $this->container->get(TokenService::SERVICE_KEY);
    }

    /**
     * Validates the passed in string to determine if it's a valid image URL.
     *
     * @param string $imageUrl
     * @return bool
     */
    public function validatePhotoURL($imageUrl)
    {
        $isValidURL = $this->validateURL($imageUrl);
        if ($isValidURL) {
            $image = getimagesize($imageUrl);
            if (strpos($image['mime'], 'image') !== false) {
                $isValidImageURL = true;
            } else {
                $isValidImageURL = false;
            }
        } else {
            $isValidImageURL = false;
        }
        return $isValidImageURL;
    }

    /**
     * Validates the passed in string to determine if it's a valid URL.
     *
     * @param string $url
     * @return bool
     */
    public function validateURL($url)
    {
        try {
            $headers = get_headers($url);
            $httpStatusCode = substr($headers[0], 9, 3);
            if (!empty($headers) && $httpStatusCode == 200) {
                $isValidURL = true;
            } else {
                $isValidURL = false;
            }
        } catch (\Exception $e) {
            $isValidURL = false;
        }
        return $isValidURL;
    }

    /**
     * Builds a Mapworks URL With Generated Access Token
     * Used Primarily for PDF
     * Also used from Crontab Jobs that generate PDFs
     *
     * @param string $pathAfterBaseURL
     * @param array $queryParameters
     * @param int $personId
     * @return string
     */
    public function generateURLforMapworks($pathAfterBaseURL, $queryParameters, $personId)
    {

        $token = $this->tokenService->generateToken($personId)->getToken();

        //No organization should be handed to getSystemUrl as this is NOT used with LDAP/SAML and does not require special routing
        //The default parameters should get the unaltered skyfactor base system URL
        $systemUrl = $this->ebiConfigService->getSystemUrl();

        // Handling additional query parameters to be sent for pdf generation if they happen to be present
        $queryParameterString = '';
        foreach ($queryParameters as $queryParameter => $parameterValue) {
            $queryParameterString .= "&$queryParameter=$parameterValue";
        }

        $generatedURL = $systemUrl . $pathAfterBaseURL . '?access_token=' . $token . $queryParameterString;

        return $generatedURL;
    }
}