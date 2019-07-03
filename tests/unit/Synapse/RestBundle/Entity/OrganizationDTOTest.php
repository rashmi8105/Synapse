<?php
namespace unit\Synapse\RestBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Synapse\CoreBundle\Entity\Institution;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\OrganizationDTO;


class OrganizationDTOTest extends KernelTestCase

{
    public function createOrganizationDTO()
    {
        $organization = new OrganizationDTO();
        $organization->setNickname('kavita');
        $organization->setSubdomain("nsu.map-works.com");
        $organization->setTimezone('CST');


        return $organization;



    }
    public function testsetNickname()
    {
        $organizationDTO = $this->createOrganizationDTO();

        $organizationDTOobj = new OrganizationDTO();
        $organizationDTOobj->setNickname('kavita');

        $this->assertEquals($organizationDTO->getNickname(), $organizationDTOobj->getNickname());
    }


    public function testsetSubdomain()
    {
        $organizationDTO = $this->createOrganizationDTO();

        $organizationDTOobj = new OrganizationDTO();
        $organizationDTOobj->setSubdomain("nsu.map-works.com");

        $this->assertEquals($organizationDTO->getSubdomain(), $organizationDTOobj->getSubdomain());
    }


    public function testsetTimezone()
    {
        $organizationDTO = $this->createOrganizationDTO();

        $organizationDTOobj = new OrganizationDTO();
        $organizationDTOobj->setTimezone('CST');

        $this->assertEquals($organizationDTO->getTimezone(), $organizationDTOobj->getTimezone());
    }

}