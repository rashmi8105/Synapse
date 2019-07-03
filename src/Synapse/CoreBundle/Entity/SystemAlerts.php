<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
/**
 * SystemAlerts
 *
 * @ORM\Table(name="system_alerts")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\SystemAlertRepository");
 * @JMS\ExclusionPolicy("all")
 */
class SystemAlerts extends BaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=200, nullable=true)
     * @JMS\Expose
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=5000, nullable=true)
     * @JMS\Expose
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     * @JMS\Expose
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     * @JMS\Expose
     */
    private $endDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_enabled", type="boolean", length=1, nullable=true)
     * @JMS\Expose
     */
    private $isEnabled;

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
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $person;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $organization;



    /**
     * Set title
     *
     * @param string $title
     * @return SystemAlerts
     * 
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return SystemAlerts
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
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return SystemAlerts
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return SystemAlerts
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set isEnabled
     *
     * @param string $isEnabled
     * @return SystemAlerts
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return string 
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
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
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person
     * @return SystemAlerts
     */
    public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Synapse\CoreBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return SystemAlerts
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization 
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}
