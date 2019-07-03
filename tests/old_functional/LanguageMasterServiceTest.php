<?php

use Codeception\Util\Stub;

class LanguageMasterServiceTest extends \Codeception\TestCase\Test
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
	 * @var \Synapse\CoreBundle\Service\Impl\LanguageMasterService
	 */
	private $langMasterService;
	
	/**
	 * {@inheritDoc}
	 */
	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->langMasterService = $this->container
		->get('lang_service');
	}
	
	
	public function testGetLanguageById()
	{
		$language = $this->langMasterService->getLanguageById(1);
		
		$this->assertInstanceOf('Synapse\CoreBundle\Entity\LanguageMaster', $language,"Language Not Found");
	}
}