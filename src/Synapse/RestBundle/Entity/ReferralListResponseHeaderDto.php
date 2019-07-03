<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class ReferralListResponseHeaderDto
{

    /**
     * [$personId]
     * 
     * @var [type] @JMS\Type("integer")
     */
    private $personId;

    /**
     * [$personId]
     * 
     * @var [type] @JMS\Type("array<Synapse\RestBundle\Entity\ReferralListResponseDto>")
     */
    private $referrals;

    /**
     *
     * @param mixed $personId            
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     *
     * @return mixed
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     *
     * @param mixed $referrals            
     */
    public function setReferrals($referrals)
    {
        $this->referrals = $referrals;
    }

    /**
     *
     * @return mixed
     */
    public function getReferrals()
    {
        return $this->referrals;
    }
}