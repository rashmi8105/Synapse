<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * OrgPermissionsetFeatures
 *
 * @ORM\Table(name="org_permissionset_features", indexes={@ORM\Index(name="fk_permissionfeature_permissionsetid", columns={"org_permissionset_id"}), @ORM\Index(name="fk_permissionfeature_featureid", columns={"feature_id"}), @ORM\Index(name="fk_permissionfeature_organizationid", columns={"organization_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgPermissionsetFeaturesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgPermissionsetFeatures extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="private_create", type="boolean", length=1, nullable=true)
     */
    private $privateCreate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="team_create", type="boolean", length=1, nullable=true)
     */
    private $teamCreate;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="team_view", type="boolean", length=1, nullable=true)
     */
    private $teamView;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="public_create", type="boolean", length=1, nullable=true)
     */
    private $publicCreate;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="public_view", type="boolean", length=1, nullable=true)
     */
    private $publicView;

    /**
     * @var boolean
     *
     * @ORM\Column(name="receive_referral", type="boolean", length=1, nullable=true)
     */
    private $receiveReferral;

    /**
     * @var boolean
     *
     * @ORM\Column(name="timeframe_all", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $timeframeAll;

    /**
     * @var boolean
     *
     * @ORM\Column(name="current_calendar", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $currentCalendar;

    /**
     * @var boolean
     *
     * @ORM\Column(name="previous_period", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $previousPeriod;

    /**
     * @var boolean
     *
     * @ORM\Column(name="next_period", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $nextPeriod;

    /**
     * @var \Synapse\CoreBundle\Entity\FeatureMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\FeatureMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="feature_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $feature;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\OrgPermissionset
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgPermissionset")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_permissionset_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgPermissionset;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="reason_referral_private_create", type="boolean", length=1, nullable=true)
     */
    private $reasonReferralPrivateCreate;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="reason_referral_team_create", type="boolean", length=1, nullable=true)
     */
    private $reasonReferralTeamCreate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reason_referral_team_view", type="boolean", length=1, nullable=true)
     */
    private $reasonReferralTeamView;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="reason_referral_public_create", type="boolean", length=1, nullable=true)
     */
    private $reasonReferralPublicCreate;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="reason_referral_public_view", type="boolean", length=1, nullable=true)
     */
    private $reasonReferralPublicView;
    


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
     * @param boolean $privateCreate
     */
    public function setPrivateCreate($privateCreate)
    {
        $this->privateCreate = $privateCreate;
    }

    /**
     * @return boolean
     */
    public function getPrivateCreate()
    {
        return $this->privateCreate;
    }

    /**
     * @param boolean $publicCreate
     */
    public function setPublicCreate($publicCreate)
    {
        $this->publicCreate = $publicCreate;
    }

    /**
     * @return boolean
     */
    public function getPublicCreate()
    {
        return $this->publicCreate;
    }

    /**
     * @param boolean $publicView
     */
    public function setPublicView($publicView)
    {
        $this->publicView = $publicView;
    }

    /**
     * @return boolean
     */
    public function getPublicView()
    {
        return $this->publicView;
    }

    /**
     * @param boolean $receiveReferral
     */
    public function setReceiveReferral($receiveReferral)
    {
        $this->receiveReferral = $receiveReferral;
    }

    /**
     * @return boolean
     */
    public function getReceiveReferral()
    {
        return $this->receiveReferral;
    }

    /**
     * @param boolean $teamCreate
     */
    public function setTeamCreate($teamCreate)
    {
        $this->teamCreate = $teamCreate;
    }

    /**
     * @return boolean
     */
    public function getTeamCreate()
    {
        return $this->teamCreate;
    }

    /**
     * @param boolean $teamView
     */
    public function setTeamView($teamView)
    {
        $this->teamView = $teamView;
    }

    /**
     * @return boolean
     */
    public function getTeamView()
    {
        return $this->teamView;
    }

       

    /**
     * Set timeframeAll
     *
     * @param boolean $timeframeAll
     * @return OrgPermissionsetFeatures
     */
    public function setTimeframeAll($timeframeAll)
    {
        $this->timeframeAll = $timeframeAll;

        return $this;
    }

    /**
     * Get timeframeAll
     *
     * @return boolean 
     */
    public function getTimeframeAll()
    {
        return $this->timeframeAll;
    }

    /**
     * Set currentCalendar
     *
     * @param boolean $currentCalendar
     * @return OrgPermissionsetFeatures
     */
    public function setCurrentCalendar($currentCalendar)
    {
        $this->currentCalendar = $currentCalendar;

        return $this;
    }

    /**
     * Get currentCalendar
     *
     * @return boolean 
     */
    public function getCurrentCalendar()
    {
        return $this->currentCalendar;
    }

    /**
     * Set previousPeriod
     *
     * @param boolean $previousPeriod
     * @return OrgPermissionsetFeatures
     */
    public function setPreviousPeriod($previousPeriod)
    {
        $this->previousPeriod = $previousPeriod;

        return $this;
    }

    /**
     * Get previousPeriod
     *
     * @return boolean 
     */
    public function getPreviousPeriod()
    {
        return $this->previousPeriod;
    }

    /**
     * Set nextPeriod
     *
     * @param boolean $nextPeriod
     * @return OrgPermissionsetFeatures
     */
    public function setNextPeriod($nextPeriod)
    {
        $this->nextPeriod = $nextPeriod;

        return $this;
    }

    /**
     * Get nextPeriod
     *
     * @return boolean 
     */
    public function getNextPeriod()
    {
        return $this->nextPeriod;
    }

    /**
     * Set feature
     *
     * @param \Synapse\CoreBundle\Entity\FeatureMaster $feature
     * @return OrgPermissionsetFeatures
     */
    public function setFeature(\Synapse\CoreBundle\Entity\FeatureMaster $feature = null)
    {
        $this->feature = $feature;

        return $this;
    }

    /**
     * Get feature
     *
     * @return \Synapse\CoreBundle\Entity\FeatureMaster 
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgPermissionsetFeatures
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \Synapse\CoreBundle\Entity\Organization 
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set orgPermissionset
     *
     * @param \Synapse\CoreBundle\Entity\OrgPermissionset $orgPermissionset
     * @return OrgPermissionsetFeatures
     */
    public function setOrgPermissionset(\Synapse\CoreBundle\Entity\OrgPermissionset $orgPermissionset = null)
    {
        $this->orgPermissionset = $orgPermissionset;

        return $this;
    }

    /**
     * Get orgPermissionset
     *
     * @return \Synapse\CoreBundle\Entity\OrgPermissionset 
     */
    public function getOrgPermissionset()
    {
        return $this->orgPermissionset;
    }
    
    /**
     * @param boolean $reasonReferralPrivateCreate
     */
    public function setReasonReferralPrivateCreate($reasonReferralPrivateCreate)
    {
    	$this->reasonReferralPrivateCreate = $reasonReferralPrivateCreate;
    }
    
    /**
     * @return boolean
     */
    public function getReasonReferralPrivateCreate()
    {
    	return $this->reasonReferralPrivateCreate;
    }
    
    /**
     * @param boolean $reasonReferralTeamCreate
     */
    public function setReasonReferralTeamCreate($reasonReferralTeamCreate)
    {
    	$this->reasonReferralTeamCreate = $reasonReferralTeamCreate;
    }
    
    /**
     * @return boolean
     */
    public function getReasonReferralTeamCreate()
    {
    	return $this->reasonReferralTeamCreate;
    }
    
    /**
     * @param boolean $reasonReferralTeamView
     */
    public function setReasonReferralTeamView($reasonReferralTeamView)
    {
    	$this->reasonReferralTeamView = $reasonReferralTeamView;
    }
    
    /**
     * @return boolean
     */
    public function getReasonReferralTeamView()
    {
    	return $this->reasonReferralTeamView;
    }
    
    /**
     * @param boolean $reasonReferralPublicCreate
     */
    public function setReasonReferralPublicCreate($reasonReferralPublicCreate)
    {
    	$this->reasonReferralPublicCreate = $reasonReferralPublicCreate;
    }
    
    /**
     * @return boolean
     */
    public function getReasonReferralPublicCreate()
    {
    	return $this->reasonReferralPublicCreate;
    }
    
    /**
     * @param boolean $reasonReferralPublicView
     */
    public function setReasonReferralPublicView($reasonReferralPublicView)
    {
    	$this->reasonReferralPublicView = $reasonReferralPublicView;
    }
    
    /**
     * @return boolean
     */
    public function getReasonReferralPublicView()
    {
    	return $this->reasonReferralPublicView;
    }
}
