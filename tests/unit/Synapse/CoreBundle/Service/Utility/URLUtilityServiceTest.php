<?php

namespace Synapse\CoreBundle\Service\Utility;

use Codeception\Specify;
use Codeception\Test\Unit;
use Flow\Exception;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Exception\PhantomJsException;
use Synapse\CoreBundle\Repository\EbiConfigRepository;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\CoreBundle\Service\Impl\EbiConfigService;
use Synapse\CoreBundle\Service\Impl\TokenService;
use Synapse\CoreBundle\Service\Utility\URLUtilityService;


class URLUtilityServiceTest extends Unit
{
    use Specify;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var RepositoryResolver
     */
    private $repositoryResolver;

    /**
     * @var string
     */
    private $systemUrl = "https://mapworks-qa.skyfactor.com";

    /**
     * @var int
     */
    private $personId = 5048809;

    /**
     * @var string
     */
    private $token = "NDUzZTFmZTIwNDUzYjc1ZDYxZjBlNjA1NWJhNGE4Yzg1NWUwMDJmNTA5OTZhZjEwZTVmYWFmYmQxMzJlMzI5Mg";

    public function _before()
    {
        $this->container = $this->getMock('container', ['get']);
        $this->logger = $this->getMock('logger', ['debug', 'error']);
        $this->repositoryResolver = $this->getMock('repositoryResolver', ['getRepository']);
    }

    public function testGenerateURLForMapworks()
    {
        $this->specify("Test Generate URL for Mapworks", function ($pathAfterBaseURL, $queryParameters, $expectedResult) {
            $mockTokenServivce = $this->getMock('TokenService', ['generateToken']);
            $mockEbiConfigService = $this->getMock('EbiConfigService', ['getSystemUrl']);

            $this->container->method('get')
                ->willReturnMap([
                    [
                        TokenService::SERVICE_KEY,
                        $mockTokenServivce
                    ],
                    [
                        EbiConfigService::SERVICE_KEY,
                        $mockEbiConfigService
                    ]
                ]);

            $mockToken = $this->getMock('TokenService', array('getToken'));

            $mockToken->method("getToken")->willReturn($this->token);
            $mockTokenServivce->method('generateToken')->willReturn($mockToken);
            $mockEbiConfigService->method('getSystemUrl')->willReturn($this->systemUrl);
            $urlUtilityServiceTest = new URLUtilityService($this->repositoryResolver, $this->logger, $this->container);
            $result = $urlUtilityServiceTest->generateURLforMapworks($pathAfterBaseURL, $queryParameters, $this->personId);
            verify($result)->equals($expectedResult);
        }, [
            "examples" =>
                [
                    [// Example 1: Test URL with query parameter values
                        '/top-issues/webpage', // Path after base url
                        [// Query Parameters
                            'print' => "pdf",
                        ],
                        $this->systemUrl . '/top-issues/webpage?access_token=' . $this->token . '&print=pdf'// Expected Result
                    ],
                    [// Example 2 : Test URL without query parameter values
                        '/top-issues/webpage', // Path after base url
                        [], // Empty query parameters
                        $this->systemUrl . '/top-issues/webpage?access_token=' . $this->token . '' // Expected Result
                    ]
                ]
        ]);
    }

    public function testValidateURL()
    {
        $this->specify("Test Validate URL", function ($url, $expectedResult) {

            $urlUtilityService = new URLUtilityService($this->repositoryResolver, $this->logger, $this->container);
            $result = $urlUtilityService->validateURL($url);
            $this->assertEquals($expectedResult, $result);
        }, ['examples' => [
            //testcase for valid url
            [
                'http://www.example.com/',
                true
            ],
            //testcase for invalid url
            [
                'https//:google-invalid.com',
                false
            ]
        ]]);
    }

    public function testValidatePhotoURL()
    {
        $this->specify("Test Validate Photo URL", function ($url, $expectedResult) {
            $urlUtilityService = new URLUtilityService($this->repositoryResolver, $this->logger, $this->container);
            $result = $urlUtilityService->validatePhotoURL($url);
            $this->assertEquals($expectedResult, $result);
        }, ['examples' => [
            //testcase for valid photo url
            [
                'http://php.net/manual/en/images/c0d23d2d6769e53e24a1b3136c064577-php_logo.png',
                true
            ],
            //testcase for valid url but invalid photo url
            [
                'https://google.com',
                false
            ],
            //testcase for invalid url
            [
                'https://Invalid-google.com',
                false
            ]
        ]]);
    }

}
