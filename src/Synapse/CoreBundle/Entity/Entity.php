<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity
 *
 * @ORM\Table(name="entity")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EntityRepository")
 *
 * @JMS\ExclusionPolicy("all")
 */
class Entity extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="entity_name", type="string", length=45, nullable=false)
     * @JMS\Expose
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Person", mappedBy="entity")
     */
    private $persons;

    public function __construct() {
        $this->persons = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getPersons()
    {
        return $this->persons;
    }

    public function addPersons($person)
    {
        $this->persons[] = $person;
    }

    


}
