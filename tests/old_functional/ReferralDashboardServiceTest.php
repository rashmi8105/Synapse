<?php
use Codeception\Util\Stub;

require_once 'tests/functional/FunctionalBaseTest.php';

class ReferralDashboardServiceTest extends FunctionalBaseTest
{
	/**
	 * @var UnitTester
	 */
	protected $tester;
	
	/**
	 * @var Symfony\Component\DependencyInjection\Container
	 */
	private $container;
	
	/**
	 * @var \Synapse\CoreBundle\Service\Impl\DashboardReferralService
	 */
	private $dashboardreferral;
	
	/**
	 * @var \Synapse\CoreBundle\Service\Impl\PersonService
	 */
	private $personService;
	
	/**
	 * {@inheritDoc}
	 */
	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->dashboardreferral = $this->container->get('dashboardreferral_service');
		$this->personService = $this->container->get('person_service'); 
		
	}

   public function testListUploeadHistoryCSV()
    {
        $person = $this->personService->find(1);        
        $dashboard = $this->dashboardreferral->getReferralDetailsBasedFilters($person, 'all', 'all', '', '', '', '', true, false);      
      
        $this->assertEquals('You may continue to use Mapworks while your download completes. We will notify you when it is available.', $dashboard[0]);
    }
 
}
