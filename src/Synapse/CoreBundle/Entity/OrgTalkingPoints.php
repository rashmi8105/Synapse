<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * org_talking_points
 *
 * @ORM\Table(name="org_talking_points", indexes={@ORM\Index(name="fk_org_talking_points_organization1_idx",columns={"organization_id"}),@ORM\Index(name="fk_org_talking_points_person1_idx",columns={"person_id"}),@ORM\Index(name="fk_org_talking_points_talking_points1_idx", columns={"talking_points_id"}),@ORM\Index(name="fk_org_talking_points_Survey1_idx", columns={"survey_id"})});
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\OrgTalkingPointsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgTalkingPoints extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *     
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     *      })
     *      @Assert\NotBlank()
     */
    private $organization;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Person @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *      })
     */
    private $person;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\TalkingPoints @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\TalkingPoints")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="talking_points_id", referencedColumnName="id")
     *      })
     */
    private $talkingPoints;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Survey @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Survey")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     *      })
     */
    private $survey;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicYear @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicYear")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_year_id", referencedColumnName="id")
     *      })
     */
    private $orgAcademicYear;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\OrgAcademicTerms @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\OrgAcademicTerms")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_academic_terms_id", referencedColumnName="id")
     *      })
     */
    private $orgAcademicTerms;

    /**
     *
     * @var string @ORM\Column(name="response", type="string", length=45, nullable=true)
     */
    private $response;

    /**
     *
     * @var \DateTime @ORM\Column(name="source_modified_at", type="datetime", nullable=true)
     */
    private $sourceModifiedAt;

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
     * @return OrgTalkingPoints
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
     * @return OrgTalkingPoints
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
     * Set talkingPoints
     *
     * @param \Synapse\CoreBundle\Entity\TalkingPoints $talkingPoints
     * @return OrgTalkingPoints
     */
    public function setTalkingPoints(\Synapse\CoreBundle\Entity\TalkingPoints $talkingPoints = null)
    {
        $this->talkingPoints = $talkingPoints;

        return $this;
    }

    /**
     * Get talkingPoints
     *
     * @return \Synapse\CoreBundle\Entity\talkingPoints
     */
    public function getTalkingPoints()
    {
        return $this->talkingPoints;
    }

    /**
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     * @return OrgTalkingPoints
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
     * Set orgAcademicYear
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear
     * @return OrgTalkingPoints
     */
    public function setOrgAcademicYear(\Synapse\AcademicBundle\Entity\OrgAcademicYear $orgAcademicYear = null)
    {
        $this->orgAcademicYear = $orgAcademicYear;

        return $this;
    }

    /**
     * Get orgAcademicYear
     *
     * @return \Synapse\AcademicBundle\Entity\OrgAcademicYear
     */
    public function getOrgAcademicYear()
    {
        return $this->orgAcademicYear;
    }

    /**
     * Set orgAcademicTerms
     *
     * @param \Synapse\AcademicBundle\Entity\OrgAcademicTerms $orgAcademicTerms
     * @return OrgTalkingPoints
     */
    public function setOrgAcademicTerms(\Synapse\AcademicBundle\Entity\OrgAcademicTerms $orgAcademicTerms = null)
    {
        $this->orgAcademicTerms = $orgAcademicTerms;

        return $this;
    }

    /**
     * Get orgAcademicTerms
     *
     * @return \Synapse\AcademicBundle\Entity\OrgAcademicTerms
     */
    public function getOrgAcademicTerms()
    {
        return $this->orgAcademicTerms;
    }

    /**
     * Set response
     *
     * @param string $response            
     * @return OrgTalkingPoints
     */
    public function setResponse($response)
    {
        $this->response = $response;
        
        return $this;
    }

    /**
     * Get response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set sourceModifiedAt
     *
     * @param \DateTime $sourceModifiedAt
     * @return OrgTalkingPoints
     */
    public function setSourceModifiedAt($sourceModifiedAt)
    {
        $this->sourceModifiedAt = $sourceModifiedAt;

        return $this;
    }

    /**
     * Get sourceModifiedAt
     *
     * @return \DateTime
     */
    public function getSourceModifiedAt()
    {
        return $this->sourceModifiedAt;
    }
}