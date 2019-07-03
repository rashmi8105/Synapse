<?php

namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Entity\EbiQuestion;


/**
 * SuccessMarkerTopicDetail
 *
 * @ORM\Table(name="success_marker_topic_detail")
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\SuccessMarkerTopicDetailRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class SuccessMarkerTopicDetail extends BaseEntity
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
     * @ORM\ManyToOne(targetEntity="SuccessMarkerTopic")
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $topic;

    /**
     * @var Factor
     * @ORM\ManyToOne(targetEntity="Factor")
     * @ORM\JoinColumn(name="factor_id", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     */
    private $factor;

    /**
     * @var EbiQuestion
     * @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\EbiQuestion")
     * @ORM\JoinColumn(name="ebi_question_id", referencedColumnName="id", nullable=true)
     * @JMS\Expose
     */
    private $ebiQuestion;


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
     * @return Factor
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * @param Factor $factor
     */
    public function setFactor($factor)
    {
        $this->factor = $factor;
    }

    /**
     * @return EbiQuestion
     */
    public function getEbiQuestion()
    {
        return $this->ebiQuestion;
    }

    /**
     * @param EbiQuestion $ebiQuestion
     */
    public function setEbiQuestion($ebiQuestion)
    {
        $this->ebiQuestion = $ebiQuestion;
    }

}