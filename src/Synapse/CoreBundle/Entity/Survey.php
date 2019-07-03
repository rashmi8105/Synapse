<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Survey
 *
 * @ORM\Table(name="survey", indexes={@ORM\Index(name="year_id", columns={"year_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\SurveyRepository")
 */
class Survey
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
     * @var string @ORM\Column(name="external_id", type="string", length=45, nullable=true)
     */
    private $externalId;
    
    /**
     *
     * @var \Year @ORM\ManyToOne(targetEntity="Synapse\AcademicBundle\Entity\Year")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="year_id", referencedColumnName="id")
     *      })
     */
    private $year;

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set externalId
     *
     * @param string $externalId            
     * @return EbiQuestion
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        
        return $this;
    }

    /**
     * Get externalId
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }
    
    /**
     * Set year
     *
     * @param \Synapse\AcademicBundle\Entity\Year $year
     * @return Survey
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
}