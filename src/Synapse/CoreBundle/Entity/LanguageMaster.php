<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * LanguageMaster
 *
 * @ORM\Table(name="language_master")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\LanguageMasterRepository")
 * 
 * @JMS\ExclusionPolicy("all")
 */
class LanguageMaster extends BaseEntity
{
    const REPOSITORY_KEY = 'SynapseCoreBundle:LanguageMaster';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * 
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="langcode", type="string", length=10, nullable=true)
     * @JMS\Expose
     */
    private $langcode;

    /**
     * @var string
     *
     * @ORM\Column(name="langdescription", type="string", length=45, nullable=true)
     * @JMS\Expose
     */
    private $langdescription;

    /**
     * @var boolean
     *
     * @ORM\Column(name="issystemdefault", type="boolean", nullable=true)
     * @JMS\Expose
     */
    private $issystemdefault;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $langdescription
     */
    public function setLangdescription($langdescription)
    {
        $this->langdescription = $langdescription;
    }

    /**
     * @return string
     */
    public function getLangdescription()
    {
        return $this->langdescription;
    }

    /**
     * @param string $langcode
     */
    public function setLangcode($langcode)
    {
        $this->langcode = $langcode;
    }

    /**
     * @return string
     */
    public function getLangcode()
    {
        return $this->langcode;
    }

    /**
     * @param boolean $issystemdefault
     */
    public function setIssystemdefault($issystemdefault)
    {
        $this->issystemdefault = $issystemdefault;
    }

    /**
     * @return boolean
     */
    public function getIssystemdefault()
    {
        return $this->issystemdefault;
    }

    
}