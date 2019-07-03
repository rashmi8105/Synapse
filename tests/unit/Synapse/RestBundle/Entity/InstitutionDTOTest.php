<?php
namespace unit\Synapse\RestBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Synapse\CoreBundle\Entity\Institution;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\InstitutionDTO;

class InstitutionDtoTest extends KernelTestCase
{
    public function createInstitutionDTO()
    {
        $institution = new InstitutionDTO();
        $institution->setSubdomain("nsu.map-works.com");
        $institution->setParentorganizationid(2);
        $institution->setStatus('A');
        $institution->setTimezone('CST');
        $institution->setWebsite("nsu.map-works.com");
        return $institution;
    }

    public function testsetSubdomain()
    {
       $institutionDTO = $this->createInstitutionDTO();

        $institutionDTOobj = new InstitutionDTO();
        $institutionDTOobj->setSubdomain("nsu.map-works.com");

        $this->assertEquals($institutionDTO->getSubdomain(), $institutionDTOobj->getSubdomain());
    }

    public function testsetParentorganizationid()
    {
        $institutionDTO = $this->createInstitutionDTO();

        $institutionDTOobj = new InstitutionDTO();
        $institutionDTOobj->setParentorganizationid(2);

        $this->assertEquals($institutionDTO->getParentorganizationid(), $institutionDTOobj->getParentorganizationid());
    }

    public function testsetStatus()
    {
        $institutionDTO = $this->createInstitutionDTO();

        $institutionDTOobj = new InstitutionDTO();
        $institutionDTOobj->setStatus('A');

        $this->assertEquals($institutionDTO->getStatus(), $institutionDTOobj->getStatus());
    }

    public function testsetTimezone()
    {
        $institutionDTO = $this->createInstitutionDTO();

        $institutionDTOobj = new InstitutionDTO();
        $institutionDTOobj->setTimezone('CST');

        $this->assertEquals($institutionDTO->getTimezone(), $institutionDTOobj->getTimezone());
    }

    public function testsetWebsite()
    {
        $institutionDTO = $this->createInstitutionDTO();

        $institutionDTOobj = new InstitutionDTO();
        $institutionDTOobj->setWebsite("nsu.map-works.com");

        $this->assertEquals($institutionDTO->getWebsite(), $institutionDTOobj->getWebsite());
    }
}