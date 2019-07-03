<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class FactorListDto
{

    /**
     * totalcount
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalCount;

    /**
     * langId
     *
     * @var integer @JMS\Type("integer")
     */
    private $langId;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\SurveyBundle\EntityDto\FactorsArrayDto>")
     *     
     */
    private $factors;

    /**
     *
     * @param integer $totalCount            
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;
    }

    /**
     *
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     *
     * @param integer $langId            
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     *
     * @return integer
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     *
     * @param Object $factors            
     */
    public function setFactors($factors)
    {
        $this->factors = $factors;
    }

    /**
     *
     * @return Object
     */
    public function getFactors()
    {
        return $this->factors;
    }
}