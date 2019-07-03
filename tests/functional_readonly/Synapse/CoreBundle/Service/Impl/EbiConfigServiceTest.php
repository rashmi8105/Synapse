<?php

use Codeception\TestCase\Test;

class EbiConfigServiceTest extends Test
{
    use Codeception\Specify;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var \Synapse\CoreBundle\Repository\RepositoryResolver
     */
    private $repositoryResolver;

    /**
     *
     * @var \Synapse\CoreBundle\Service\Impl\EbiConfigService
     */
    private $ebiConfigService;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->repositoryResolver = $this->container->get('repository_resolver');
        $this->ebiConfigService = $this->container->get('ebi_config_service');
    }

    public function testGetSystemUrl()
    {
        $this->specify("Verify the functionality to get System Url", function($organizationId, $expectedOutput){

            $result = $this->ebiConfigService->getSystemUrl($organizationId);

            $this->assertEquals($expectedOutput, $result);

        }, ["examples"=>
            [
                // This organization has LDAP/SAML enabled
                [180, "http://synapsetesting0180-functional_readonly.skyfactor.com/"],
                // This organization does not have LDAP/SAML enabled
                [203, "https://mapworks.skyfactor.com/"]
            ]
        ]);
    }

    public function testGenerateCompleteUrl()
    {
        $this->markTestSkipped("This would fail now. Once the migration script is executed in functional database , it would pass");
        $this->specify("Verify the functionality to get System Url", function($key, $organizationId, $expectedUrl){

            $result = $this->ebiConfigService->generateCompleteUrl($key, $organizationId);
            verify($result)->equals($expectedUrl);

        }, ["examples"=>
            [
                // This organization has LDAP/SAML enabled
                ['StudentDashboard_AppointmentPage',180, "https://synapsetesting0180-uat.skyfactor.com/#/student-agenda"],
                // This organization does not have LDAP/SAML enabled
                ['StudentDashboard_AppointmentPage',203, "https://mapworks.skyfactor.com/#/student-agenda"],
                ['Staff_ReferralPage',180, "https://synapsetesting0180-uat.skyfactor.com/#/dashboard/"],
                ['Staff_ReferralPage',203, "https://mapworks.skyfactor.com/#/dashboard/"],
                ['Gateway_Staff_Landing_Page',180, "https://synapsetesting0180-uat.skyfactor.com/#/dashboard/"],
                ['Gateway_Staff_Landing_Page',203, "https://mapworks.skyfactor.com/#/dashboard/"],
                ['Gateway_Student_Landing_Page',180, "https://synapsetesting0180-uat.skyfactor.com/#/student/"],
                ['Gateway_Student_Landing_Page',203, "https://mapworks.skyfactor.com/#/student/"],
                ['Academic_Update_Reminder_to_Faculty',180, "https://synapsetesting0180-uat.skyfactor.com/#/academic-updates/update/"],
                ['Academic_Update_Reminder_to_Faculty',203, "https://mapworks.skyfactor.com/#/academic-updates/update/"],
                // deleted keys
                ['StaffDashboard_AppointmentPage',203, "https://mapworks.skyfactor.com/"],
                ['Academic_Update_View_URL',203, "https://mapworks.skyfactor.com/"],
                ['Student_ResetPwd_URL_Prefix',203, "https://mapworks.skyfactor.com/"],
                ['MultiCampus_Change_Request',203, "https://mapworks.skyfactor.com/"]
            ]
        ]);
    }


}