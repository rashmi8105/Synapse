<?php
namespace Synapse\UploadBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * UploadFileLog
 *
 * @ORM\Table(name="upload_file_log")
 * @ORM\Entity(repositoryClass="Synapse\UploadBundle\Repository\UploadFileLogRepository")
 */
class UploadFileLog extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var string
     * @ORM\Column(name="upload_type", type="string", nullable=true, columnDefinition="enum('A', 'C', 'F', 'G', 'S', 'SB', 'SM', 'T', 'TP', 'P','H','SL','RV','RM','RMA','CI','FA','GF','GS','S2G')")
     *     
     */
    private $uploadType;

    /**
     *
     * @var \DateTime @ORM\Column(name="upload_date", type="datetime", nullable=true)
     */
    private $uploadDate;

    /**
     *
     * @var string @ORM\Column(name="uploaded_columns", type="text", nullable=true)
     */
    private $uploadedColumns;

    /**
     *
     * @var integer @ORM\Column(name="uploaded_row_count", type="integer", nullable=true)
     */
    private $uploadedRowCount;

    /**
     *
     * @var integer @ORM\Column(name="valid_row_count", type="integer", nullable=true)
     */
    private $validRowCount;

    /**
     *
     * @var string @ORM\Column(name="status", type="string", length=1, nullable=true)
     */
    private $status;

    /**
     *
     * @var string @ORM\Column(name="viewed", type="boolean", nullable=true)
     */
    private $viewed;

    /**
     *
     * @var string @ORM\Column(name="uploaded_file_path", type="string", length=500, nullable=true)
     */
    private $uploadedFilePath;

    /**
     *
     * @var string @ORM\Column(name="uploaded_file_hash", type="string", length=32, nullable=true)
     */
    private $uploadedFileHash;

    /**
     *
     * @var string @ORM\Column(name="error_file_path", type="string", length=500, nullable=true)
     */
    private $errorFilePath;

    /**
     *
     * @var string @ORM\Column(name="error_count", type="integer",nullable=true)
     */
    private $errorCount;

    /**
     *
     * @var string @ORM\Column(name="job_number", type="string", length=255, nullable=true)
     */
    private $jobNumber;

    /**
     *
     * @var integer @ORM\Column(name="organization_id", type="integer", nullable=true)
     */
    private $organizationId;

    /**
     *
     * @var integer @ORM\Column(name="group_id", type="integer", nullable=true)
     */
    private $groupId;

    /**
     *
     * @var integer @ORM\Column(name="person_id", type="integer", nullable=true)
     */
    private $personId;

    /**
     *
     * @var integer @ORM\Column(name="course_id", type="integer", nullable=true)
     */
    private $courseId;

    /**
     *
     * @var integer @ORM\Column(name="created_row_count", type="integer", nullable=true)
     */
    private $createdRowCount;

    /**
     *
     * @var integer @ORM\Column(name="updated_row_count", type="integer", nullable=true)
     */
    private $updatedRowCount;

    /**
     *
     * @var integer @ORM\Column(name="error_row_count", type="integer", nullable=true)
     */
    private $errorRowCount;

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
     * Set uploadType
     *
     * @param string $uploadType            
     * @return UploadFileLog
     */
    public function setUploadType($uploadType)
    {
        $this->uploadType = $uploadType;
        
        return $this;
    }

    /**
     * Get uploadType
     *
     * @return string
     */
    public function getUploadType()
    {
        return $this->uploadType;
    }

    /**
     * Set uploadDate
     *
     * @param \DateTime $uploadDate            
     * @return UploadFileLog
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;
        
        return $this;
    }

    /**
     * Get uploadDate
     *
     * @return \DateTime
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * Set uploadedColumns
     *
     * @param string $uploadedColumns            
     * @return UploadFileLog
     */
    public function setUploadedColumns($uploadedColumns)
    {
        $this->uploadedColumns = $uploadedColumns;
        
        return $this;
    }

    /**
     * Get uploadedColumns
     *
     * @return string
     */
    public function getUploadedColumns()
    {
        return $this->uploadedColumns;
    }

    /**
     * Set uploadedRowCount
     *
     * @param integer $uploadedRowCount            
     * @return UploadFileLog
     */
    public function setUploadedRowCount($uploadedRowCount)
    {
        $this->uploadedRowCount = $uploadedRowCount;
        
        return $this;
    }

    /**
     * Get uploadedRowCount
     *
     * @return integer
     */
    public function getUploadedRowCount()
    {
        return $this->uploadedRowCount;
    }

    /**
     * Set validRowCount
     *
     * @param integer $validRowCount
     * @return UploadFileLog
     */
    public function setValidRowCount($validRowCount)
    {
        $this->validRowCount = $validRowCount;

        return $this;
    }

    /**
     * Get validRowCount
     *
     * @return integer
     */
    public function getValidRowCount()
    {
        return $this->validRowCount;
    }


    /**
     * Set CreatedRowCount
     *
     * @param integer $createdRowCount
     * @return UploadFileLog
     */
    public function setCreatedRowCount($createdRowCount)
    {
        $this->createdRowCount = $createdRowCount;

        return $this;
    }

    /**
     * Get createdRowCount
     *
     * @return integer
     */
    public function getCreatedRowCount()
    {
        return $this->createdRowCount;
    }


    /**
     * Set updatedRowCount
     *
     * @param integer $updatedRowCount
     * @return UploadFileLog
     */
    public function setUpdatedRowCount($updatedRowCount)
    {
        $this->updatedRowCount = $updatedRowCount;

        return $this;
    }

    /**
     * Get updatedRowCount
     *
     * @return integer
     */
    public function getUpdatedRowCount()
    {
        return $this->updatedRowCount;
    }


    /**
     * Set $errorRowCount
     *
     * @param integer $errorRowCount
     * @return UploadFileLog
     */
    public function setErrorRowCount($errorRowCount)
    {
        $this->errorRowCount = $errorRowCount;

        return $this;
    }

    /**
     * Get errorRowCount
     *
     * @return integer
     */
    public function getErrorRowCount()
    {
        return $this->errorRowCount;
    }

    /**
     * Set status
     *
     * @param string $status            
     * @return UploadFileLog
     */
    public function setStatus($status)
    {
        $this->status = $status;
        
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set viewed
     *
     * @param string $viewed            
     * @return UploadFileLog
     */
    public function setViewed($viewed)
    {
        $this->viewed = $viewed;
        
        return $this;
    }

    /**
     * Get viewed
     *
     * @return string
     */
    public function getViewed()
    {
        return $this->viewed;
    }

    /**
     * Set uploadedFilePath
     *
     * @param string $uploadedFilePath            
     * @return UploadFileLog
     */
    public function setUploadedFilePath($uploadedFilePath)
    {
        $this->uploadedFilePath = $uploadedFilePath;
        
        return $this;
    }

    /**
     * Get uploadedFilePath
     *
     * @return string
     */
    public function getUploadedFilePath()
    {
        return $this->uploadedFilePath;
    }

    /**
     * Sets the value of uploadedFileHash.
     *
     * @param string
     *
     * @return self
     */
    public function setUploadedFileHash($uploadedFileHash)
    {
        $this->uploadedFileHash = $uploadedFileHash;

        return $this;
    }

    /**
     * Gets the value of uploadedFileHash.
     *
     * @return string
     */
    public function getUploadedFileHash()
    {
        return $this->uploadedFileHash;
    }

    /**
     * Set errorFilePath
     *
     * @param string $errorFilePath            
     * @return UploadFileLog
     */
    public function setErrorFilePath($errorFilePath)
    {
        $this->errorFilePath = $errorFilePath;
        
        return $this;
    }

    /**
     * Get errorFilePath
     *
     * @return string
     */
    public function getErrorFilePath()
    {
        return $this->errorFilePath;
    }

    /**
     * Set errorCount
     *
     * @param string $errorCount            
     * @return UploadFileLog
     */
    public function setErrorCount($errorCount)
    {
        $this->errorCount = $errorCount;
        
        return $this;
    }

    /**
     * Get errorCount
     *
     * @return string
     */
    public function getErrorCount()
    {
        return $this->errorCount;
    }

    /**
     * Set jobNumber
     *
     * @param string $jobNumber            
     * @return UploadFileLog
     */
    public function setJobNumber($jobNumber)
    {
        $this->jobNumber = $jobNumber;
        
        return $this;
    }

    /**
     * Get jobNumber
     *
     * @return string
     */
    public function getJobNumber()
    {
        return $this->jobNumber;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization            
     * @return UploadFileLog
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
        
        return $this;
    }

    /**
     * Get organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Set person
     *
     * @param \Synapse\CoreBundle\Entity\Person $person            
     * @return UploadFileLog
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
        
        return $this;
    }

    /**
     * Get person
     *
     * @return \Synapse\CoreBundle\Entity\Person
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Sets the value of groupId.
     *
     * @param integer $groupId
     *            the group id
     *            
     * @return self
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        
        return $this;
    }

    /**
     * Gets the value of groupId.
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Sets the value of courseId.
     *
     * @param integer $courseId
     *            the course id
     *            
     * @return self
     */
    public function setCourseId($courseId)
    {
        $this->coursed = $courseId;
        
        return $this;
    }

    /**
     * Gets the value of courseId.
     *
     * @return integer
     */
    public function getCourseId()
    {
        return $this->courseId;
    }
}
