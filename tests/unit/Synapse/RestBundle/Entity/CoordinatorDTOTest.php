<?php

namespace unit\Synapse\RestBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Synapse\CoreBundle\Entity\Institution;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\CoordinatorDTO;

class CoordinatorDTOTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function createCoordinatorDTO()
    {
        $coordinator = new CoordinatorDTO();
        $coordinator->setFirstname('kavi');
        $coordinator->setTitle('admin');
        $coordinator->setRoleid(1);
        $coordinator->setEmail('test@test.com');
        $coordinator->setIsmobile("true");
        $coordinator->setPhone(987456321);
        $coordinator->setOrganizationid(123);

        return $coordinator;

    }

    public function testsetFirstName()
    {
        $coordinatorDTO = $this->createCoordinatorDTO();

        $coordinatorDTOobj = new CoordinatorDTO();
        $coordinatorDTOobj->setFirstname('kavi');

        $this->assertEquals($coordinatorDTO->getFirstname(), $coordinatorDTOobj->getFirstname());
    }

    public function testsetTitle()
    {
        $coordinatorDTO = $this->createCoordinatorDTO();

        $coordinatorDTOobj = new CoordinatorDTO();
        $coordinatorDTOobj->setTitle('admin');

        $this->assertEquals($coordinatorDTO->getTitle(), $coordinatorDTOobj->getTitle());
    }

    public function testsetRoleid()
    {
        $coordinatorDTO = $this->createCoordinatorDTO();

        $coordinatorDTOobj = new CoordinatorDTO();
        $coordinatorDTOobj->setRoleid(1);

        $this->assertEquals($coordinatorDTO->getRoleid(), $coordinatorDTOobj->getRoleid());
    }

    public function testSetEmail()
    {
        $coordinatorDTO = $this->createCoordinatorDTO();

        $coordinatorDTOobj = new CoordinatorDTO();
        $coordinatorDTOobj->setEmail('test@test.com');

        $this->assertEquals($coordinatorDTO->getEmail(), $coordinatorDTOobj->getEmail());
    }

    public function testsetIsmobile()
    {
        $coordinatorDTO = $this->createCoordinatorDTO();

        $coordinatorDTOobj = new CoordinatorDTO();
        $coordinatorDTOobj->setIsmobile("true");

        $this->assertEquals($coordinatorDTO->getIsmobile(), $coordinatorDTOobj->getIsmobile());
    }
    public function testsetPhone()
    {
        $coordinatorDTO = $this->createCoordinatorDTO();

        $coordinatorDTOobj = new CoordinatorDTO();
        $coordinatorDTOobj->setPhone(987456321);

        $this->assertEquals($coordinatorDTO->getPhone(), $coordinatorDTOobj->getPhone());
    }
    public function testsetOrganizationid()
    {
        $coordinatorDTO = $this->createCoordinatorDTO();

        $coordinatorDTOobj = new CoordinatorDTO();
        $coordinatorDTOobj->setOrganizationid(123);

        $this->assertEquals($coordinatorDTO->getOrganizationid(), $coordinatorDTOobj->getOrganizationid());
    }

}