<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table(name="role")
 * @ORM\Entity
 */
class Role extends BaseEntity
{
    // Set in the role table.
    const ROLE_PRIMARY_COORDINATOR = 1;
    const ROLE_TECHNICAL_COORDINATOR = 2;
    const ROLE_NONTECHNICAL_COORDINATOR = 3;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=1, nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * DTO property
     *
     * @var string */
    private $name;

    /**
     * Set status
     *
     * @param string $status
     * @return Role
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Helper function for RoleMasterLang name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Helper function for RoleMasterLang name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public static function getCoordinatorIds()
    {
        return [
            self::ROLE_PRIMARY_COORDINATOR,
            self::ROLE_TECHNICAL_COORDINATOR,
            self::ROLE_NONTECHNICAL_COORDINATOR,
        ];
    }
}
