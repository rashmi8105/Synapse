<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EbiPermissionsetFeatures
 *
 * @ORM\Table(name="ebi_permissionset_features", indexes={@ORM\Index(name="permissionfeature_featureid", columns={"feature_id"}), @ORM\Index(name="permissionfeature_permissionsetid", columns={"ebi_permissionset_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\EbiPermissionsetFeaturesRepository")
 */
class EbiPermissionsetFeatures
{

    /**
     *
     * @var boolean @ORM\Column(name="private_create", type="boolean", length=1, nullable=true)
     */
    private $privateCreate;

    /**
     *
     * @var boolean @ORM\Column(name="team_create", type="boolean", length=1, nullable=true)
     */
    private $teamCreate;

    /**
     *
     * @var boolean @ORM\Column(name="team_view", type="boolean", length=1, nullable=true)
     */
    private $teamView;

    /**
     *
     * @var boolean @ORM\Column(name="public_create", type="boolean", length=1, nullable=true)
     */
    private $publicCreate;

    /**
     *
     * @var boolean @ORM\Column(name="public_view", type="boolean", length=1, nullable=true)
     */
    private $publicView;

    /**
     *
     * @var boolean @ORM\Column(name="timeframe_all", type="boolean", nullable=true)
     */
    private $timeframeAll;

    /**
     *
     * @var boolean @ORM\Column(name="current_calendar", type="boolean", nullable=true)
     */
    private $currentCalendar;

    /**
     *
     * @var boolean @ORM\Column(name="previous_period", type="boolean", nullable=true)
     */
    private $previousPeriod;

    /**
     *
     * @var boolean @ORM\Column(name="next_period", type="boolean", nullable=true)
     */
    private $nextPeriod;

    /**
     *
     * @var boolean @ORM\Column(name="receive_referral", type="boolean", nullable=true)
     */
    private $receiveReferral;

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Permissionset @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Permissionset")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="ebi_permissionset_id", referencedColumnName="id")
     *      })
     */
    private $ebiPermissionset;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\FeatureMaster @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\FeatureMaster")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="feature_id", referencedColumnName="id")
     *      })
     */
    private $feature;
    
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
     * Set privateCreate
     *
     * @param boolean $privateCreate            
     * @return ebiPermissionsetFeatures
     */
    public function setPrivateCreate($privateCreate)
    {
        $this->privateCreate = $privateCreate;
        
        return $this;
    }

    /**
     * Get privateCreate
     *
     * @return boolean
     */
    public function getPrivateCreate()
    {
        return $this->privateCreate;
    }

    /**
     * Set publicCreate
     *
     * @param boolean $publicCreate            
     * @return ebiPermissionsetFeatures
     */
    public function setPublicCreate($publicCreate)
    {
        $this->publicCreate = $publicCreate;
        
        return $this;
    }

    /**
     * Get publicCreate
     *
     * @return boolean
     */
    public function getPublicCreate()
    {
        return $this->publicCreate;
    }

    /**
     * Set publicView
     *
     * @param boolean $publicView            
     * @return ebiPermissionsetFeatures
     */
    public function setPublicView($publicView)
    {
        $this->publicView = $publicView;
        
        return $this;
    }

    /**
     * Get publicView
     *
     * @return boolean
     */
    public function getPublicView()
    {
        return $this->publicView;
    }

    /**
     * Set teamCreate
     *
     * @param boolean $teamCreate            
     * @return EbiPermissionsetFeatures
     */
    public function setTeamCreate($teamCreate)
    {
        $this->teamCreate = $teamCreate;
        
        return $this;
    }

    /**
     * Get teamCreate
     *
     * @return boolean
     */
    public function getTeamCreate()
    {
        return $this->teamCreate;
    }

    /**
     * Set teamView
     *
     * @param boolean $teamView            
     * @return EbiPermissionsetFeatures
     */
    public function setTeamView($teamView)
    {
        $this->teamView = $teamView;
        
        return $this;
    }

    /**
     * Get teamView
     *
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
     * @return PermissionsetFeatures
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
     * @return PermissionsetFeatures
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
     * @return PermissionsetFeatures
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
     * @return PermissionsetFeatures
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
     * Set receiveReferral
     *
     * @param boolean $receiveReferral            
     * @return PermissionsetFeatures
     */
    public function setReceiveReferral($receiveReferral)
    {
        $this->receiveReferral = $receiveReferral;
        
        return $this;
    }

    /**
     * Get receiveReferral
     *
     * @return boolean
     */
    public function getReceiveReferral()
    {
        return $this->receiveReferral;
    }

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
     * Set ebiPermissionset
     *
     * @param \Synapse\CoreBundle\Entity\Permissionset $ebiPermissionset            
     * @return PermissionsetFeatures
     */
    public function setPermissionset(\Synapse\CoreBundle\Entity\Permissionset $ebiPermissionset = null)
    {
        $this->ebiPermissionset = $ebiPermissionset;
        
        return $this;
    }

    /**
     * Get ebiPermissionset
     *
     * @return \Synapse\CoreBundle\Entity\Permissionset
     */
    public function getPermissionset()
    {
        return $this->ebiPermissionset;
    }

    /**
     * Set feature
     *
     * @param \Synapse\CoreBundle\Entity\FeatureMaster $feature            
     * @return PermissionsetFeatures
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