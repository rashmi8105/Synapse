<?php
use Codeception\Util\Stub;
use Synapse\RestBundle\Entity\ProfileDto;
use Synapse\RestBundle\Entity\ReOrderProfileDto;

class ProfileServiceTest extends \Codeception\TestCase\Test
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
    
     /**
     * {@inheritDoc}
     */
    public function _before()
    {
        $this->container = $this->getModule('Symfony2')->kernel->getContainer();
        $this->profileService = $this->container->get('profile_service');
    }
 
    public function testCreateEbiProfileNumType()
    {
        $profileDto = $this->createProfileDto("E");
        $profile = $this->profileService->createProfile($profileDto);
        $this->assertEquals('E', $profile->getDefinitionType());
        $this->assertEquals('N', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
        
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testCreateEbiProfileDuplicate()
    {
        $profileDto = $this->createProfileDto("E");
        $profileDto->setItemLabel("AASS");
        $profile = $this->profileService->createProfile($profileDto);
        $profileDto->setItemLabel("AASS");
        $profile = $this->profileService->createProfile($profileDto);
        
    
    }

    public function testCreateEbiProfileListType()
    {
        $profileDto = $this->createProfileDto("E", true);
        $profile = $this->profileService->createProfile($profileDto);
        $this->assertEquals('E', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
       
    }
    
    public function testUpdateEbiProfileNumTypeToList()
    {
        $profileDto = $this->createProfileDto("E");
        $profile = $this->profileService->createProfile($profileDto);
        $profileDto = $this->createProfileDto("E", true);
        $profileDto->setId($profile->getId());
        $profile = $this->profileService->updateProfile($profileDto);
        $this->assertEquals('E', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals(0, $profile->getNumberType()['min_digits']);
        $this->assertEquals(0, $profile->getNumberType()['max_digits']);
        $this->assertEquals(0, $profile->getNumberType()['decimal_points']);

    }

    public function testUpdateEbiProfileListToNum()
    {
        $profileDto = $this->createProfileDto("E", true);
        $profile = $this->profileService->createProfile($profileDto);
        $profileDto = $this->createProfileDto("E");
        $profileDto->setId($profile->getId());
        $profile = $this->profileService->updateProfile($profileDto);
        $this->assertEquals('E', $profile->getDefinitionType());
        $this->assertEquals('N', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);

    }

    public function testUpdateEbiProfileListToList()
    {
        $profileDto = $this->createProfileDto("E", true);
        $profile = $this->profileService->createProfile($profileDto);
        $profileDto = $this->createProfileDtoOneList("E", true);
        $profileDto->setId($profile->getId());
        $profile = $this->profileService->updateProfile($profileDto);
        $this->assertEquals('E', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
    }

  
    public function testUpdateEbiProfileListToListSame()
    {
        $profileDto = $this->createProfileDto("E", true);
        $profile = $this->profileService->createProfile($profileDto);
        $profileDto = $this->createProfileDto("E", true);
        $profileDto->setId($profile->getId());
        $profile = $this->profileService->updateProfile($profileDto);
        $this->assertEquals('E', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'],$profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
      }

    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateProfileInvalidLang()
    {
    	$profileDto = $this->createProfileDto("O", true);
    	$profileDto->setLangId(-1);
    	$profile = $this->profileService->updateProfile($profileDto);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateProfileInvalidId()
    {
    	$profileDto = $this->createProfileDto("O", true);
    	$profileDto->setId(-1);
    	$profile = $this->profileService->updateProfile($profileDto);
    }
    
    /**
     * @expectedException Synapse\RestBundle\Exception\ValidationException
     */
    public function testUpdateProfileInvalidIdAndLang()
    {
    	$profileDto = $this->createProfileDto("O", true);
    	$profileDto->setId(-1);
    	$profileDto->setLangId(-1);
    	$profile = $this->profileService->updateProfile($profileDto);
    }
    
    public function testDeteleEbiProfile()
    {
        $profileDto = $this->createProfileDto("E", true);
        $seqArray = array();
        $id = array();
        for ($count = 0; $count < 10; $count ++) {
            $profileDto = $this->createProfileDto("E", true);
            $profile = $this->profileService->createProfile($profileDto);
          
            $seqArray[] = $profileDto->getSequenceNo();
            $id[] = $profile->getId();
        }
        $profile = $this->profileService->deleteProfile($id[5]);
        $this->assertEquals('E', $profile->getDefinitionType());
        $this->assertAttributeNotEquals('NULL', 'deletedAt', $profile);
    }

   public function testGetEbiProfiles()
    
    {
       
        $seqArray = array();
        $id = array();
        for ($count = 0; $count < 10; $count ++) {
            $profileDto = $this->createProfileDto("E", true);
            $profile = $this->profileService->createProfile($profileDto);
            $seqArray[] = $profileDto->getSequenceNo();
            $id[] = $profile->getId();
        }
        $profiles = $this->profileService->getProfiles("E", 0);
        $this->assertArrayHasKey('item_label', $profiles['profile_items'][0]);
        $this->assertArrayHasKey('item_subtext', $profiles['profile_items'][0]);
        $this->assertArrayHasKey('item_data_type', $profiles['profile_items'][0]);
        $this->assertArrayHasKey('id', $profiles['profile_items'][0]);
        $this->assertInternalType('array', $profiles);
    }

    public function testGetEbiProfile()    
    {
        $profileDto = $this->createProfileDto("E");
        $profile = $this->profileService->createProfile($profileDto);        
        $profiles = $this->profileService->getProfile($profile->getId());
        $this->assertEquals('E', $profile->getDefinitionType());
        $this->assertEquals($profile->getId(), $profiles->getId());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);

    }
    
    public function testGetProfileInvalid()
    {
    	$profiles = $this->profileService->getProfile(-1);
    }

    public function testReorderEbiProfileToLow()
    {
        $profileDto = $this->createProfileDto("E", true);
        $seqArray = array();
        $id = array();
        for ($count = 0; $count < 10; $count ++) {
            $profileDto->setItemLabel('AAA_' . $count);
            $profile = $this->profileService->createProfile($profileDto);
            
            $seqArray[] = $profile->getSequenceNo();
            $id[] = $profile->getId();
        }
        $profiles = $this->profileService->reorderProfile($this->createReorderDto($id[7], $seqArray[3]));
        $profile = $this->profileService->getProfile( $id[7]);
        $this->assertEquals($id[7], $profile->getId());
        $this->assertEquals('E', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
        
    }
	
    public function testReorderEbiProfileToHigh()
    
    {
         $profileDto = $this->createProfileDto("E", true);
        $seqArray = array();
        $id = array();
        for ($count = 0; $count < 10; $count ++) {
            $profileDto->setItemLabel('AAA_'.$count);
            $profile = $this->profileService->createProfile($profileDto);
           
            $seqArray[] = $profile->getSequenceNo();
            $id[] = $profile->getId();
        }
      $profiles = $this->profileService->reorderProfile($this->createReorderDto($id[7], $seqArray[3]));
        $profile = $this->profileService->getProfile($id[7]);
        $this->assertEquals($id[7] , $profile->getId());
        $this->assertEquals('E', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);
      
    }

    public function testReorderEbiProfileToOut()
    {
        $seqArray = array();
        $id = array();
        for ($count = 0; $count < 10; $count ++) {
            $profileDto = $this->createProfileDto("E", true);
            $profileDto->setItemLabel('AAA_'.$count);
            $profile = $this->profileService->createProfile($profileDto);
           
            $seqArray[] = $profile->getSequenceNo();
            $id[] = $profile->getId();
        }
        $profiles = $this->profileService->reorderProfile($this->createReorderDto($id[7], 9999));
        
        $profile = $this->profileService->getProfile( $id[7]);
        $this->assertEquals($id[7] , $profile->getId());
        $this->assertEquals('E', $profile->getDefinitionType());
        $this->assertEquals('S', $profile->getItemDataType());
        $this->assertEquals($profileDto->getNumberType()['min_digits'], $profile->getNumberType()['min_digits']);
        $this->assertEquals($profileDto->getNumberType()['max_digits'], $profile->getNumberType()['max_digits']);
        $this->assertEquals($profileDto->getNumberType()['decimal_points'], $profile->getNumberType()['decimal_points']);

     }
    
	
    public function createProfileDto($type, $isList = false)
    {
        $profileDto = new ProfileDto();
        $profileDto->setLangId(1);
        $profileDto->setItemLabel("Test Label" . uniqid("key_",true));
        $profileDto->setItemSubtext("Test Label Subtext");
        $profileDto->setDefinitionType("E");
        $profileDto->setSequenceNo("1");
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
            $category_type[0]['value'] = uniqid("val_",true);
            $category_type[0]['sequence_no'] = 1;
            
            $category_type[1]['answer'] = "Karen Sims 2";
            $category_type[1]['value'] = uniqid("val_",true);
            $category_type[1]['sequence_no'] = 2;
            $profileDto->setCategoryType($category_type);
        }
        
        return $profileDto;
    }
	
    public function createReorderDto($id,$seq)
    {
    	$dto = new ReOrderProfileDto();
    	$dto->setId($id);
    	$dto->setSequenceNo($seq);
    	
    	return $dto;
    }
    
    
    public function createProfileDtoOneList($type, $isList = false)
    {
        $profileDto = new ProfileDto();
        $profileDto->setLangId(1);
        $profileDto->setItemLabel("Test Label" . strrev(time()));
        $profileDto->setItemSubtext("Test Label Subtext");
        $profileDto->setDefinitionType("E");
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
            $category_type[0]['value'] = uniqid("val_",true);
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