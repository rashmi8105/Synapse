<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class Response
{

    /**
     * Collection of errors
     *
     * @var \Synapse\RestBundle\Entity\Error collection of errors
     *     
     *      @JMS\Expose
     *      @JMS\Type("array")
     */
    private $errors;

    /**
     * Response Data
     *
     * @var mixed Response payload
     *     
     *      @JMS\Expose
     */
    private $data;

    /**
     * This property contains data which could referenced within the data property
     * 
     * @var array sideLoaded
     *     
     *      @JMS\Expose
     *      @JMS\SerializedName("sideLoaded")
     */
    private $sideLoaded;

    /**
     *
     * @param mixed $data
     *            response data
     * @param array $errors
     *            response errors
     * @param array $sideLoaded            
     */
    function __construct($data, array $errors = [], array $sideLoaded = [])
    {
        $this->errors = $errors;
        $this->data = $data;
        $this->sideLoaded = $sideLoaded;
    }

    /**
     *
     * @param mixed $data            
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     *
     * @param Error $error            
     */
    public function addError(Error $error)
    {
        $this->errors[] = $error;
    }

    /**
     *
     * @return Error[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     *
     * @param array $errors            
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * Adds side loaded data for this response
     * 
     * @param string $key            
     * @param mixed $data            
     */
    public function addSideLoadedData($key, $data)
    {
        $this->sideLoaded[$key] = $data;
    }

    /**
     *
     * @return array
     */
    public function getSideLoaded()
    {
        return $this->sideLoaded;
    }
}