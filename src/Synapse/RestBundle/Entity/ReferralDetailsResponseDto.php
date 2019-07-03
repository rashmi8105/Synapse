<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class ReferralDetailsResponseDto
{

    /**
     * [$personId]
     *
     * @var [type] @JMS\Type("integer")
     */
    private $personId;

    /**
     * [$organizationId]
     *
     * @var [type] @JMS\Type("integer")
     */
    private $organizationId;

    /**
     * [$totalRecords]
     *
     * @var [type] @JMS\Type("integer")
     */
    private $totalRecords;

    /**
     * [$totalPages]
     *
     * @var [type] @JMS\Type("integer")
     */
    private $totalPages;

    /**
     * [$recordsPerPage]
     *
     * @var [type] @JMS\Type("integer")
     */
    private $recordsPerPage;

    /**
     * [$currentPage]
     *
     * @var [type] @JMS\Type("integer")
     */
    private $currentPage;

    /**
     *
     * @var Object @JMS\Type("array<Synapse\RestBundle\Entity\ReferralsArrayResponseDto>")
     */
    private $referrals;

    /**
     *
     * @param integer $currentPage            
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     *
     * @return integer
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     *
     * @param integer $organizationId            
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    /**
     *
     * @return integer
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param integer $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return integer
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param integer $recordsPerPage            
     */
    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = $recordsPerPage;
    }

    /**
     *
     * @return integer
     */
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    /**
     *
     * @param Object $referrals            
     */
    public function setReferrals($referrals)
    {
        $this->referrals = $referrals;
    }

    /**
     *
     * @return Object
     */
    public function getReferrals()
    {
        return $this->referrals;
    }

    /**
     *
     * @param integer $totalPages            
     */
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;
    }

    /**
     *
     * @return integer
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     *
     * @param integer $totalRecords            
     */
    public function setTotalRecords($totalRecords)
    {
        $this->totalRecords = $totalRecords;
    }

    /**
     *
     * @return integer
     */
    public function getTotalRecords()
    {
        return $this->totalRecords;
    }
}