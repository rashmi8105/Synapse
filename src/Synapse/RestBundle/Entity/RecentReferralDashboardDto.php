<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

class RecentReferralDashboardDto
{

    /**
     * Id of a person.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $personId;

    /**
     * Total number of open referrals that a person has received.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $totalOpenReferralsReceived;

    /**
     * Total number of referrals a person has received.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $totalReferralsReceived;

    /**
     * Total number of open referrals a person has sent.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $totalOpenReferralsSent;

    /**
     * Total number of referrals a person has sent.
     *
     * @var integer
     * @JMS\Type("integer")
     */
    private $totalReferralsSent;

    /**
     * Array of referrals.
     *
     * @var array
     * @JMS\Type("array")
     */
    private $referrals;

    /**
     *
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * @param array $referrals
     */
    public function setReferrals($referrals)
    {
        $this->referrals = $referrals;
    }

    /**
     * @return array
     */
    public function getReferrals()
    {
        return $this->referrals;
    }

    /**
     * @param int $totalOpenReferralsReceived
     */
    public function setTotalOpenReferralsReceived($totalOpenReferralsReceived)
    {
        $this->totalOpenReferralsReceived = $totalOpenReferralsReceived;
    }

    /**
     * @return int
     */
    public function getTotalOpenReferralsReceived()
    {
        return $this->totalOpenReferralsReceived;
    }

    /**
     * @param int $totalOpenReferralsSent
     */
    public function setTotalOpenReferralsSent($totalOpenReferralsSent)
    {
        $this->totalOpenReferralsSent = $totalOpenReferralsSent;
    }

    /**
     * @return int
     */
    public function getTotalOpenReferralsSent()
    {
        return $this->totalOpenReferralsSent;
    }

    /**
     * @param int $totalReferralsReceived
     */
    public function setTotalReferralsReceived($totalReferralsReceived)
    {
        $this->totalReferralsReceived = $totalReferralsReceived;
    }

    /**
     * @return int
     */
    public function getTotalReferralsReceived()
    {
        return $this->totalReferralsReceived;
    }

    /**
     * @param int $totalReferralsSent
     */
    public function setTotalReferralsSent($totalReferralsSent)
    {
        $this->totalReferralsSent = $totalReferralsSent;
    }

    /**
     * @return int
     */
    public function getTotalReferralsSent()
    {
        return $this->totalReferralsSent;
    }
}