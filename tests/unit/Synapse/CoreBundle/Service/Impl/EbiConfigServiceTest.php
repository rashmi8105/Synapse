<?php
namespace Synapse\CoreBundle\Service\Impl;

use Symfony\Bridge\Monolog\Logger;
use Synapse\CoreBundle\Entity\EbiConfig;
use Synapse\CoreBundle\Entity\Organization;
use Synapse\CoreBundle\Service\Impl\LoggerHelperService;
use Synapse\CoreBundle\Util\UtilServiceHelper;

class EbiConfigServiceTest extends \PHPUnit_Framework_TestCase
{
    use\Codeception\Specify;

    public function testGetSystemUrl()
    {
        $this->specify("Test fetch system url", function ($isLdapSamlEnabled, $subdomain, $environment, $expectedResult) {

            $mockRepositoryResolver = $this->getMock('RepositoryResolver', ['getRepository']);
            $mockLogger = $this->getMock('Logger', ['debug', 'error']);

            // Container mock object
            $mockContainer = $this->getMock('Container', ['get', 'getParameter']);

            $mockOrganizationRepository = $this->getMock('OrganizationRepository', ['find']);

            $mockEbiConfigRepository = $this->getMock('EbiConfigRepository', ['findOneBy']);

            $mockRepositoryResolver->method('getRepository')
                ->willReturnMap([
                    ['SynapseCoreBundle:EbiConfig', $mockEbiConfigRepository],
                    ['SynapseCoreBundle:Organization', $mockOrganizationRepository]
                ]);

            $mockContainer->method('getParameter')
                ->willReturnMap([
                    ['kernel.environment', $environment]
                ]);

            $mockOrganization = new Organization();

            $mockOrganization->setIsLdapSamlEnabled($isLdapSamlEnabled);
            $mockOrganization->setSubdomain($subdomain);

            $mockOrganizationRepository->expects($this->any())
                ->method('find')
                ->will($this->returnValue($mockOrganization));

            $mockEbiConfigRepository->expects($this->any())
                ->method('findOneBy')
                ->will($this->returnValue($this->getEbiConfigInstance()));

            $ebiConfigService = new EbiConfigService($mockRepositoryResolver, $mockLogger, $mockContainer);

            $mockOrganizationId = 2;

            $systemUrl = $ebiConfigService->getSystemUrl($mockOrganizationId);

            $this->assertEquals($expectedResult, $systemUrl);
            $this->assertInternalType("string", $systemUrl);
        }, [
            'examples' => [
                // LDAP/SAML enabled. 'prod' Environment variable
                [ true, 'nsu', 'prod', 'http://nsu.skyfactor.com/'],
                // LDAP/SAML enabled. 'uat' Environment variable
                [ true, 'nsu', 'uat', 'http://nsu-uat.skyfactor.com/'],
                // LDAP/SAML enabled. null Environment variable
                [ true, 'nsu', null, 'http://nsu.skyfactor.com/'],
                // LDAP/SAML disabled. Environment variable is irrelevant
                [ false, '', 'prod', 'https://mapworks-dev.skyfactor.com/'],
            ]
        ]);
    }

    /**
     * Returns EbiConfig instance.
     *
     * @return EbiConfig
     */
    private function getEbiConfigInstance()
    {
        $ebiConfig = new EbiConfig();
        $ebiConfig->setKey("System_URL");
        $ebiConfig->setValue("https://mapworks-dev.skyfactor.com/");

        return $ebiConfig;
    }

}