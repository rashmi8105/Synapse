<?php
namespace Synapse\SurveyBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Student Isq Questions
 *
 * @package Synapse\SurveyBundle\EntityDto
 */
class IsqDataArrayDto
{

    /**
     * id
     *
     * @var integer @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * name
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $name;

    /**
     * response
     *
     * @var string @JMS\Type("string")
     *     
     */
    private $response;

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $name            
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param string $response            
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }
}