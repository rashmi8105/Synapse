<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for talking points
 *
 * @package Synapse\RestBundle\Entity
 */
class TalkingPointsDto
{

    /**
     * talkingPointId
     * 
     * @var integer @JMS\Type("integer")
     *     
     */
    private $talkingPointId;

    /**
     * @JMS\Type("DateTime")
     * 
     * @var DateTime
     */
    private $talkingPointDate;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $title;

    /**
     * @JMS\Type("string")
     * 
     * @var string
     */
    private $description;

    /**
     *
     * @param integer $talkingPointId            
     */
    public function setTalkingPointId($talkingPointId)
    {
        $this->talkingPointId = $talkingPointId;
    }

    /**
     *
     * @return int
     */
    public function getTalkingPointId()
    {
        return $this->talkingPointId;
    }

    /**
     *
     * @param \Synapse\RestBundle\Entity\DateTime $talkingPointDate            
     */
    public function setTalkingPointDate($talkingPointDate)
    {
        $this->talkingPointDate = $talkingPointDate;
    }

    /**
     *
     * @return \Synapse\RestBundle\Entity\DateTime
     */
    public function getTalkingPointDate()
    {
        return $this->talkingPointDate;
    }

    /**
     *
     * @param string $title            
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param string $description            
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}