<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrgFeatures
 *
 * @ORM\Table(name="org_features", indexes={@ORM\Index(name="organizationfeature_featureid", columns={"feature_id"}), @ORM\Index(name="organizationfeatures_organizationid", columns={"organization_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgFeaturesRepository")
 */

class OrgFeatures extends BaseEntity
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="private", type="boolean", nullable=true)
     */
    private $private;

    /**
     * @var boolean
     *
     * @ORM\Column(name="connected", type="boolean", nullable=true)
     */
    private $connected;

    /**
     * @var boolean
     *
     * @ORM\Column(name="team", type="boolean", nullable=true)
     */
    private $team;

    /**
     * @var string
     *
     * @ORM\Column(name="default_access", type="string", length=45, nullable=true)
     */
    private $defaultAccess;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Synapse\CoreBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     */
    private $organization;

    /**
     * @var \Synapse\CoreBundle\Entity\FeatureMaster
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\FeatureMaster")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="feature_id", referencedColumnName="id")
     * })
     */
    private $feature;



    /**
     * Set private
     *
     * @param boolean $private
     * @return OrgFeatures
     */
    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    /**
     * Get private
     *
     * @return boolean 
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Set connected
     *
     * @param boolean $connected
     * @return OrgFeatures
     */
    public function setConnected($connected)
    {
        $this->connected = $connected;

        return $this;
    }

    /**
     * Get connected
     *
     * @return boolean 
     */
    public function getConnected()
    {
        return $this->connected;
    }

    /**
     * Set team
     *
     * @param boolean $team
     * @return OrgFeatures
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return boolean 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set defaultAccess
     *
     * @param string $defaultAccess
     * @return OrgFeatures
     */
    public function setDefaultAccess($defaultAccess)
    {
        $this->defaultAccess = $defaultAccess;

        return $this;
    }

    /**
     * Get defaultAccess
     *
     * @return string 
     */
    public function getDefaultAccess()
    {
        return $this->defaultAccess;
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
     * Set organization
     *
     * @param $organization
     * @return OrgFeatures
     */
    public function setOrganization($organization)
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
     * Set feature
     *
     * @param \Synapse\CoreBundle\Entity\FeatureMaster $feature
     * @return OrgFeatures
     */
    public function setFeature($feature)
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
}
