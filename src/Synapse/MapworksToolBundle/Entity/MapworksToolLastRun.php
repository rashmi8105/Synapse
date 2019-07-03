<?php

namespace Synapse\MapworksToolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\MapworksToolBundle\Entity\MapworksTool;


/**
 * MapworksToolLastRun
 *
 * @ORM\Table(name="mapworks_tool_last_run")
 * @ORM\Entity(repositoryClass="Synapse\MapworksToolBundle\Repository\MapworksToolLastRunRepository")
 */
class MapworksToolLastRun extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MapworksTool")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="tool_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $toolId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $personId;

    /**
     * @var \Date
     * @ORM\Column(name="last_run", type="datetime", nullable=true)
     */
    private $lastRun;

    /**
     * Gets the id.
     *
     * @return string @JMS\Type("integer")
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the toolId.
     *
     * @return string @JMS\Type("integer")
     */
    public function getToolId()
    {
        return $this->toolId;
    }

    /**
     * Sets the toolId.
     *
     * @param string @JMS\Type("integer") $toolId the toolId
     */
    public function setToolId($toolId)
    {
        $this->toolId = $toolId;
    }

    /**
     * Get personId
     *
     * @return string @JMS\Type("integer")
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Set personId
     *
     * @param Person $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * @return \DateTime
     */
    public function getLastRun()
    {
        return $this->lastRun;
    }

    /**
     * @param \DateTime $lastRun
     */
    public function setLastRun($lastRun)
    {
        $this->lastRun = $lastRun;
    }

}