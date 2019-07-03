<?php
use Codeception\Util\Stub;
use Synapse\DataBundle\EntityDto\ProfileBlocksDto;
use JMS\Serializer\Tests\Fixtures\Publisher;

class ProfileBlocksServiceCreateTest extends \Codeception\TestCase\Test
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

    public function testCreateDataBlock()
    {
        $profile = $this->createProfileBlockDto();
        $profile = $this->profileBlockService->createProfileBlocks($profile);
        $this->assertGreaterThan(0, $profile->getProfileBlockId());
        $this->assertEquals('Test Block', $profile->getProfileBlockName());
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateDataBlockUniqueException()
    {
        $profile = $this->createProfileBlockDto();
        $this->profileBlockService->createProfileBlocks($profile);
        $this->profileBlockService->createProfileBlocks($profile);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateDataBlockProfileMappedException()
    {
        $profile = $this->createProfileBlockDto();
        $this->profileBlockService->createProfileBlocks($profile);
        $profile->setProfileBlockName('Test Block123');
        $this->profileBlockService->createProfileBlocks($profile);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateDataBlockLongBlockName()
    {
        $profile = $this->createProfileBlockDto();
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
    public function testCreateDataBlockAssingInvaliedProfile()
    {
        $profile = $this->createProfileBlockDto();
        $profileItemsArray = [];
        $profileItemsArray[0]['id'] = - 1;
        
        $profile->setProfileItems($profileItemsArray);
        $profile = $this->profileBlockService->createProfileBlocks($profile);
    }
}
