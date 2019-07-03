<?php
namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * RiskLevels
 *
 * @ORM\Table(name="risk_level")
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\RiskLevelsRepository")
 */
class RiskLevels extends BaseEntity 
{

     /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     *      @JMS\Expose
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="risk_text", type="string", length=10, nullable=true)
     *      @JMS\Expose
     */
    private $riskText;

    /**
     *
     * @var string @ORM\Column(name="image_name", type="string", length=200, nullable=true)
     *      @JMS\Expose
     */
    private $imageName;
    
    /**
     *
     * @var string @ORM\Column(name="color_hex", type="string", length=10, nullable=true)
     *      @JMS\Expose
     */
    private $colorHex;

    /**
     *
     * @var int @ORM\Column(name="report_sequence", type="integer", nullable=true)
     *      @JMS\Expose
     */
    private $reportSequence;

    /**
     *
     * @param string $riskText            
     */
    public function setRiskText($riskText)
    {
        $this->riskText = $riskText;
        
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getRiskText()
    {
        return $this->riskText;
    }

    /**
     *
     * @param string $imageName            
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
        
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     *
     * @param int $id            
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     *
     * @param string $colorHex
     */
    public function setColorHex($colorHex)
    {
        $this->colorHex = $colorHex;
    
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getColorHex()
    {
        return $this->colorHex;
    }

    /**
     *
     * @param int $reportSequence
     */
    public function setReportSequence($reportSequence)
    {
        $this->reportSequence = $reportSequence;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function getReportSequence()
    {
        return $this->reportSequence;
    }
}