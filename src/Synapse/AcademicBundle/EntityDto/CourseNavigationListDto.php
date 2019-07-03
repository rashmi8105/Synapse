<?php
namespace Synapse\AcademicBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

class CourseNavigationListDto
{

    /**
     * key
     *
     * @var string @JMS\Type("string")
     */
    private $key;

    /**
     * value
     *
     * @var string @JMS\Type("string")
     */
    private $value;

    /**
     * currentYear
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $currentYear;

    /**
     * currentYear
     *
     * @var boolean @JMS\Type("boolean")
     */
    private $currentTerm;

    /**
     *
     * @param string $key            
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     *
     * @param string $value            
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *
     * @param boolean $currentYear            
     */
    public function setCurrentYear($currentYear)
    {
        $this->currentYear = $currentYear;
    }

    /**
     *
     * @return boolean
     */
    public function getCurrentYear()
    {
        return $this->currentYear;
    }

    /**
     *
     * @param boolean $currentTerm            
     */
    public function setCurrentTerm($currentTerm)
    {
        $this->currentTerm = $currentTerm;
    }

    /**
     *
     * @return boolean
     */
    public function getCurrentTerm()
    {
        return $this->currentTerm;
    }
}