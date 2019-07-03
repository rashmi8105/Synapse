<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * reports
 *
 * @ORM\Table(name="report_tips",indexes={@ORM\Index(name="fk_report_tips_report_sections1_idx", columns={"section_id"})})
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportTipsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ReportTips extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\ReportsBundle\Entity\ReportSections @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\ReportSections")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="section_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $sectionId;

    /**
     *
     * @var string @ORM\Column(name="title", type="string", length=45, nullable=true)
     *      @JMS\Expose
     */
    private $title;

    /**
     *
     * @var string @ORM\Column(name="description", type="string", length=1000, nullable=true)
     */
    private $description;
    
    /**
     *
     * @var integer @ORM\Column(name="sequence", type="smallint", precision=0, scale=0, nullable=true, unique=false)
     */
    private $sequence;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sectionId
     *
     * @param \Synapse\ReportsBundle\Entity\ReportSections $sectionId            
     * @return ReportElementBuckets
     */
    public function setSectionId(\Synapse\ReportsBundle\Entity\ReportSections $sectionId = null)
    {
        $this->sectionId = $sectionId;
        
        return $this;
    }

    /**
     * Get sectionId
     *
     * @return \Synapse\ReportsBundle\Entity\ReportSections
     */
    public function getSectionId()
    {
        return $this->sectionId;
    }

    /**
     * Set name
     *
     * @param
     *            string title
     * @return ReportTips
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description            
     * @return ReportTips
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set sequence
     *
     * @param integer $sequence            
     * @return ReportSections
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
}