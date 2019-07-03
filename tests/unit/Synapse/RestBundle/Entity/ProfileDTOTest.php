<?php

namespace unit\Synapse\RestBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Synapse\CoreBundle\Entity\Institution;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\ProfileDto;

class ProfileDTOTest extends KernelTestCase
{
    public function createProfileDTO()
    {
        $profile = new ProfileDto();
        $profile->setId(1);
        $profile->setDefinitiontype("abc");
        $profile->setItemDataType(1);
        $profile->setDecimalPoints(5);
        $profile->setIsrequired(1);
        $profile->setMinDigits(5);
        $profile->setMaxDigits(100);
        $profile->setOrganizationId("xyz");
        $profile->setItemSubtext("excellent");
        $profile->setItemLabel(1);
        $profile->setLangId(10);
        $profile->setListName("list");
        $profile->setListValue([1,2,3]);
		$profile->setCategoryType('a','b','c');
		$profile->setNumberType([1,2]);
        $profile->setSequenceNo(11);


        return $profile;

    }
    public function testsetId()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setId(1);

        $this->assertEquals($profileDTO->getId(), $profileDTOobj->getId());
    }
    public function testsetDefinitiontype()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setDefinitiontype("abc");

        $this->assertEquals($profileDTO->getDefinitiontype(), $profileDTOobj->getDefinitiontype());
    }
    public function testsetItemDataType()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setItemDataType(1);

        $this->assertEquals($profileDTO->getItemDataType(), $profileDTOobj->getItemDataType());
    }
    public function testsetDecimalPoints()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setDecimalPoints('5');

        $this->assertEquals($profileDTO->getDecimalPoints(), $profileDTOobj->getDecimalPoints());
    }
    public function testsetIsRequired()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setIsRequired(1);

        $this->assertEquals($profileDTO->getIsRequired(), $profileDTOobj->getIsRequired());
    }
    public function testsetMinDigits()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setMinDigits(5);

        $this->assertEquals($profileDTO->getMinDigits(), $profileDTOobj->getMinDigits());
    }
    public function testsetMaxDigits()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setMaxDigits(100);

        $this->assertEquals($profileDTO->getMaxDigits(), $profileDTOobj->getMaxDigits());
    }
    public function testsetOrganizationId()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setOrganizationId("xyz");

        $this->assertEquals($profileDTO->getOrganizationId(), $profileDTOobj->getOrganizationId());
    }
    public function testsetItemSubtext()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setItemSubtext("excellent");

        $this->assertEquals($profileDTO->getItemSubtext(), $profileDTOobj->getItemSubtext());
    }
    public function testsetItemLabel()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setItemLabel(1);

        $this->assertEquals($profileDTO->getItemLabel(), $profileDTOobj->getItemLabel());
    }
    public function testsetLangId()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setLangId(10);

        $this->assertEquals($profileDTO->getLangId(), $profileDTOobj->getLangId());
    }
    public function testsetListName()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setListName("list");

        $this->assertEquals($profileDTO->getListName(), $profileDTOobj->getListName());
    }
    public function testsetListValue()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setListValue([1,2,3]);

        $this->assertEquals($profileDTO->getListValue(), $profileDTOobj->getListValue());
    }
    public function testsetSequenceNo()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setSequenceNo(11);

        $this->assertEquals($profileDTO->getSequenceNo(), $profileDTOobj->getSequenceNo());
    }
	 public function tetsetCategoryType()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setCategoryType(['a','b','c']);

        $this->assertEquals($profileDTO->getCategoryType(), $profileDTOobj->getCategoryType());
    }
	public function tetsetNumberType()
    {
        $profileDTO = $this->createProfileDTO();

        $profileDTOobj = new ProfileDto();
        $profileDTOobj->setNumberType(3);

        $this->assertEquals($profileDTO->getNumberType(), $profileDTOobj->getNumberType());
    }

}