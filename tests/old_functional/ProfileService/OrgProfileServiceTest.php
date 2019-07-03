<?php
use Synapse\CoreBundle\Security\Authorization\TinyRbac\Manager;
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\ProfileDto;
use Synapse\RestBundle\Entity\ReOrderProfileDto;

class OrgProfileServiceTest extends \Codeception\TestCase\Test
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
     * @var \Synapse\CoreBundle\Service\ProfileService
     */
    private $profileService;

    private $org = 1;
    
    private $personId = 1;

    /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->profileService = $this->container->get('orgprofile_service');
    }
    protected function initializeRbac()
    {
        // Bootstrap Rbac Authorization.
        /** @var Manager $rbacMan */
        $rbacMan = $this->container->get('tinyrbac.manager');
        $rbacMan->initializeForUser($this->personId);
    }
    public function testCreateOrgProfileNumType()
    {
        $this->initializeRbac();
        $profileDto = $this->createProfileDto("O");
        $profile = $this->profileService->createProfile($profileDto);
        
        $this->assertEquals('O', $profile->getDefinitionType());
        $this->assertEquals($this->org, $profile->getOrganizationId());
        $this->assertEquals('N', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateOrgProfileUnique()
    {
        $profileDto = $this->createProfileDto("O");
        $profileDto->setItemLabel("AAAAA");
        $profile = $this->profileService->createProfile($profileDto);
        
        $profileDto->setItemLabel("AAAAA");
        $profile = $this->profileService->createProfile($profileDto);
    }

    public function testCreateOrgProfileListType()
    {
        $profileDto = $this->createProfileDto("O", true);
        $profile = $this->profileService->createProfile($profileDto);
        
        $this->assertEquals('O', $profile->getDefinitionType());
        $this->assertEquals($this->org, $profile->getOrganizationId());
        $this->assertEquals('S', $profile->getItemDataType());
    }
    
    /*
     * Update
     */
    public function testUpdateOrgProfileNumTypeToList()
    {
        $profileDto = $this->createProfileDto("O");
        $profile = $this->profileService->createProfile($profileDto);
        
        $profileDto = $this->createProfileDto("O", true);
        $profileDto->setId($profile->getId());
        $profile = $this->profileService->editProfile($profileDto);
        
        $this->assertEquals('O', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
    }

    public function testUpdateOrgProfileListToNum()
    {
        $profileDto = $this->createProfileDto("O", true);
        $profile = $this->profileService->createProfile($profileDto);
        
        $profileDto = $this->createProfileDto("O");
        $profileDto->setId($profile->getId());
        $profile = $this->profileService->editProfile($profileDto);
        
        $this->assertEquals('O', $profile->getDefinitionType());
        $this->assertEquals('N', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
    }

    public function testUpdateOrgProfileListToList()
    {
        $profileDto = $this->createProfileDto("O", true);
        $profile = $this->profileService->createProfile($profileDto);
        
        $profileDto = $this->createProfileDtoOneList("O", true);
        $profileDto->setId($profile->getId());
        $profile = $this->profileService->editProfile($profileDto);
        
        $this->assertEquals('O', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
    }

    public function testUpdateOrgProfileListToListSame()
    {
        $profileDto = $this->createProfileDto("O", true);
        $profile = $this->profileService->createProfile($profileDto);
        
        $profileDto = $this->createProfileDto("O", true);
        $profileDto->setId($profile->getId());
        $profile = $this->profileService->editProfile($profileDto);
        
        $this->assertEquals('O', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
    }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testeditProfileInvalidId()
    {
        $profileDto = $this->createProfileDto("O", true);
        $profileDto->setId(- 1);
        $profile = $this->profileService->editProfile($profileDto);
    }

    public function testDeteleOrgProfile()
    {
        $profileDto = $this->createProfileDto("O", true);
        $seqArray = array();
        $id = array();
        for ($count = 0; $count < 10; $count ++) {
            $profileDto->setItemLabel("SSS_".$count);
            $profile = $this->profileService->createProfile($profileDto);
            
            $seqArray[] = $profileDto->getSequenceNo();
            $id[] = $profile->getId();
        }
        $profile = $this->profileService->deleteProfile($id[5]);
        $this->assertEquals('O', $profile->getDefinitionType());
        $this->assertAttributeNotEquals('NULL', 'deletedAt', $profile);
    }

    public function testGetOrgProfiles()
    
    {
        $profileDto = $this->createProfileDto("O", true);
        $seqArray = array();
        $id = array();
        for ($count = 0; $count < 10; $count ++) {
            $profileDto->setItemLabel('AAA_' . $count);
            $profile = $this->profileService->createProfile($profileDto);
            $seqArray[] = $profile->getSequenceNo();
            $id[] = $profile->getId();
        }
        $profiles = $this->profileService->getProfiles($this->org);
        $this->assertArrayHasKey('item_label', $profiles['profile_items'][0]);
        $this->assertArrayHasKey('item_subtext', $profiles['profile_items'][0]);
        $this->assertArrayHasKey('item_data_type', $profiles['profile_items'][0]);
        $this->assertArrayHasKey('id', $profiles['profile_items'][0]);
        $this->assertInternalType('array', $profiles['profile_items']);
    }

    public function testGetOrgProfile()
    {
        $profileDto = $this->createProfileDto("O");
        $profile = $this->profileService->createProfile($profileDto);
        $profiles = $this->profileService->getProfile($this->org, $profile->getId());
        
        $this->assertEquals('O', $profiles->getDefinitionType());
        $this->assertEquals($profiles->getId(), $profiles->getId());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profiles->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profiles->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profiles->getNumberType()['decimal_points']);
    }

    public function testGetProfileInvalid()
    {
        $profiles = $this->profileService->getProfile($this->org, - 1);
    }

    public function testReorderOrgProfileToLow()
    
    {
        $profileDto = $this->createProfileDto("O", true);
        $seqArray = array();
        $id = array();
        for ($count = 0; $count < 10; $count ++) {
            $profileDto->setItemLabel('AAA_' . $count);
            $profile = $this->profileService->createProfile($profileDto);
            
            $seqArray[] = $profile->getSequenceNo();
            $id[] = $profile->getId();
        }
        $profiles = $this->profileService->reorderProfile($this->createReorderDto($id[7], $seqArray[3]));
        $profile = $this->profileService->getProfile($this->org, $id[7]);
        $this->assertEquals($id[7], $profile->getId());
        $this->assertEquals('O', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
    }

    public function testReorderOrgProfileToHigh()
    
    {
        $profileDto = $this->createProfileDto("O", true);
        $seqArray = array();
        $id = array();
        for ($count = 0; $count < 10; $count ++) {
            $profileDto->setItemLabel('AAA_' . $count);
            $profile = $this->profileService->createProfile($profileDto);
            
            $seqArray[] = $profile->getSequenceNo();
            $id[] = $profile->getId();
        }
        $profiles = $this->profileService->reorderProfile($this->createReorderDto($id[7], $seqArray[3]));
        $profile = $this->profileService->getProfile($this->org, $id[7]);
        $this->assertEquals($id[7], $profile->getId());
        $this->assertEquals('O', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
    }

    public function testReorderOrgProfileToOut()
    
    {
        $seqArray = array();
        $id = array();
        for ($count = 0; $count < 10; $count ++) {
            $profileDto = $this->createProfileDto("O", true);
            $profileDto->setItemLabel('AAA_' . $count);
            $profile = $this->profileService->createProfile($profileDto);
            
            $seqArray[] = $profile->getSequenceNo();
            $id[] = $profile->getId();
        }
        $profiles = $this->profileService->reorderProfile($this->createReorderDto($id[7], 9999));
        
        $profile = $this->profileService->getProfile($this->org, $id[7]);
        $this->assertEquals($id[7], $profile->getId());
        $this->assertEquals('O', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
    }

    public function createProfileDto($type, $isList = false)
    {
        $profileDto = new ProfileDto();
        $profileDto->setOrganizationId($this->org);
        $profileDto->setLangId(1);
        $profileDto->setItemLabel("Test Label" . uniqid("key_", true));
        $profileDto->setItemSubtext("Test Label Subtext");
        $profileDto->setDefinitionType("O");
        if ($isList !== true) {
            $profileDto->setItemDataType("N");
            
            $numberType = array(
                'min_digits' => 1,
                'max_digits' => 2,
                "decimal_points" => 2
            );
            $profileDto->setNumberType($numberType);
        } else {
            $profileDto->setItemDataType("S");
            $category_type = array();
            $category_type[0]['answer'] = "Karen Sims 1";
            $category_type[0]['value'] = uniqid("val_", true);
            $category_type[0]['sequence_no'] = 1;
            
            $category_type[1]['answer'] = "Karen Sims 2";
            $category_type[1]['value'] = uniqid("val_", true);
            $category_type[1]['sequence_no'] = 2;
            $profileDto->setCategoryType($category_type);
        }
        
        return $profileDto;
    }

    public function createReorderDto($id, $seq)
    {
        $dto = new ReOrderProfileDto();
        $dto->setId($id);
        $dto->setSequenceNo($seq);
        
        return $dto;
    }

    public function createProfileDtoOneList($type, $isList = false)
    {
        $profileDto = new ProfileDto();
        $profileDto->setOrganizationId(1);
        $profileDto->setLangId(1);
        $profileDto->setItemLabel("Test Label" . strrev(time()));
        $profileDto->setItemSubtext("Test Label Subtext");
        if ($type == 'O') {
            $profileDto->setDefinitionType("O");
        } else {
            $profileDto->setDefinitionType("E");
        }
        if ($isList !== true) {
            $profileDto->setItemDataType("N");
            
            $numberType = array(
                'min_digits' => 1,
                'max_digits' => 2,
                "decimal_points" => 2
            );
            $profileDto->setNumberType($numberType);
        } else {
            $profileDto->setItemDataType("S");
            $category_type = array();
            $category_type[0]['answer'] = "Karen Sims";
            $category_type[0]['value'] = uniqid("val_", true);
            $category_type[0]['sequence_no'] = 1;
            
            $profileDto->setCategoryType($category_type);
        }
        
        return $profileDto;
    }

    /**
     * {@inheritDoc}
     */
    protected function _after()
    {}
}