<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * ReferralsTeams
 *
 * @ORM\Table(name="referrals_teams")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\ReferralsTeamsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @JMS\ExclusionPolicy("all")
 */
class ReferralsTeams extends BaseEntity
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
     * @var \Synapse\CoreBundle\Entity\Teams
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Teams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Teams_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $teams;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set teams
     *
     * @param \Synapse\CoreBundle\Entity\Teams $teams
     * @return ReferralsTeams
     */
    public function setTeams(\Synapse\CoreBundle\Entity\Teams $teams = null)
    {
        $this->teams = $teams;

        return $this;
    }

    /**
     * Get teams
     *
     * @return \Synapse\CoreBundle\Entity\Teams
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Set referrals
     *
     * @param \Synapse\CoreBundle\Entity\Referrals $referrals
     * @return ReferralsTeams
     */
    public function setReferrals(\Synapse\CoreBundle\Entity\Referrals $referrals = null)
    {
        $this->referrals = $referrals;

        return $this;
    }

    /**
     * Get referrals
     *
     * @return \Synapse\CoreBundle\Entity\Referrals
     */
    public function getReferrals()
    {
        return $this->referrals;
    }
}
