<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * PersonFactorCalculated
 *
 * @ORM\Table(name="person_factor_calculated", indexes={@ORM\Index(name="fk_person_factor_calculated_person1_idx", columns={"person_id"}), @ORM\Index(name="fk_person_factor_calculated_organization_idx", columns={"organization_id"}), @ORM\Index(name="fk_person_factor_calculated_factor1_idx", columns={"factor_id"})}
 * ,uniqueConstraints = {@ORM\UniqueConstraint(name="org_person_factor_uniq_idx", columns={"organization_id","person_id","survey_id","factor_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\PersonFactorCalculatedRepository")
 */
class PersonFactorCalculated extends BaseEntity
{
    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
	/**
	 *
	 * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true)
	 *      })
	 */
	private $organization;

	/**
	 *
	 * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
	 *      })
	 */
	private $person;
	
	/**
     *
     * @var \Synapse\SurveyBundle\Entity\Factor @ORM\ManyToOne(targetEntity="\Synapse\SurveyBundle\Entity\Factor")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="factor_id", referencedColumnName="id")
     *      })
     */
    private $factor;
	
	/**
	 * @var \Synapse\CoreBundle\Entity\Survey @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Survey")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
	 *      })
	 */
	private $survey;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="mean_value", type="decimal", precision=13, scale=4, nullable=true, unique=false)
	 */
	private $meanValue;
	
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
	 * @param \Synapse\CoreBundle\Entity\Organization $organization
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
	 * Set person
	 *
	 * @param \Synapse\CoreBundle\Entity\Person $person
	 */
	public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null)
	{
		$this->person = $person;

		return $this;
	}

	/**
	 * Get person
	 *
	 * @return \Synapse\CoreBundle\Entity\Person
	 */
	public function getPerson()
	{
		return $this->person;
	}

	/**
	 * Set factor
	 *
	 * @param \Synapse\SurveyBundle\Entity\Factor $factor
	 * @return FactorQuestions
	 */
	public function setFactor(\Synapse\SurveyBundle\Entity\Factor $factor = null)
	{
		$this->factor = $factor;
	
		return $this;
	}
	
	/**
	 * Get factor
	 *
	 * @return \Synapse\SurveyBundle\Entity\Factor
	 */
	public function getFactor()
	{
		return $this->factor;
	}
	
	/**
	 * Set survey
	 *
	 * @param \Synapse\CoreBundle\Entity\Survey $survey
	 */
	public function setSurvey(\Synapse\CoreBundle\Entity\Survey $survey = null)
	{
		$this->survey = $survey;

		return $this;
	}

	/**
	 * Get survey
	 *
	 * @return \Synapse\CoreBundle\Entity\Survey
	 */
	public function getSurvey()
	{
		return $this->survey;
	}
	
	/**
	 * Get meanValue
	 *
	 * @return meanValue
	 */
	public function getMeanValue()
	{
		return $this->meanValue;
	}
	
	/**
	 * Set meanValue
	 *
	 * @return meanValue
	 */
	public function setMeanValue($meanValue)
	{
		$this->meanValue = $meanValue;
	
		return $this;
	}
}
