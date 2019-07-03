<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class SurveyQuestionsDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * factorName
     *
     * @var integer @JMS\Type("string")
     *     
     */
    private $rptText;

    /**
     *
     * @param integer $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $rptText            
     */
    public function setRptText($rptText)
    {
        $this->rptText = $rptText;
    }

    /**
     *
     * @return string
     */
    public function getRptText()
    {
        return $this->rptText;
    }

    /**
     *
     * @param integer $sequence            
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     *
     * @return integer
     */
    public function getSequence()
    {
        return $this->sequence;
    }
}