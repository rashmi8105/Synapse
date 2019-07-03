<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * ContactTypesLang
 *
 * @ORM\Table(name="contact_types_lang", indexes={@ORM\Index(name="fk_contact_types_lang_contact_types1_idx", columns={"contact_types_id"}), @ORM\Index(name="fk_contact_types_lang_language_master1_idx", columns={"language_master_id"})})
 * @ORM\Entity
 */
class ContactTypesLang extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    

    /**
     * @var \Synapse\CoreBundle\Entity\ContactTypes
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ContactTypes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_types_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $contactTypesId;
    
    /**
     * @var \Synapse\CoreBundle\Entity\LanguageMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="language_master_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $languageMasterId;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     */
    private $description;

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
     * @param \Synapse\CoreBundle\Entity\ContactTypes $contactTypesId
     */
    public function setContactTypesId($contactTypesId)
    {
        $this->contactTypesId = $contactTypesId;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\ContactTypes
     */
    public function getContactTypesId()
    {
        return $this->contactTypesId;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $languageMasterId
     */
    public function setLanguageMasterId($languageMasterId)
    {
        $this->languageMasterId = $languageMasterId;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\LanguageMaster
     */
    public function getLanguageMasterId()
    {
        return $this->languageMasterId;
    }

}
