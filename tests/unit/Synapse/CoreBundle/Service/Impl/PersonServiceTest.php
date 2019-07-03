<?php

namespace Synapse\CoreBundle\Service\Impl;


use Synapse\AcademicBundle\Service\Impl\AcademicYearService;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Repository\AuthCodeRepository;
use Synapse\CoreBundle\Repository\ClientRepository;
use Synapse\CoreBundle\Repository\OrganizationRoleRepository;
use Synapse\CoreBundle\Repository\PersonRepository;
use Synapse\CoreBundle\Repository\RefreshTokenRepository;

class PersonServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;

    public function testGetPersonFromAuthenticationVariables()
    {

        $this->specify("getPerson Object from differt authentication variables", function ($inputData, $returnPerson, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', array(
                'getRepository'
            ));
            $mockLogger = $this->getMock('Logger', array(
                'debug',
                'error',
                'info'
            ));

            $mockContainer = $this->getMock('Container', array(
                'get'
            ));

            $mockRefreshToken = $this->getMock('RefreshToken', ['getUser']);
            $mockAuthCode = $this->getMock('AuthCode', ['getUser']);
            $mockClient = $this->getMock('Client', ['getPerson']);

            $mockPerson = $this->getMock('Person', ['getId', 'getOrganization']);
            $mockOrganization = $this->getMock('Organization', ['getId']);
            $mockRefreshTokenRepository = $this->getMock("RefreshTokenRepository", ['findOneBy']);
            $mockPersonRepository = $this->getMock("PersonRepository", ['findOneBy']);
            $mockAuthCodeRepository = $this->getMock("AuthCodeRepository", ['findOneBy']);
            $mockClientRepository = $this->getMock('ClientRepository', ['findOneBy']);


            if ($returnPerson) {
                $mockRefreshTokenRepository->method('findOneBy')->willReturn($mockRefreshToken);
                $mockPersonRepository->method('findOneBy')->willReturn($mockPerson);
                $mockAuthCodeRepository->method('findOneBy')->willReturn($mockAuthCode);
                $mockClientRepository->method('findOneBy')->willReturn($mockClient);

                $mockRefreshToken->method('getUser')->willReturn($mockPerson);
                $mockAuthCode->method('getUser')->willReturn($mockPerson);
                $mockClient->method('getPerson')->willReturn($mockPerson);

                $mockOrganization->method('getId')->willReturn(1);
                $mockPerson->method('getId')->willReturn(1);
                $mockPerson->method('getOrganization')->willReturn($mockOrganization);
            } else {
                $mockRefreshTokenRepository->method('findOneBy')->willReturn(null);
                $mockPersonRepository->method('findOneBy')->willReturn(null);
                $mockAuthCodeRepository->method('findOneBy')->willReturn(null);
                $mockClientRepository->method('findOneBy')->willReturn(null);

                $mockRefreshToken->method('getUser')->willReturn(null);
                $mockAuthCode->method('getUser')->willReturn(null);
                $mockClient->method('getPerson')->willReturn(null);
            }

            $mockContainer->method('get')->willReturn(function ($serviceKey) {

                $mockOrganziationLangService = $this->getMock("OrganizationLangService", []);
                switch ($serviceKey) {
                    case OrganizationlangService::SERVICE_KEY:
                        return $mockOrganziationLangService;
                        break;
                    default:
                        return 1;
                        break;
                }

            });
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [RefreshTokenRepository::REPOSITORY_KEY, $mockRefreshTokenRepository],
                    [PersonRepository::REPOSITORY_KEY, $mockPersonRepository],
                    [AuthCodeRepository::REPOSITORY_KEY, $mockAuthCodeRepository],
                    [ClientRepository::REPOSITORY_KEY, $mockClientRepository]
                ]);

            $this->person = $mockRepositoryResolver->getRepository(RefreshTokenRepository::REPOSITORY_KEY);

            $personService = new PersonService($mockRepositoryResolver, $mockContainer, $mockLogger);

            try {
                $result = $personService->getPersonFromAuthenticationVariables($inputData);
                //TODO::Expand this part of this test out to test more than just that it's a mock person object.
                $this->assertInstanceOf("Person", $result);
            } catch (SynapseException $e) {
                $this->assertEquals($e->getUserMessage(), $expectedResult['user_message']);
                $this->assertEquals($e->getMessage(), $expectedResult['dev_message']);
                $this->assertEquals($e->getCode(), $expectedResult['code']);
                $this->assertEquals($e->getHttpCode(), $expectedResult['http_code']);
                $this->assertInstanceOf('\Synapse\CoreBundle\Exception\UnauthorizedException', $e);
            }

        }, [
                'examples' => [
                    [
                        // grant type = invalid, will throw an UnauthorizedException
                        [
                            "client_id" => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
                            "client_secret" => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
                            "grant_type" => "No, grant wrote",
                            "username" => "Chris.Coordinator@mailinator.com",
                            "password" => "Qait@123"
                        ],
                        true,
                        [
                            'user_message' => 'You have provided an unsupported grant_type. Please try your request again with a valid grant type.',
                            'dev_message' => 'getPersonFromAuthenticationVariables Invalid grant type provided: No, grant wrote',
                            'code' => 'invalid_grant',
                            'http_code' => 401
                        ]
                    ],
                    [
                        // grant type = password , wont throw exception , would return person object
                        [
                            "client_id" => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
                            "client_secret" => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
                            "grant_type" => "password",
                            "username" => "Chris.Coordinator@mailinator.com",
                            "password" => "Qait@123"
                        ],
                        true,
                        []
                    ],
                    [
                        // grant type = password , wont throw exception , would return person object
                        [
                            "client_id" => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
                            "client_secret" => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
                            "grant_type" => "password",
                            "username" => "Chris.Coordinator@mailinator.com",
                            "password" => "Qait@123"
                        ],
                        false,
                        [
                            'user_message' => '',
                            'dev_message' => '',
                            'code' => 'invalid_grant',
                            'http_code' => 401
                        ]
                    ],
                    [
                        // grant_type = authorization_code , throw exception
                        [
                            "client_id" => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
                            "client_secret" => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
                            "grant_type" => "authorization_code",
                            "code" => "test"
                        ],
                        true,
                        []

                    ],
                    [
                        // grant_type = authorization_code , wont throw exception , would return person object
                        [
                            "client_id" => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
                            "client_secret" => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
                            "grant_type" => "authorization_code",
                            "code" => "test"
                        ],
                        false,
                        [
                            'user_message' => 'Your authorization code, client ID, or client secret is invalid. Please try your request again with valid credentials',
                            'dev_message' => 'getPersonFromAuthenticationVariables Authorization Code: test',
                            'code' => 'invalid_grant',
                            'http_code' => 401
                        ]

                    ],
                    [
                        // grant_type = refresh_token , throw exception

                        [
                            "access_token" => "382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
                            "refresh_token" => "382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
                            "grant_type" => "refresh_token"
                        ],
                        false,
                        [
                            'user_message' => 'Your refresh token, client ID, or client secret is invalid. Please try your request again with valid credentials.',
                            'dev_message' => 'getPersonFromAuthenticationVariables Refresh Token: 382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s',
                            'code' => 'invalid_grant',
                            'http_code' => 401
                        ]

                    ],
                    [
                        // grant type = client_credentials , returns person
                        [
                            "client_id" => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
                            "client_secret" => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
                            "grant_type" => "client_credentials"
                        ],
                        true,
                        []

                    ],
                    [
                        // grant type = client_credentials , throw exception
                        [
                            "client_id" => "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s",
                            "client_secret" => "3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480",
                            "grant_type" => "client_credentials"
                        ],
                        false,
                        [
                            'user_message' => 'Your client ID or client secret is invalid. Please try your request again with valid credentials',
                            'dev_message' => 'getPersonFromAuthenticationVariables Client Internal ID: 1 Client ID: 382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s Client Secret: 3lmdg9u1qj40wkgc0w088o0c00gcwgcgcggwssogccwgk8w480',
                            'code' => 'invalid_grant',
                            'http_code' => 401
                        ]

                    ]

                ]
            ]
        );

    }

}
