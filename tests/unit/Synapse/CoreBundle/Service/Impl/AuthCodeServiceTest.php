<?php
use Synapse\CoreBundle\Repository\AuthCodeRepository;
use Synapse\CoreBundle\Service\Impl\AuthCodeService;
use Synapse\RestBundle\Entity\AuthCodeDto;

class AuthCodeServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    /**
     * @expectedException \Synapse\CoreBundle\Exception\SynapseException
     */
    public function testReGenerateAuthorizationCode()
    {

        $this->specify("Test regeneration of AuthorizationCode", function ($authCodeDto, $organizationId, $noAuthException = false) {
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

            $mockClientObject = $this->getMock('Client', ['getRandomId', 'getSecret']);

            $mockClientObject->method('getRandomId')->willReturn(1);
            $mockClientObject->method('getSecret')->willReturn(1);

            // Entity Mocks
            $mockAuthCodeObject = $this->getMock('\Synapse\CoreBundle\Entity\AuthCode', ['getClientId', 'getClient']);

            $mockAuthCodeObject->method('getClient')->willReturn($mockClientObject);
            $mockAuthCodeObject->method('getClientId')->willReturn(1);

            //Repository Mocks
            $mockAuthCodeRepository = $this->getMock("AuthCodeRepository", ["findOneBy", "flush"]);
            if ($noAuthException) {
                $mockAuthCodeRepository->method('findOneBy')->willReturn(null);
            } else {
                $mockAuthCodeRepository->method('findOneBy')->willReturn($mockAuthCodeObject);
            }

            $mockAuthCodeRepository->method('flush')->willReturn(1);


            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        AuthCodeRepository::REPOSITORY_KEY,
                        $mockAuthCodeRepository
                    ],

                ]);

            $authCodeService = new AuthCodeService($mockRepositoryResolver, $mockContainer, $mockLogger);

            $this->assertNull($authCodeDto->getAuthCode());
            $this->assertNull($authCodeDto->getClientId());
            $this->assertNull($authCodeDto->getClientSecret());

            $authCodeDto = $authCodeService->reGenerateAuthorizationCode($authCodeDto, $organizationId);
            $this->assertNotNull($authCodeDto->getAuthCode());
            $this->assertEquals(1, $authCodeDto->getClientId());
            $this->assertEquals(1, $authCodeDto->getClientSecret());
        }, [
                'examples' => [
                    [
                        // Regenerates Auth code
                        $this->generateAuthCodeDto(1, 1), 1
                    ],
                    [
                        // Throws SynapseValidation Exception
                        $this->generateAuthCodeDto(1, 1), 1, true
                    ],
                    [
                        // Throws Access Denied Exception
                        $this->generateAuthCodeDto(1, 1), 2
                    ],
                ]
            ]
        );
    }


    public function testReInstateAuthorizationCode()
    {

        $this->specify("Test to regenerate the the same authorization code ", function ($authorizationCode) {
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


            $authCodeObject = new \Synapse\CoreBundle\Entity\AuthCode();
            $authCodeObject->setId(1);
            $authCodeObject->setToken($authorizationCode);

            //Repository Mocks
            $mockAuthCodeRepository = $this->getMock("AuthCodeRepository", ["findOneByIncludingDeletedRecords", "persist", "flush"]);

            $mockAuthCodeRepository->method('findOneByIncludingDeletedRecords')->willReturn($authCodeObject);

            $mockAuthCodeRepository->method('persist')->willReturn(function ($authCodeObject) {
                $authCodeObject->setId(2);
            });

            $mockAuthCodeRepository->method('flush')->willReturn(1);
            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    [
                        AuthCodeRepository::REPOSITORY_KEY,
                        $mockAuthCodeRepository
                    ],

                ]);


            $this->assertEquals($authorizationCode, $authCodeObject->getToken()); // authorization code should be the same which is passed in before the reInstateAuthorizationCode is called
            $authCodeService = new AuthCodeService($mockRepositoryResolver, $mockContainer, $mockLogger);
            $authCodeService->reInstateAuthorizationCode($authorizationCode);

            $authorizationCodeExplode = explode("_", $authCodeObject->getToken()); // After the reInstateAuthorizationCode is called. The same objects token value would be changed
            $this->assertEquals($authorizationCodeExplode[0], $authorizationCode); // The first part of the array would have the authorization code
            $this->assertEquals($authorizationCodeExplode[2], "expired"); // the end part would have expired.

        }, [
                'examples' => [
                    [
                        // Regenerates Auth code
                        "testingAuthcode"
                    ],

                    [
                        // Regenerates Auth code
                        "MTgwZGRkOTRjZjE1YzI3N2NjYThiY2RmMmMwNDljZGU5YTQxMTlmMmRkZjAxZTcxZGJmN2RjZTc3MDJlZjQwNA"
                    ],

                ]
            ]
        );
    }

    private function generateAuthCodeDto($personId, $organizationId)
    {
        $authCodeDto = new AuthCodeDto();
        $authCodeDto->setPersonId($personId);
        $authCodeDto->setOrganizationId($organizationId);
        return $authCodeDto;
    }

}