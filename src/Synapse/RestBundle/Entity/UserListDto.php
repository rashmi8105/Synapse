<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for UserListDto
 *
 * @package Synapse\RestBundle\Entity
 */
class UserListDto
{
    /**
     * List of students
     *
     * @var UserDTO[]
     * @JMS\Type("array<Synapse\RestBundle\Entity\UserDTO>")
     */
    private $student;

    /**
     * List of faculty users
     *
     * @var UserDTO[]
     * @JMS\Type("array<Synapse\RestBundle\Entity\UserDTO>")
     */
    private $faculty;

    /**
     * Total pages in current search
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalPages;

    /**
     * Number of records to be listed in a page.
     *
     * @var integer @JMS\Type("integer")
     */
    private $recordsPerPage;

    /**
     * Current page number
     *
     * @var integer @JMS\Type("integer")
     */
    private $currentPage;

    /**
     * Count of total records from the search.
     *
     * @var integer @JMS\Type("integer")
     */
    private $totalRecords;

    /**
     * last updated date
     *
     * @var string @JMS\Type("string")
     */
    private $lastUpdated;

    /**
     * @return UserDTO[]
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * @param UserDTO[] $student
     */
    public function setStudent($student)
    {
        $this->student = $student;
    }

    /**
     * @return UserDTO[]
     */
    public function getFaculty()
    {
        return $this->faculty;
    }

    /**
     * @param UserDTO[] $faculty
     */
    public function setFaculty($faculty)
    {
        $this->faculty = $faculty;
    }

    /**
     * @return integer
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * @param integer $totalPages
     */
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
    }

    /**
     * @return integer
     */
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    /**
     * @param integer $recordsPerPage
     */
    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = $recordsPerPage;
    }

    /**
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param integer $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return integer
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }

    /**
     * @param integer $totalRecords
     */
    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
    }

    /**
     * @return mixed
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * @param mixed $lastUpdated
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }
}