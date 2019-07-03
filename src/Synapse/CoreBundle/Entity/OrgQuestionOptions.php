<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * OrgQuestionOptions
 *
 * @ORM\Table(name="org_question_options", indexes={@ORM\Index(name="fk_org_question_options_org_question1_idx", columns={"org_question_id"}), @ORM\Index(name="fk_org_question_options_organization1_idx", columns={"organization_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgQuestionOptionsRepository")
 */
class OrgQuestionOptions extends BaseEntity
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
     * @var string
     *
     * @ORM\Column(name="option_name", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    private $optionName;

    /**
     * @var string
     *
     * @ORM\Column(name="option_value", type="string", length=5000, precision=0, scale=0, nullable=true, unique=false)
     */
    private $optionValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="sequence", type="smallint", precision=0, scale=0, nullable=true, unique=false)
     */
    private $sequence;

    /**
     * @var \Synapse\CoreBundle\Entity\OrgQuestion
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\OrgQuestion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="org_question_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $orgQuestion;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set optionName
     *
     * @param string $optionName
     * @return OrgQuestionOptions
     */
    public function setOptionName($optionName)
    {
        $this->optionName = $optionName;

        return $this;
    }

    /**
     * Get optionName
     *
     * @return string 
     */
    public function getOptionName()
    {
        return $this->optionName;
    }

    /**
     * Set optionValue
     *
     * @param string $optionValue
     * @return OrgQuestionOptions
     */
    public function setOptionValue($optionValue)
    {
        $this->optionValue = $optionValue;

        return $this;
    }

    /**
     * Get optionValue
     *
     * @return string 
     */
    public function getOptionValue()
    {
        return $this->optionValue;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return OrgQuestionOptions
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return integer 
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set orgQuestion
     *
     * @param \Synapse\CoreBundle\Entity\OrgQuestion $orgQuestion
     * @return OrgQuestionOptions
     */
    public function setOrgQuestion(\Synapse\CoreBundle\Entity\OrgQuestion $orgQuestion = null)
    {
        $this->orgQuestion = $orgQuestion;

        return $this;
    }

    /**
     * Get orgQuestion
     *
     * @return \Synapse\CoreBundle\Entity\OrgQuestion 
     */
    public function getOrgQuestion()
    {
        return $this->orgQuestion;
    }

    /**
     * Set organization
     *
     * @param \Synapse\CoreBundle\Entity\Organization $organization
     * @return OrgQuestionOptions
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
}
