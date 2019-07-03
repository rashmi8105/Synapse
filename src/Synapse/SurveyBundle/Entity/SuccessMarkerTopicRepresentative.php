<?php

namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\Entity\BaseEntity;


/**
 * SuccessMarkerTopicRepresentative
 *
 * @ORM\Table(name="success_marker_topic_representative")
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class SuccessMarkerTopicRepresentative extends BaseEntity
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
     * @var SuccessMarkerTopic
     * @ORM\OneToOne(targetEntity="SuccessMarkerTopic")
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id", nullable=false, unique=true)
     * @JMS\Expose
     */
    private $topic;

    /**
     * @var SuccessMarkerTopicDetail
     * @ORM\OneToOne(targetEntity="SuccessMarkerTopicDetail")
     * @ORM\JoinColumn(name="representative_detail_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $representativeDetail;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return SuccessMarkerTopic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param SuccessMarkerTopic $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * @return SuccessMarkerTopicDetail
     */
    public function getRepresentativeDetail()
    {
        return $this->representativeDetail;
    }

    /**
     * @param SuccessMarkerTopicDetail $representativeDetail
     */
    public function setRepresentativeDetail($representativeDetail)
    {
        $this->representativeDetail = $representativeDetail;
    }

}