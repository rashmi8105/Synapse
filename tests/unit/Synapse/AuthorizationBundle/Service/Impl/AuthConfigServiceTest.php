<?php

use Synapse\AuthenticationBundle\Service\Impl\AuthConfigService;

class AuthConfigServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    public function testValidateClientIds()
    {

        $this->specify("Test to validate ClientIds", function ($organizationId, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockRepositoryResolver->method('getRepository')->willReturnCallback(function ($repositoryKey) {
                if ($repositoryKey == "SynapseAuthenticationBundle:OrgAuthConfig") {
                    $mockOrgConfigRepository = $this->getMock('OrgAuthConfigRepository', ['findOneBy']);
                    $mockOrgConfigRepository->method('findOneBy')->willReturnCallback(function ($criteriaArray) {
                        if ($criteriaArray['organization']) {
                            return true;
                        } else {
                            return false;
                        }
                    });
                    return $mockOrgConfigRepository;
                } else {
                    return 1;
                }
            });
            $mockLogger = $this->getMock('Logger', []);
            $mockSecurityContext = $this->getMock('SecurityContext', []);
            $mockEbiConfigService = $this->getMock('EbiCongigService', []);
            $mockOrganizationService = $this->getMock('Organization', []);

            $tokenService = new AuthConfigService($mockRepositoryResolver, $mockLogger, $mockSecurityContext, $mockEbiConfigService, $mockOrganizationService);
            $result = $tokenService->getAuthConfigForOrganization($organizationId);
            $this->assertEquals($result, $expectedResult);

        }, [
                'examples' => [

                    // This  will return false as the organization  id  id 0 and there is no auth config for the organization
                    [
                        0, false
                    ],
                    // Valid organization Id(1) will return auth config for the organization
                    [
                        1, true
                    ],
                    // Valid organization Id (2) will return auth config for the organization
                    [
                        2, true
                    ],

                ]
            ]
        );
    }


}