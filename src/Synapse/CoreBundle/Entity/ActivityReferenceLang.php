<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * ActivityReferenceLang
 *
 * @ORM\Table(name="activity_reference_lang", indexes={@ORM\Index(name="fk_activity_reference_lang_activity_reference1", columns={"activity_reference_id"}), @ORM\Index(name="fk_activity_reference_lang_language_master1", columns={"language_master_id"})})
 * @ORM\Entity
 */
class ActivityReferenceLang
{
    /**
     * @var string
     *
     * @ORM\Column(name="heading", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $heading;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\LanguageMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\LanguageMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="language_master_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $languageMaster;

    /**
     * @var \Synapse\CoreBundle\Entity\ActivityReference
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ActivityReference")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activity_reference_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $activityReference;



    /**
     * Set heading
     *
     * @param string $heading
     * @return ActivityReferenceLang
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * Get heading
     *
     * @return string 
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ActivityReferenceLang
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
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
     * Set languageMaster
     *
     * @param \Synapse\CoreBundle\Entity\LanguageMaster $languageMaster
     * @return ActivityReferenceLang
     */
    public function setLanguageMaster(\Synapse\CoreBundle\Entity\LanguageMaster $languageMaster = null)
    {
        $this->languageMaster = $languageMaster;

        return $this;
    }

    /**
     * Get languageMaster
     *
     * @return \Synapse\CoreBundle\Entity\LanguageMaster 
     */
    public function getLanguageMaster()
    {
        return $this->languageMaster;
    }

    /**
     * Set activityReference
     *
     * @param \Synapse\CoreBundle\Entity\ActivityReference $activityReference
     * @return ActivityReferenceLang
     */
    public function setActivityReference(\Synapse\CoreBundle\Entity\ActivityReference $activityReference = null)
    {
        $this->activityReference = $activityReference;

        return $this;
    }

    /**
     * Get activityReference
     *
     * @return \Synapse\CoreBundle\Entity\ActivityReference 
     */
    public function getActivityReference()
    {
        return $this->activityReference;
    }
}
