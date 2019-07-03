<?php

namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * QuestionBank
 *
 * @ORM\Table(name="question_bank")
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\QuestionBankRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class QuestionBank extends BaseEntity
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="intro_text", type="text", nullable=true)
     * @JMS\Expose
     */
    private $introText;

    /**
     * @var string
     * @ORM\Column(name="text", type="text", nullable=false)
     * @JMS\Expose
     */
    private $text;

    /**
     * @var string
     * @ORM\Column(name="question_type", type="string", nullable=false)
     * @JMS\Expose
     */
    private $questionType;

    /**
     * @var boolean
     * @ORM\Column(name="on_success_marker_page", type="boolean", nullable=false)
     * @JMS\Expose
     */
    private $onSuccessMarkerPage;


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
    public function getIntroText()
    {
        return $this->introText;
    }

    /**
     * @param string $introText
     */
    public function setIntroText($introText)
    {
        $this->introText = $introText;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getQuestionType()
    {
        return $this->questionType;
    }

    /**
     * @param string $questionType
     */
    public function setQuestionType($questionType)
    {
        $this->questionType = $questionType;
    }

    /**
     * @return boolean
     */
    public function isOnSuccessMarkerPage()
    {
        return $this->onSuccessMarkerPage;
    }

    /**
     * @param boolean $onSuccessMarkerPage
     */
    public function setOnSuccessMarkerPage($onSuccessMarkerPage)
    {
        $this->onSuccessMarkerPage = $onSuccessMarkerPage;
    }

}