<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * report_bucket_range
 *
 * @ORM\Table(name="report_bucket_range",indexes={@ORM\Index(name="fk_report_element_buckets_report_elements1_idx", columns={"element_id"})})
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\ReportBucketRangeRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class ReportBucketRange extends BaseEntity
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
     * @var \Synapse\ReportsBundle\Entity\ReportElementBuckets @ORM\ManyToOne(targetEntity="Synapse\ReportsBundle\Entity\ReportElementBuckets")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="element_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $elementId;
    
    /**
     *
     * @var string @ORM\Column(name="value", type="decimal", precision=8, scale=4, nullable=true, unique=false)
     */
    private $value;
    
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
     * @param \Synapse\ReportsBundle\Entity\ReportElementBuckets $elementId
     * @return ReportElementBuckets
     */
    public function setElementId(\Synapse\ReportsBundle\Entity\ReportElementBuckets $elementId = null)
    {
    	$this->elementId = $elementId;
    
    	return $this;
    }
    
    /**
     * Get elementId
     *
     * @return \Synapse\ReportsBundle\Entity\ReportElementBuckets
     */
    public function getElementId()
    {
    	return $this->elementId;
    }
    
    /**
     * Set value
     */
    public function setValue($value)
    {
    	$this->value = $value;
    
    	return $this;
    }
    
    /**
     * Get value
     */
    public function getValue()
    {
    	return $this->value;
    }
}