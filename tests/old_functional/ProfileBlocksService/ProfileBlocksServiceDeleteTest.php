<?php
use Codeception\Util\Stub;
use Synapse\DataBundle\EntityDto\ProfileBlocksDto;
use JMS\Serializer\Tests\Fixtures\Publisher;

class ProfileBlocksServiceDeleteTest extends \Codeception\TestCase\Test
{

    private $profileBlockService;

    public function _before()
    {
        $container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->profileBlockService = $container->get('profileblocks_service');
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

    public function testDeleteProfileBlocks()
    {
        $profile = $this->createProfileBlockDto();
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        
        $profile = $this->profileBlockService->deleteProfileBlocks($profile->getProfileBlockId());
        $this->assertEmpty($profile);
        
    }
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testDeleteProfileBlocksInvliadId()
    {
          
        $profile = $this->profileBlockService->deleteProfileBlocks(-1);
        $this->assertEmpty($profile);
    
    }
}