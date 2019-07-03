<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EbiQuestion
 *
 * @ORM\Table(name="ebi_question", indexes={@ORM\Index(name="fk_ebi_question_question_type1_idx", columns={"question_type_id"}), @ORM\Index(name="fk_ebi_question_question_category1_idx", columns={"question_category_id"})})
 * @ORM\Entity (repositoryClass="Synapse\CoreBundle\Repository\EbiQuestionRepository")
 *
 */
class EbiQuestion extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="question_key", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    private $questionKey;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\QuestionType @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\QuestionType")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="question_type_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $questionType;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\QuestionCategory @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\QuestionCategory")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="question_category_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $questionCategory;

    /**
     *
     * @var boolean @ORM\Column(name="has_other", type="boolean", nullable=true)
     */
    private $hasOther;

    /**
     *
     * @var string @ORM\Column(name="external_id", type="string", length=45, nullable=true)
     */
    private $externalId;

    /**
     * @var boolean @ORM\Column(name="on_success_marker_page", type="boolean", nullable=true)
     */
    private $onSuccessMarkerPage;


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
     * Set questionKey
     *
     * @param string $questionKey            
     * @return EbiQuestion
     */
    public function setQuestionKey($questionKey)
    {
        $this->questionKey = $questionKey;
        
        return $this;
    }

    /**
     * Get questionKey
     *
     * @return string
     */
    public function getQuestionKey()
    {
        return $this->questionKey;
    }

    /**
     * Set questionType
     *
     * @param \Synapse\CoreBundle\Entity\QuestionType $questionType            
     * @return EbiQuestion
     */
    public function setQuestionType(\Synapse\CoreBundle\Entity\QuestionType $questionType = null)
    {
        $this->questionType = $questionType;
        
        return $this;
    }

    /**
     * Get questionType
     *
     * @return \Synapse\CoreBundle\Entity\QuestionType
     */
    public function getQuestionType()
    {
        return $this->questionType;
    }

    /**
     * Set questionCategory
     *
     * @param \Synapse\CoreBundle\Entity\QuestionCategory $questionCategory            
     * @return EbiQuestion
     */
    public function setQuestionCategory(\Synapse\CoreBundle\Entity\QuestionCategory $questionCategory = null)
    {
        $this->questionCategory = $questionCategory;
        
        return $this;
    }

    /**
     * Get questionCategory
     *
     * @return \Synapse\CoreBundle\Entity\QuestionCategory
     */
    public function getQuestionCategory()
    {
        return $this->questionCategory;
    }

    /**
     * Set hasOther
     *
     * @param boolean $hasOther            
     * @return EbiQuestion
     */
    public function setHasOther($hasOther)
    {
        $this->hasOther = $hasOther;
        
        return $this;
    }

    /**
     * Get hasOther
     *
     * @return boolean
     */
    public function getHasOther()
    {
        return $this->hasOther;
    }

    /**
     * Set externalId
     *
     * @param string $externalId            
     * @return EbiQuestion
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        
        return $this;
    }

    /**
     * Get externalId
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param boolean $onSuccessMarkerPage
     */
    public function setOnSuccessMarkerPage($onSuccessMarkerPage)
    {
        $this->onSuccessMarkerPage = $onSuccessMarkerPage;
    }

    /**
     * @return boolean
     */
    public function isOnSuccessMarkerPage()
    {
        return $this->onSuccessMarkerPage;
    }

}
