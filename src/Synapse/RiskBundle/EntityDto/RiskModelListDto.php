<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for Risk Variables
 *
 * @package Synapse\RiskBundle\EntityDto
 */
class RiskModelListDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     */
    private $id;

    /**
     * model_name
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $modelName;

    /**
     * variables_count
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $variablesCount;

    /**
     * campuses_count
     *
     * @var integer @JMS\Type("integer")
     *     
     *     
     */
    private $campusesCount;

    /**
     * @param int $campusesCount
     */
    public function setCampusesCount($campusesCount)
    {
        $this->campusesCount = $campusesCount;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $modelName
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;
    }

    /**
     * @param string $variablesCount
     */
    public function setVariablesCount($variablesCount)
    {
        $this->variablesCount = $variablesCount;
    }

}