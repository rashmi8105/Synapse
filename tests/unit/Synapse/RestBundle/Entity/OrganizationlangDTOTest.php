<?php

namespace unit\Synapse\RestBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Synapse\CoreBundle\Entity\Institution;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\OrganizationlangDTO;

class OrganizationlangDTOTest extends KernelTestCase{

    public function createOrganizationlangDTO()
    {
        $organizationlang = new OrganizationlangDTO();

        $organizationlang->setOrganizationid(1);
        $organizationlang->setLangid(2);
        $organizationlang->setOrganizationname('Techm');
        $organizationlang->setNickname('tm');
        $organizationlang->setSubdomain("mahindra.ad");
        $organizationlang->setStatus('active');
        $organizationlang->setWebsite('test@test.com');

        return $organizationlang;
    }


    public function testsetOrganizationid()
    {
        $organizationlangDTO = $this->createOrganizationlangDTO();

        $organizationlangDTOobj = new OrganizationlangDTO();
        $organizationlangDTOobj->setOrganizationid(1);

        $this->assertEquals($organizationlangDTO->getOrganizationid(), $organizationlangDTOobj->getOrganizationid());
    }


    public function testsetLangid()
    {
        $organizationlangDTO = $this->createOrganizationlangDTO();

        $organizationlangDTOobj = new OrganizationlangDTO();
        $organizationlangDTOobj->setLangid(2);

        $this->assertEquals($organizationlangDTO->getLangid(), $organizationlangDTOobj->getLangid());
    }


    public function testsetOrganizationname()
    {
        $organizationlangDTO = $this->createOrganizationlangDTO();

        $organizationlangDTOobj = new OrganizationlangDTO();
        $organizationlangDTOobj->setOrganizationname('Techm');

        $this->assertEquals($organizationlangDTO->getOrganizationname(), $organizationlangDTOobj->getOrganizationname());
    }


    public function testsetSubdomain()
    {
    $organizationlangDTO = $this->createOrganizationlangDTO();

    $organizationlangDTOobj = new OrganizationlangDTO();
    $organizationlangDTOobj->setSubdomain("mahindra.ad");

    $this->assertEquals($organizationlangDTO->getSubdomain(), $organizationlangDTOobj->getSubdomain());

    }

    public function testsetStatus()
    {
        $organizationlangDTO = $this->createOrganizationlangDTO();

        $organizationlangDTOobj = new OrganizationlangDTO();
        $organizationlangDTOobj->setStatus('active');

        $this->assertEquals($organizationlangDTO->getStatus(), $organizationlangDTOobj->getStatus());
    }

    public function testsetWebsite()
    {
    $organizationlangDTO = $this->createOrganizationlangDTO();

    $organizationlangDTOobj = new OrganizationlangDTO();
    $organizationlangDTOobj->setWebsite('test@test.com');

    $this->assertEquals($organizationlangDTO->getWebsite(), $organizationlangDTOobj->getWebsite());
    }

    public function testsetNickname()
    {
        $organizationlangDTO = $this->createOrganizationlangDTO();

        $organizationlangDTOobj = new OrganizationlangDTO();
        $organizationlangDTOobj->setNickname('tm');

        $this->assertEquals($organizationlangDTO->getNickname(), $organizationlangDTOobj->getNickname());
    }




}

