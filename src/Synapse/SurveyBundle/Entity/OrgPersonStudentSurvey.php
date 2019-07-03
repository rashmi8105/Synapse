<?php
namespace Synapse\SurveyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OrgPersonStudentSurvey
 *
 * @ORM\Table(name="org_person_student_survey", indexes={@ORM\Index(name="fk_org_person_student_survey_survey1_idx", columns={"survey_id"}),
 * @ORM\Index(name="fk_org_person_student_survey_organization1_idx", columns={"organization_id"}),
 * @ORM\Index(name="fk_org_person_student_survey_person1_idx", columns={"person_id"}),
 * @ORM\Index(name="org_person_student_survey_covering_index", columns={"organization_id", "survey_id", "person_id", "deleted_at"})},
 * uniqueConstraints={@ORM\UniqueConstraint(name="survey_unique_index", columns={"organization_id", "person_id", "survey_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\OrgPersonStudentSurveyRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */

class OrgPersonStudentSurvey extends BaseEntity
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
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $person;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Survey @ORM\ManyToOne(targetEntity="\Synapse\CoreBundle\Entity\Survey")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     *      })
     *      @JMS\Expose
     */
    private $survey;

    /**
     *
     * @var integer @ORM\Column(name="receive_survey", type="integer", options={"default" = 0})
     *      @JMS\Expose
     */
    private $receiveSurvey;

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
     *
     */
    public function setOrganization(\Synapse\CoreBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;
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
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     */
    public function setSurvey(\Synapse\CoreBundle\Entity\Survey $survey = null)
    {
        $this->survey = $survey;
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
     *
     * @param integer $receiveSurvey            
     */
    public function setReceiveSurvey($receiveSurvey)
    {
        $this->receiveSurvey = $receiveSurvey;
    }

    /**
     *
     * @return integer
     */
    public function getReceiveSurvey()
    {
        return $this->receiveSurvey;
    }
}