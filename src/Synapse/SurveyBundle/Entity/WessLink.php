<?php
namespace Synapse\SurveyBundle\Entity;

use Synapse\CoreBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * WessLink
 *
 * @ORM\Table(name="wess_link", indexes={@ORM\Index(name="fk_wess_link_organization1", columns={"org_id"}), @ORM\Index(name="fk_wess_link_survey1_idx", columns={"survey_id"}), @ORM\Index(name="fk_wess_link_year1_idx", columns={"year_id"})})
 * @ORM\Entity(repositoryClass="Synapse\SurveyBundle\Repository\WessLinkRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class WessLink extends BaseEntity
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
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id")
     *      })
     */
    private $organization;

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
     * @var integer @ORM\Column(name="cohort_code", type="integer", nullable=true)
     */
    private $cohortCode;

    /**
     *
     * @var integer @ORM\Column(name="wess_survey_id", type="integer", nullable=true)
     */
    private $wessSurveyId;

    /**
     *
     * @var integer @ORM\Column(name="wess_cohort_id", type="integer", nullable=true)
     */
    private $wessCohortId;

    /**
     *
     * @var integer @ORM\Column(name="wess_order_id", type="integer", nullable=true)
     */
    private $wessOrderId;

    /**
     *
     * @var integer @ORM\Column(name="wess_launchedflag", type="integer", nullable=true)
     */
    private $wessLaunchedflag;

    /**
     *
     * @var integer @ORM\Column(name="wess_maporder_key", type="integer", nullable=true)
     */
    private $wessMaporderKey;

    /**
     *
     * @var integer @ORM\Column(name="wess_prod_year", type="integer", nullable=true)
     */
    private $wessProdYear;

    /**
     *
     * @var integer @ORM\Column(name="wess_cust_id", type="integer", nullable=true)
     */
    private $wessCustId;

    /**
     *
     * @var string @ORM\Column(name="status", type="string", columnDefinition="ENUM('open','ready','launched','closed')", nullable=true)
     */
    private $status;

    /**
     *
     * @var \Date @ORM\Column(name="open_date", type="datetime", nullable=true)
     */
    private $openDate;

    /**
     *
     * @var \Date @ORM\Column(name="close_date", type="datetime", nullable=true)
     */
    private $closeDate;

    /**
     *
     * @var \Synapse\AcademicBundle\Entity\Year @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\Year")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="year_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $year;

    /**
     *
     * @var integer @ORM\Column(name="wess_admin_link", type="string", length=200, nullable=true)
     */
    private $wessAdminLink;

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
     * @return WessLink
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
     * Set survey
     *
     * @param \Synapse\CoreBundle\Entity\Survey $survey            
     * @return WessLink
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
     * Set cohortCode
     *
     * @param integer $cohortCode            
     * @return WessLink
     */
    public function setCohortCode($cohortCode)
    {
        $this->cohortCode = $cohortCode;
        
        return $this;
    }

    /**
     * Get cohortCode
     *
     * @return integer
     */
    public function getCohortCode()
    {
        return $this->cohortCode;
    }

    /**
     * Set wessSurveyId
     *
     * @param integer $wessSurveyId            
     * @return WessLink
     */
    public function setWessSurveyId($wessSurveyId)
    {
        $this->wessSurveyId = $wessSurveyId;
        
        return $this;
    }

    /**
     * Get wessSurveyId
     *
     * @return integer
     */
    public function getWessSurveyId()
    {
        return $this->wessSurveyId;
    }

    /**
     * Set wessCohortId
     *
     * @param integer $wessCohortId            
     * @return WessLink
     */
    public function setWessCohortId($wessCohortId)
    {
        $this->wessCohortId = $wessCohortId;
        
        return $this;
    }

    /**
     * Get wessCohortId
     *
     * @return integer
     */
    public function getWessCohortId()
    {
        return $this->wessCohortId;
    }

    /**
     * Set wessOrderId
     *
     * @param integer $wessOrderId            
     * @return WessLink
     */
    public function setWessOrderId($wessOrderId)
    {
        $this->wessOrderId = $wessOrderId;
        
        return $this;
    }

    /**
     * Get wessOrderId
     *
     * @return integer
     */
    public function getWessOrderId()
    {
        return $this->wessOrderId;
    }

    /**
     * Set wessLaunchedflag
     *
     * @param integer $wessLaunchedflag            
     * @return WessLink
     */
    public function setWessLaunchedflag($wessLaunchedflag)
    {
        $this->wessLaunchedflag = $wessLaunchedflag;
        
        return $this;
    }

    /**
     * Get wessLaunchedflag
     *
     * @return integer
     */
    public function getWessLaunchedflag()
    {
        return $this->wessLaunchedflag;
    }

    /**
     * Set wessMaporderKey
     *
     * @param integer $wessMaporderKey            
     * @return WessLink
     */
    public function setWessMaporderKey($wessMaporderKey)
    {
        $this->wessMaporderKey = $wessMaporderKey;
        
        return $this;
    }

    /**
     * Get wessMaporderKey
     *
     * @return integer
     */
    public function getWessMaporderKey()
    {
        return $this->wessMaporderKey;
    }

    /**
     * Set wessProdYear
     *
     * @param integer $wessProdYear            
     * @return WessLink
     */
    public function setWessProdYear($wessProdYear)
    {
        $this->wessProdYear = $wessProdYear;
        
        return $this;
    }

    /**
     * Get wessProdYear
     *
     * @return integer
     */
    public function getWessProdYear()
    {
        return $this->wessProdYear;
    }

    /**
     * Set wessCustId
     *
     * @param integer $wessCustId            
     * @return WessLink
     */
    public function setWessCustId($wessCustId)
    {
        $this->wessCustId = $wessCustId;
        
        return $this;
    }

    /**
     * Get wessCustId
     *
     * @return integer
     */
    public function getWessCustId()
    {
        return $this->wessCustId;
    }

    /**
     * Set status
     *
     * @param string $status            
     * @return WessLink
     */
    public function setStatus($status)
    {
        $this->status = $status;
        
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param \DateTime $openDate            
     */
    public function setOpenDate($openDate)
    {
        $this->openDate = $openDate;
        return $this;
    }

    /**
     *
     * @return \DateTime
     */
    public function getOpenDate()
    {
        return $this->openDate;
    }

    /**
     *
     * @param \DateTime $closeDate            
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;
        return $this;
    }

    /**
     *
     * @return \DateTime
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }

    /**
     * Set year
     *
     * @param \Synapse\AcademicBundle\Entity\Year $year            
     * @return WessLink
     */
    public function setYear(\Synapse\AcademicBundle\Entity\Year $year = null)
    {
        $this->year = $year;
        
        return $this;
    }

    /**
     * Get year
     *
     * @return \Synapse\AcademicBundle\Entity\Year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set wessAdminLink
     *
     * @param string $wessAdminLink            
     * @return WessLink
     */
    public function setWessAdminLink($wessAdminLink)
    {
        $this->wessAdminLink = $wessAdminLink;
        
        return $this;
    }

    /**
     * Get wessAdminLink
     *
     * @return string
     */
    public function getWessAdminLink()
    {
        return $this->wessAdminLink;
    }
}