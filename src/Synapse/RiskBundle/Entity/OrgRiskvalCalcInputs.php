<?php
namespace Synapse\RiskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * OrgRiskvalCalcInputs
 *
 * @ORM\Table(name="org_riskval_calc_inputs", indexes={@ORM\Index(name="fk_org_riskval_calc_inputs_person1_idx", columns={"person_id"}), @ORM\Index(name="fk_org_riskval_calc_inputs_organization1_idx", columns={"org_id"})})
 * @ORM\Entity(repositoryClass="Synapse\RiskBundle\Repository\OrgRiskvalCalcInputsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class OrgRiskvalCalcInputs extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @var \Synapse\CoreBundle\Entity\Organization @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Organization")
     *      @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="org_id", referencedColumnName="id", nullable=true)
     *      })
     */
    private $org;

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
     * @var string @ORM\Column(name="is_riskval_calc_required", type="string", options={"default":"n"}, precision=0, scale=0, nullable=false, unique=false,columnDefinition="ENUM('y','n')")
     */
    private $isRiskvalCalcRequired;
    
    /**
     *
     * @var string @ORM\Column(name="is_success_marker_calc_reqd", type="string", options={"default":"n"}, precision=0, scale=0, nullable=false, unique=false,columnDefinition="ENUM('y','n')")
     */
    private $isSuccessMarkerCalcReqd;
    
    /**
     *
     * @var string @ORM\Column(name="is_talking_point_calc_reqd", type="string", options={"default":"n"}, precision=0, scale=0, nullable=false, unique=false,columnDefinition="ENUM('y','n')")
     */
    private $isTalkingPointCalcReqd;
    
    /**
     *
     * @var string @ORM\Column(name="is_factor_calc_reqd", type="string", options={"default":"n"}, precision=0, scale=0, nullable=false, unique=false,columnDefinition="ENUM('y','n')")
     */
    private $isFactorCalcReqd;

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
     * Set org
     *
     * @param \Synapse\CoreBundle\Entity\Organization $org            
     */
    public function setOrg(\Synapse\CoreBundle\Entity\Organization $org = null)
    {
        $this->org = $org;
        
        return $this;
    }

    /**
     * Get org
     *
     * @return \Synapse\CoreBundle\Entity\Organization
     */
    public function getOrg()
    {
        return $this->org;
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
     * Set isRiskvalCalcRequired
     *
     * @param string $isRiskvalCalcRequired            
     */
    public function setIsRiskvalCalcRequired($isRiskvalCalcRequired)
    {
        $this->isRiskvalCalcRequired = $isRiskvalCalcRequired;
        
        return $this;
    }

    /**
     * Get isRiskvalCalcRequired
     *
     * @return string
     */
    public function getIsRiskvalCalcRequired()
    {
        return $this->isRiskvalCalcRequired;
    }
    
    /**
     * Set isSuccessMarkerCalcReqd
     *
     * @param string $isSuccessMarkerCalcReqd            
     */
    public function setIsSuccessMarkerCalcReqd($isSuccessMarkerCalcReqd)
    {
        $this->isSuccessMarkerCalcReqd = $isSuccessMarkerCalcReqd;
        
        return $this;
    }

    /**
     * Get isRiskvalCalcRequired
     *
     * @return string
     */
    public function getIsSuccessMarkerCalcReqd()
    {
        return $this->isSuccessMarkerCalcReqd;
    }
    
    /**
     * Set isTalkingPointCalcReqd
     *
     * @param string $isTalkingPointCalcReqd            
     */
    public function setIsTalkingPointCalcReqd($isTalkingPointCalcReqd)
    {
        $this->isTalkingPointCalcReqd = $isTalkingPointCalcReqd;
        
        return $this;
    }

    /**
     * Get isTalkingPointCalcReqd
     *
     * @return string
     */
    public function getIsTalkingPointCalcReqd()
    {
        return $this->isTalkingPointCalcReqd;
    }
    
     /**
     * Set isFactorCalcReqd
     *
     * @param string $isFactorCalcReqd            
     */
    public function setIsFactorCalcReqd($isFactorCalcReqd)
    {
        $this->isFactorCalcReqd = $isFactorCalcReqd;
        
        return $this;
    }

    /**
     * Get isFactorCalcReqd
     *
     * @return string
     */
    public function getIsFactorCalcReqd()
    {
        return $this->isFactorCalcReqd;
    }
}