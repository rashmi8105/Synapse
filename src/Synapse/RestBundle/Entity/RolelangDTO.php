<?php
namespace Synapse\RestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object
 *
 * @package Synapse\RestBundle\Entity
 */
class RolelangDTO
{

    /**
     * Role Lang Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $rolelangid;

    /**
     * Rolename
     * 
     * @var string @JMS\Type("string")
     */
    private $rolename;

    /**
     * language Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $langid;

    /**
     * Role Id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $roleid;

    /**
     *
     * @param int $langid            
     */
    public function setLangid($langid)
    {
        $this->langid = $langid;
    }

    /**
     *
     * @return int
     */
    public function getLangid()
    {
        return $this->langid;
    }

    /**
     *
     * @param string $rolename            
     */
    public function setRolename($rolename)
    {
        $this->rolename = $rolename;
    }

    /**
     *
     * @return string
     */
    public function getRolename()
    {
        return $this->rolename;
    }

    /**
     *
     * @param int $rolelangid            
     */
    public function setRolelangid($rolelangid)
    {
        $this->rolelangid = $rolelangid;
    }

    /**
     *
     * @return int
     */
    public function getRolelangid()
    {
        return $this->rolelangid;
    }

    /**
     *
     * @param int $roleid            
     */
    public function setRoleid($roleid)
    {
        $this->roleid = $roleid;
    }

    /**
     *
     * @return int
     */
    public function getRoleid()
    {
        return $this->roleid;
    }
}
