<?php
use Codeception\Util\Stub;
use Synapse\DataBundle\EntityDto\ProfileBlocksDto;
use JMS\Serializer\Tests\Fixtures\Publisher;

class ProfileBlocksServiceUpdateTest extends \Codeception\TestCase\Test
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

    public function testUpdateDataBlock()
    {
        $profile = $this->createProfileBlockDto();
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        $profile->setProfileBlockName('Test Block Updated');
		$profileItemsArray = [];
        $profileItemsArray[0]['id'] = 3;
        $profile->setProfileItems($profileItemsArray);
        $profile = $this->profileBlockService->updateProfileBlocks($profile);
        
        $this->assertGreaterThan(0, $profile->getProfileBlockId());
        $this->assertEquals('Test Block Updated', $profile->getProfileBlockName());
        $profileItems = $profile->getProfileItems();
        $this->assertArrayHasKey('id', $profileItems[0]);
    }
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateDataBlockInvalidId()
    {
        $profile = $this->createProfileBlockDto();
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        $profile->setProfileBlockName('Test Block Updated');
        $profile->setProfileBlockId(-1);
        $profile = $this->profileBlockService->updateProfileBlocks($profile);
    
        
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateDataBlockUniqueException()
    {
        $profile = $this->createProfileBlockDto();
        $profile->setProfileBlockName('Test Block Updated');
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        
        $profile2 = $this->createProfileBlockDto();
        $profile2 = $this->profileBlockService->createProfileBlocks($profile2);
        $profile2->setProfileBlockName('Test Block Updated');
        $profile2 = $this->profileBlockService->updateProfileBlocks($profile2);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateDataBlockLongBlockName()
    {
        $profile = $this->createProfileBlockDto();
        
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        
        $blockName = "";
        for ($strLen = 0; $strLen < 60; $strLen ++) {
            $blockName .= $strLen;
        }
        $profile->setProfileBlockName($blockName);
        $this->profileBlockService->createProfileBlocks($profile);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateDataBlockProfileUniqueException()
    {
        $profile = $this->createProfileBlockDto();
        $profile->setProfileBlockName('Test Block Updated');
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        
        $profile2 = $this->createProfileBlockDto();
        $profile2->setProfileBlockName('Test Block');
        $profile2->setProfileItems([]);
        $profile2 = $this->profileBlockService->createProfileBlocks($profile2);
        
        $profileItemsArray = [];
        $profileItemsArray[0]['id'] = 1;
        $profileItemsArray[1]['id'] = 2;
        $profile2->setProfileItems($profileItemsArray);
        $profile2 = $this->profileBlockService->updateProfileBlocks($profile2);
    }

    public function testUpdateDataBlockRemoveOneItem()
    {
        $profile = $this->createProfileBlockDto();
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        $profile->setProfileBlockName('Test Block Updated');
        $profileItemsArray = [];
        $profileItemsArray[0]['id'] = 2;
        
        $profile->setProfileItems($profileItemsArray);
        $profile = $this->profileBlockService->updateProfileBlocks($profile);
        
        $this->assertGreaterThan(0, $profile->getProfileBlockId());
        $this->assertEquals('Test Block Updated', $profile->getProfileBlockName());
        $profileItems = $profile->getProfileItems();
        $this->assertArrayHasKey('id', $profileItems[0]);
    }

    public function testUpdateDataBlockRemoveAllItem()
    {
        $profile = $this->createProfileBlockDto();
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        $profile->setProfileBlockName('Test Block Updated');
        
        $profile->setProfileItems([]);
        $profile = $this->profileBlockService->updateProfileBlocks($profile);
        
        $this->assertGreaterThan(0, $profile->getProfileBlockId());
        $this->assertEquals('Test Block Updated', $profile->getProfileBlockName());
        $profileItems = $profile->getProfileItems();
        $this->assertEmpty($profileItems);
    }

    public function testUpdateDataBlockAddOneItem()
    {
        $profile = $this->createProfileBlockDto();
        $profile->setProfileBlockName('Test Block Updated');
        $profileItemsArray = [];
        $profileItemsArray[0]['id'] = 1;
        
        $profile->setProfileItems($profileItemsArray);
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        $profile->setProfileBlockName('Test Block Updated');
        $profileItemsArray = [];
        $profileItemsArray[0]['id'] = 2;
        $profileItemsArray[1]['id'] = 3;
        
        $profile->setProfileItems($profileItemsArray);
        $profile = $this->profileBlockService->updateProfileBlocks($profile);
        
        $this->assertGreaterThan(0, $profile->getProfileBlockId());
        $this->assertEquals('Test Block Updated', $profile->getProfileBlockName());
        $profileItems = $profile->getProfileItems();
        $this->assertArrayHasKey('id', $profileItems[0]);
    }
}
