<?php

namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\Entity\BaseEntity;


/**
 * SuccessMarkerTopic
 *
 * @ORM\Table(name="success_marker_topic")
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\SuccessMarkerTopicRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class SuccessMarkerTopic extends BaseEntity
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="smallint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     * @JMS\Expose
     */
    private $name;

    /**
     * @var SuccessMarker
     * @ORM\ManyToOne(targetEntity="SuccessMarker")
     * @ORM\JoinColumn(name="success_marker_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $successMarker;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return SuccessMarker
     */
    public function getSuccessMarker()
    {
        return $this->successMarker;
    }

    /**
     * @param SuccessMarker $successMarker
     */
    public function setSuccessMarker($successMarker)
    {
        $this->successMarker = $successMarker;
    }

}