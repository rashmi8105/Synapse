<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OrgPersonStudentCohort
 *
 * @ORM\Table(name="org_person_student_cohort",indexes={@ORM\Index(name="fk_org_person_student_cohort_organization1", columns={"organization_id"}), @ORM\Index(name="fk_org_person_student_cohort_person1", columns={"person_id"}), @ORM\Index(name="fk_org_person_student_cohort_org_academic_year_id1", columns={"org_academic_year_id"}), @ORM\Index(name="org_person_student_cohort_covering_index", columns={"organization_id", "org_academic_year_id", "person_id", "deleted_at"})}, uniqueConstraints={@ORM\UniqueConstraint(name="cohort_unique_index", columns={"organization_id", "person_id", "org_academic_year_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgPersonStudentCohortRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgPersonStudentCohort extends BaseEntity {
	
	/**
	 *
	 * @var integer @ORM\Column(name="id", type="integer", nullable=false)
	 *      @ORM\Id
	 *      @ORM\GeneratedValue(strategy="IDENTITY")
	 *      @JMS\Expose
	 *     
	 */
	private $id;
	
	/**
	 *
	 * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
	 *      })
	 *      @JMS\Expose
	 */
	private $organization;
	
	/**
	 *
	 * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
	 *      })
	 *      @JMS\Expose
	 */
	private $person;
	
	/**
	 *
	 * @var \Synapse\AcademicBundle\Entity\OrgAcademicYear @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicYear")
	 *      @ORM\JoinColumns({
	 *      @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id", nullable=true)
	 *      })
	 *      @JMS\Expose 
	 */
	private $orgAcademicYear;
	
	/**
	 *
	 * @var string @Assert\Length(max="11")
	 *      @ORM\Column(name="cohort", type="integer", length=11)
	 *     
	 *      @JMS\Expose
	 */
	private $cohort;
	
	/**
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Set organization
	 *
	 * @param \Synapse\CoreBundle\Entity\Organization $organization       	
	 */
	public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null) {
		$this->organization = $organization;
	}
	
	/**
	 *
	 * @return \Synapse\CoreBundle\Entity\Organization
	 */
	public function getOrganization() {
		return $this->organization;
	}
	
	/**
	 * Set person
	 *
	 * @param \Synapse\CoreBundle\Entity\Person $person
	 */
	public function setPerson(\Synapse\CoreBundle\Entity\Person $person = null) {
		$this->person = $person;
	}
	
	/**
	 *
	 * @return \Synapse\CoreBundle\Entity\Person
	 */
	public function getPerson() {
		return $this->person;
	}
	
	/**
	 * Set orgAcademicYear
	 *
	 * @param \Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear
	 */
	public function setOrgAcademicYear(\Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear = null) {
		$this->orgAcademicYear = $orgAcademicYear;
	}
	
	/**
	 * Get orgAcademicYear
	 *
	 * @return \Synapse\AcademicBundle\Entity\OrgAcademicYear
	 */
	public function getOrgAcademicYear() {
		return $this->orgAcademicYear;
	}
	
	/**
	 *
	 * @param int $cohort        	
	 */
	public function setCohort($cohort) {
		$this->cohort = $cohort;
	}
	
	/**
	 *
	 * @return int
	 */
	public function getCohort() {
		return $this->cohort;
	}
}