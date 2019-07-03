<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\DataBundle\EntityDto\ProfileBlocksDto;
use JMS\Serializer\Tests\Fixtures\Publisher;

class ProfileBlocksServiceGetTest extends \Codeception\TestCase\Test
{

    private $profileBlockService;
    
    private $userId = 1;
    
    /**
     *
     * @var Symfony\Component\DependencyInjection\Container
     */
    private $container;

    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->profileBlockService = $this->container->get('profileblocks_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->userId);
    }
    public function createProfileBlockDto()
    {
        $profile = new ProfileBlocksDto();
        $profile->setProfileBlockName('Test Block');
        $profileItemsArray = [];
        $profileItemsArray[0]['id'] = 1;
        $profileItemsArray[1]['id'] = 2;
        $profile->setProfileItems($profileItemsArray);
        return $profile;
    }

    public function testGetProfileBlock()
    {
        $profile = $this->createProfileBlockDto();
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        $profileId = $profile->getProfileBlockId();
        $profiles = $this->profileBlockService->getBlockById($profileId);
        
        $this->assertGreaterThan(0, $profiles->getProfileBlockId());
        $this->assertEquals($profileId, $profiles->getProfileBlockId());
        $this->assertEquals('Test Block', $profiles->getProfileBlockName());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testGetProfileBlockInvalidId()
    {
        $profiles = $this->profileBlockService->getBlockById(- 1);
    }

    public function testGetProfileBlocks()
    {
        $profile = $this->createProfileBlockDto();
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        $profileId = $profile->getProfileBlockId();
        $profiles = $this->profileBlockService->getDatablocks($profileId);
        $profiles = $profiles[0];
        
        $this->assertArrayHasKey('profile_block_id', $profiles);
        $this->assertArrayHasKey('profile_block_name', $profiles);
    }
    
    public function testGetProfileBlocksForSearch()
    {
        $this->initializeRbac();
        $profile = $this->createProfileBlockDto();
    	$profile = $this->profileBlockService->createProfileBlocks($profile);
    	$profileId = $profile->getProfileBlockId();
    	$profiles = $this->profileBlockService->getDatablocks(1,'search');
    	$profiles = $profiles[0];
    
    	$this->assertArrayHasKey('profile_block_id', $profiles);
    	$this->assertArrayHasKey('profile_block_name', $profiles);
    }
}