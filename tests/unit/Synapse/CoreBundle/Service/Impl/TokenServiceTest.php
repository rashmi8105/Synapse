<?php

class TokenServiceTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    /**
     * @expectedException OAuth2\OAuth2AuthenticateException
     */
    public function testValidateClientIds()
    {

        $this->specify("Test to validate ClientIds", function ($clientId, $organizationId) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockRepositoryResolver->method('getRepository')->willReturn(1);
            $mockLogger = $this->getMock('Logger', []);
            $mockPerson = $this->getMock('Person', []);
            $mockDoctrine = $this->getMock('Doctrine', ['getmanager']);
            $mockDoctrine->method('getmanager')->willReturn(1);

            $tokenService = new \Synapse\CoreBundle\Service\Impl\TokenService($mockRepositoryResolver, $mockLogger, $mockPerson, $mockDoctrine);
            // there will be no assertions here, as this method does not return anything, it would either throw exception or return void
            $tokenService->validateClientIds($clientId, $organizationId);


        }, [
                'examples' => [
                    //valid data for web app , will not throw exceptions
                    [
                        "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s", 2
                    ],
                    //valid data for web app admin, will not throw exceptions
                    [
                        "2_14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884", -1
                    ],

                    //valid data for ART, will not throw exceptions
                    [
                        "3_2y0ku7f5748wwwskkk84o00ssgwsgkokks8ogs08ckscckcskg", -2
                    ],
                    // Throws exception
                    [
                        "1_382nwsdkpl44gs84s448w8g00s4okkksc880og40wgkswcgw0s", -1
                    ],
                    // Throws exception
                    [
                        "2_14tx5vbsnois4ggg0ok0c4gog8kg0ww488gwkg88044cog4884", 1
                    ],
                    // Throws exception
                    [
                        "3_2y0ku7f5748wwwskkk84o00ssgwsgkokks8ogs08ckscckcskg", 1
                    ],
                    // Throws exception
                    [
                        "3_2y0ku7f5748wwwskkk84o00ssgwsgkokks8ogs08ckscckcskg", -1
                    ],
                ]
            ]
        );
    }


}