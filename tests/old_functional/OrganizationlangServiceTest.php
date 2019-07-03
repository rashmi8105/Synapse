<?php

use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\OrganizationDTO;
use Synapse\CoreBundle\Entity\OrganizationLang;
class OrganizationlangServiceTest extends \Codeception\TestCase\Test
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
	 * @var \Synapse\CoreBundle\Service\Impl\OrganizationlangService
	 */
	private $organizationLangService;
	
	private $organization = 1;
	
	private $invalidOrg = -2;
	
	private $langId = 1;
	
	/**
	 * {@inheritDoc}
	 */
	
	public function _before()
	{
		$this->container = $this->getModule('Symfony2')->kernel->getContainer();
		$this->organizationLangService= $this->container
		->get('organizationlang_service');
	}
	
	public function testGetOrganizations()
	{
		$organizationLang = $this->organizationLangService->getOrganizations();
		$this->assertInternalType('array',$organizationLang);
        $this->assertArrayHasKey('institutions_last_updated',$organizationLang);
        $this->assertArrayHasKey('institutions',$organizationLang);
        $this->assertNotEmpty($organizationLang,'organizationLang should not be empty');
		$this->assertNotNull($organizationLang['institutions']);
		$this->assertNotNull($organizationLang['institutions'][0]['id']);
		$this->assertNotNull($organizationLang['institutions'][0]['name']);
		$this->assertNotNull($organizationLang['institutions'][0]['nick_name']);
		$this->assertNotNull($organizationLang['institutions'][0]['subdomain']);
		$this->assertNotNull($organizationLang['institutions'][1]['timezone']);
	}	
	
	public function testGetOrganization()
	{
		$organization = $this->organizationLangService->getOrganization($this->organization);

		$this->assertInternalType('array',$organization);
		$this->assertEquals($this->organization, $organization['id']);
		$this->assertNotNull($organization);
		$this->assertNotNull($organization['id']);
		$this->assertNotNull($organization['name']);
		$this->assertNotNull($organization['nick_name']);
		$this->assertNotNull($organization['subdomain']);
		$this->assertNotNull($organization['timezone']);
	}
	
	/**
	 * @expectedException Synapse\RestBundle\Exception\ValidationException
	 */
	public function testGetOrganizationInvalid()
	{
		$organization = $this->organizationLangService->getOrganization($this->invalidOrg);
	}
	
	public function createOrganizationDto()
	{
		$organizationDto = new OrganizationDTO();

		$organizationDto->setId($this->organization);
		$organizationDto->setLangid($this->langId);
		$organizationDto->setName('Organization');
		$organizationDto->setNickName("org");
		$organizationDto->setSubdomain("SubDomain");
		$organizationDto->setTimezone("Africa/Algiers");
		$organizationDto->setWebsite("www.organization.com");
		return $organizationDto;
		
	}
	
	public function createOrganizationLang()
	{
		$organizationLang = new OrganizationLang();
		$organizationLang->setNickName("Org");
		$organizationLang->setOrganizationName("Organization");
        $organizationLang->setOrganization(1);
		return $organizationLang;
	}
	
}