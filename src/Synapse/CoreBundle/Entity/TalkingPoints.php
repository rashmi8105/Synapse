<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * TalkingPoints
 *
 * @ORM\Table(name="talking_points", indexes={@ORM\Index(name="fk_talking_points_ebi_question1_idx", columns={"ebi_question_id"}), @ORM\Index(name="fk_talking_points_ebi_metadata1_idx", columns={"ebi_metadata_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\TalkingPointsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class TalkingPoints extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="type", type="string", length=1, nullable=true)
     */
    private $type;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\EbiQuestion @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiQuestion")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_question_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $ebiQuestion;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\EbiMetadata @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\EbiMetadata")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_metadata_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $ebiMetadata;

    /**
     *
     * @var string @ORM\Column(name="talking_points_type", type="string", length=1, nullable=true)
     */
    private $talkingPointsType;

    /**
     *
     * @var integer @ORM\Column(name="min_range", type="integer", nullable=true)
     *      @JMS\Expose
     */
    private $minRange;

    /**
     *
     * @var integer @ORM\Column(name="max_range", type="integer", nullable=true)
     *      @JMS\Expose
     */
    private $maxRange;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type            
     * @return TalkingPoints
     */
    public function setType($type)
    {
        $this->type = $type;
        
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set ebiQuestion
     *
     * @param \Synapse\CoreBundle\Entity\EbiQuestion $ebiQuestion            
     * @return TalkingPoints
     */
    public function setEbiQuestion(\Synapse\CoreBundle\Entity\EbiQuestion $ebiQuestion = null)
    {
        $this->ebiQuestion = $ebiQuestion;
        
        return $this;
    }

    /**
     * Get ebiQuestion
     *
     * @return \Synapse\CoreBundle\Entity\EbiQuestion
     */
    public function getEbiQuestion()
    {
        return $this->ebiQuestion;
    }

    /**
     * Set ebiMetadata
     *
     * @param \Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata            
     * @return TalkingPoints
     */
    public function setEbiMetadata(\Synapse\CoreBundle\Entity\EbiMetadata $ebiMetadata = null)
    {
        $this->ebiMetadata = $ebiMetadata;
        
        return $this;
    }

    /**
     * Get ebiMetadata
     *
     * @return \Synapse\CoreBundle\Entity\EbiMetadata
     */
    public function getEbiMetadata()
    {
        return $this->ebiMetadata;
    }

    /**
     * Set talkingPointsType
     *
     * @param string $talkingPointsType            
     * @return TalkingPoints
     */
    public function setTalkingPointsType($talkingPointsType)
    {
        $this->talkingPointsType = $talkingPointsType;
        
        return $this;
    }

    /**
     * Get talkingPointsType
     *
     * @return string
     */
    public function getTalkingPointsType()
    {
        return $this->talkingPointsType;
    }

    /**
     * Set minRange
     *
     * @param integer $minRange            
     * @return TalkingPoints
     */
    public function setMinRange($minRange)
    {
        $this->minRange = $minRange;
        
        return $this;
    }

    /**
     * Get minRange
     *
     * @return integer
     */
    public function getMinRange()
    {
        return $this->minRange;
    }

    /**
     * Set maxRange
     *
     * @param integer $maxRange            
     * @return TalkingPoints
     */
    public function setMaxRange($maxRange)
    {
        $this->maxRange = $maxRange;
        
        return $this;
    }

    /**
     * Get maxRange
     *
     * @return integer
     */
    public function getMaxRange()
    {
        return $this->maxRange;
    }
}