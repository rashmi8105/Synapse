<?php
namespace Synapse\UploadBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 *
 * @package Synapse\UploadBundle\EntityDto
 *         
 */
class UploadFileHistoryDto
{

    /**
     * id
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $id;

    /**
     * $fileName
     *
     * @var string @JMS\Type("string")
     */
    private $fileName;
    

    /**
     * $type
     *
     * @var string @JMS\Type("string")
     */
    private $type;

    /**
     * $uploadedDate
     *
     * @var datetime @JMS\Type("DateTime")
     *     
     */
    private $uploadedDate;

    
    /**
     * $uploadedBy
     *
     * @var string @JMS\Type("string")
     */
    private $uploadedBy;

    
    /**
     * $success
     *
     * @var string @JMS\Type("string")
     */
    private $success;
    
    /**
     * $error
     *
     * @var string @JMS\Type("string")
     */
    private $error;
    
    /**
     * $repositoryUrl
     *
     * @var string @JMS\Type("string")
     */
    private $repositoryUrl;


    /**
     *
     * @param mixed $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $message            
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     *
     * @param string $type            
     */
    public function setType($type)
    {
        
        $this->type = $type;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param mixed $uploadedDate            
     */
    public function setUploadedDate($uploadedDate)
    {
        $this->uploadedDate = $uploadedDate;
    }

    /**
     *
     * @return mixed
     */
    public function getUploadedDate()
    {
        return $this->uploadedDate;
    }

 /**
     *
     * @param string $uploadedBy            
     */
    public function setUploadedBy($uploadedBy)
    {
        
        $this->uploadedBy = $uploadedBy;
    }

    /**
     *
     * @return string
     */
    public function getUploadedBy()
    {
        return $this->uploadedBy;
    }
    
    /**
     *
     * @param string $success
     */
    public function setSuccess($success)
    {
    
    	$this->success = $success;
    }
    
    /**
     *
     * @return string
     */
    public function getSuccess()
    {
    	return $this->success;
    }
    
    /**
     *
     * @param string $error
     */
    public function setError($error)
    {
    
    	$this->error = $error;
    }
    
    /**
     *
     * @return string
     */
    public function getError()
    {
    	return $this->error;
    }
    
    /**
     *
     * @param string $repositoryUrl
     */
    public function setRepositoryUrl($repositoryUrl)
    {
    
    	$this->repositoryUrl = $repositoryUrl;
    }
    
    /**
     *
     * @return string
     */
    public function getRepositoryUrl()
    {
    	return $this->repositoryUrl;
    }
    
}