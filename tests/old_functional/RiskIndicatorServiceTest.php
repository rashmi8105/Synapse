<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;

class RiskIndicatorServiceTest extends \Codeception\TestCase\Test
{

    /**
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     *
     * @var \Synapse\SearchBundle\Service\Impl\RiskService
     */
    private $riskService;

    private $groupService;

    private $userId = 4;

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->riskService = $this->container->get('risk_service');
        $this->groupService = $this->container->get('group_service');
    }

    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /**
         *
         * @var Manager $rbacMan
         */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->userId);
    }

    public function testGetRiskIndicators()
    {
        $result = $this->riskService->getRiskIndicatorsOrIntentToLeave($type = 'indicators');
       
        $this->assertInstanceOf("Synapse\SearchBundle\EntityDto\RiskLevelArrayDto", $result);
        $this->assertInternalType("array", $result->getRiskLevels());
        
        foreach($result->getRiskLevels() as $risk){
            
            $this->assertInstanceOf("Synapse\SearchBundle\EntityDto\RiskLevelsDto", $risk);
            $this->assertNotEmpty($risk->getRiskLevel());
            $this->assertNotEmpty($risk->getRiskText());
        }
    }

    public function testGetIntentToLeaveTypes()
    {
        $result = $this->riskService->getRiskIndicatorsOrIntentToLeave($type = 'intent_to_leave');

        $this->assertInstanceOf("Synapse\SearchBundle\EntityDto\IntentToLeaveArrayDto", $result);
        $this->assertInternalType("array", $result->getIntentToLeaveTypes());
        
        foreach($result->getIntentToLeaveTypes() as $intent){
            
            $this->assertInstanceOf("Synapse\SearchBundle\EntityDto\IntentToLeaveDto", $intent);
            $this->assertNotEmpty($intent->getId());
            $this->assertNotEmpty($intent->getText());
        }
        
    }
    public function testGetListGroupsUser()
    {
        $this->initializeRbac();
        $result = $this->groupService->getListGroups($this->userId); 
        $this->assertEquals(2, $result->getGroups()[0]
            ->getGroupId());       
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetRiskIndicatorsInvalidType()
    {
        $result = $this->riskService->getRiskIndicatorsOrIntentToLeave($type = 'indicato');
    }

}
