<?php

namespace unit\Synapse\RestBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Synapse\CoreBundle\Entity\Institution;
use Synapse\RestBundle\Entity\Error;
use Synapse\RestBundle\Entity\RolelangDTO;

class RolelangDTOTest extends KernelTestCase
{
    public function createRolelangDTO()
    {

        $rolelang = new RolelangDTO();
        $rolelang->setRolename(1);
        $rolelang->setLangid(1);
        $rolelang->setRoleid(2);

        return $rolelang;
     }

    public function testsetRolename()
    {
        $rolelangDTO = $this->createRolelangDTO();

        $rolelangDTOobj = new RolelangDTO();
        $rolelangDTOobj->setRolename(1);

        $this->assertEquals($rolelangDTO->getRolename(), $rolelangDTOobj->getRolename());
    }

    public function testsetLangid()
    {
        $rolelangDTO = $this->createRolelangDTO();

        $rolelangDTOobj = new RolelangDTO();
        $rolelangDTOobj->setLangid(1);

        $this->assertEquals($rolelangDTO->getLangid(), $rolelangDTOobj->getLangid());
    }
    public function testsetRoleid()
    {
        $rolelangDTO = $this->createRolelangDTO();

        $rolelangDTOobj = new RolelangDTO();
        $rolelangDTOobj->setRoleid(2);

        $this->assertEquals($rolelangDTO->getRoleid(), $rolelangDTOobj->getRoleid());
    }
}