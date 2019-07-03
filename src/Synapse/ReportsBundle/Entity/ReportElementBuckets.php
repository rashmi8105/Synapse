<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * reports
 *
 * @ORM\Table(name="report_element_buckets",indexes={@ORM\Index(name="fk_report_element_buckets_report_elements1_idx", columns={"element_id"})})
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportElementBucketsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ReportElementBuckets extends BaseEntity
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
     * @var \Synapse\ReportsBundle\Entity\ReportSectionElements @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\ReportSectionElements")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="element_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $elementId;

    /**
     *
     * @var string @ORM\Column(name="bucket_name", type="string", length=45, nullable=true)
     *      @JMS\Expose
     */
    private $bucketName;

    /**
     *
     * @var string @ORM\Column(name="bucket_text", type="string", length=1000, nullable=true)
     */
    private $bucketText;

    /**
     *
     * @var string @ORM\Column(name="range_min", type="decimal", precision=8, scale=4, nullable=true, unique=false)
     */
    private $rangeMin;

    /**
     *
     * @var string @ORM\Column(name="range_max", type="decimal", precision=8, scale=4, nullable=true, unique=false)
     */
    private $rangeMax;
    
    /**
     *
     * @var string @ORM\Column(name="is_choices", type="integer", nullable=true)
     */
    private $isChoices;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set elementId
     *
     * @param \Synapse\ReportsBundle\Entity\reports $elementId            
     * @return ReportElementBuckets
     */
    public function setElementId(\Synapse\ReportsBundle\Entity\ReportSectionElements $elementId = null)
    {
        $this->elementId = $elementId;
        
        return $this;
    }

    /**
     * Get elementId
     *
     * @return \Synapse\ReportsBundle\Entity\ReportSectionElements
     */
    public function getElementId()
    {
        return $this->elementId;
    }

    /**
     * Set bucketName
     *
     * @param
     *            string bucketName
     *            
     * @return ReportElementBuckets
     */
    public function setBucketName($bucketName)
    {
        $this->bucketName = $bucketName;
        
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getBucketName()
    {
        return $this->bucketName;
    }

    /**
     * Set bucketText
     *
     * @param
     *            string bucketText
     *            
     * @return ReportElementBuckets
     */
    public function setBucketText($bucketText)
    {
        $this->bucketText = $bucketText;
        
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getBucketText()
    {
        return $this->bucketText;
    }

    /**
     * Set rangeMin
     */
    public function setRangeMin($rangeMin)
    {
        $this->rangeMin = $rangeMin;
        
        return $this;
    }

    /**
     * Get rangeMin
     */
    public function getRangeMin()
    {
        return $this->rangeMin;
    }

    /**
     * Set rangeMax
     */
    public function setRangeMax($rangeMax)
    {
        $this->rangeMax = $rangeMax;
        
        return $this;
    }

    /**
     * Get rangeMax
     */
    public function getRangeMax()
    {
        return $this->rangeMax;
    }

    /**
     * @param string $isChoices
     */
    public function setIsChoices($isChoices)
    {
        $this->isChoices = $isChoices;
    }

    /**
     * @return string
     */
    public function getIsChoices()
    {
        return $this->isChoices;
    }


}