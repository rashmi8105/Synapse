<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * ReferralsInterestedParties
 *
 * @ORM\Table(name="referrals_interested_parties")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ReferralsInterestedPartiesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @JMS\ExclusionPolicy("all")
 */
class ReferralsInterestedParties extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Referrals
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Referrals")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="referrals_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $referrals;

    /**
     * @var \Synapse\CoreBundle\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $person;

    /**
     * @var \Synapse\CoreBundle\Entity\ReferralHistory
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\ReferralHistory")
     * @ORM\JoinColumn(name="referral_history_id", referencedColumnName="id")
     * @JMS\Expose
     */
    private $referralHistory;

    /**
     * @var string
     *
     * @ORM\Column(name="user_key", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $userKey;
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Referrals $referrals
     */
    public function setReferrals($referrals)
    {
        $this->referrals = $referrals;

    }

    /**
     * Get referrals
     *
     * @return Referrals
     */
    public function getReferrals()
    {
        return $this->referrals;
    }

    /**
     * @param Person $person
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;

    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param string $userKey
     */
    public function setUserKey($userKey)
    {
        $this->userKey = $userKey;
    }

    /**
     * @return string
     */
    public function getUserKey()
    {
        return $this->userKey;
    }

    /**
     * @param ReferralHistory $referralHistory
     */
    public function setReferralHistory($referralHistory)
    {
        $this->referralHistory = $referralHistory;
    }

    /**
     * @return ReferralHistory
     */
    public function getReferralHistory()
    {
        return $this->referralHistory;
    }
}