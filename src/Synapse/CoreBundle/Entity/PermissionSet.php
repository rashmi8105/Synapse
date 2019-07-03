<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PermissionSet
 *
 * @ORM\Table(name="ebi_permissionset")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\PermissionSetRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class PermissionSet extends BaseEntity
{

    /**
     *
     * @var boolean @ORM\Column(name="accesslevel_agg", type="boolean", nullable=true)
     */
    private $accesslevelAgg;

    /**
     *
     * @var boolean @ORM\Column(name="accesslevel_ind_agg", type="boolean", nullable=true)
     */
    private $accesslevelIndAgg;

    /**
     *
     * @var boolean @ORM\Column(name="is_active", type="boolean", length=1, nullable=true)
     */
    private $isActive;

    /**
     *
     * @var string @ORM\Column(name="risk_indicator", type="boolean", length=1, nullable=true)
     */
    private $riskIndicator;

    /**
     *
     * @var string @ORM\Column(name="intent_to_leave", type="boolean", length=1, nullable=true)
     */
    private $intentToLeave;

    /**
     *
     * @var datetime @ORM\Column(name="inactive_date", type="datetime", nullable=true)
     */
    private $inactiveDate;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="view_courses", type="boolean", length=1, nullable=true)
     *
     */
    private $viewCourses;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="create_view_academic_update", type="boolean", length=1, nullable=true)
     *
     */
    private $createViewAcademicUpdate;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="view_all_academic_update_courses", type="boolean", length=1, nullable=true)
     *
     */
    private $viewAllAcademicUpdateCourses;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="view_all_final_grades", type="boolean", length=1, nullable=true)
     *
     */
    private $viewAllFinalGrades;
    
    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Set accesslevelAgg
     *
     * @param boolean $accesslevelAgg            
     * @return EbiPermissionset
     */
    public function setAccesslevelAgg($accesslevelAgg)
    {
        $this->accesslevelAgg = $accesslevelAgg;
        
        return $this;
    }

    /**
     * Get accesslevelAgg
     *
     * @return boolean
     */
    public function getAccesslevelAgg()
    {
        return $this->accesslevelAgg;
    }

    /**
     * Set accesslevelIndAgg
     *
     * @param boolean $accesslevelIndAgg            
     * @return EbiPermissionset
     */
    public function setAccesslevelIndAgg($accesslevelIndAgg)
    {
        $this->accesslevelIndAgg = $accesslevelIndAgg;
        
        return $this;
    }

    /**
     * Get accesslevelIndAgg
     *
     * @return boolean
     */
    public function getAccesslevelIndAgg()
    {
        return $this->accesslevelIndAgg;
    }

    /**
     * Set riskIndicator
     *
     * @param string $riskIndicator            
     * @return EbiPermissionset
     */
    public function setRiskIndicator($riskIndicator)
    {
        $this->riskIndicator = $riskIndicator;
        
        return $this;
    }

    /**
     * Get riskIndicator
     *
     * @return string
     */
    public function getRiskIndicator()
    {
        return $this->riskIndicator;
    }

    /**
     * Set intentToLeave
     *
     * @param string $intentToLeave            
     * @return EbiPermissionset
     */
    public function setIntentToLeave($intentToLeave)
    {
        $this->intentToLeave = $intentToLeave;
        
        return $this;
    }

    /**
     * Get intentToLeave
     *
     * @return string
     */
    public function getIntentToLeave()
    {
        return $this->intentToLeave;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive            
     * @return EbiPermissionset
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

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
     * Set inactiveDate
     *
     * @param datetime $inactiveDate            
     * @return EbiPermissionset
     */
    public function setInactiveDate($inactiveDate)
    {
        $this->inactiveDate = $inactiveDate;
        
        return $this;
    }

    /**
     * Get inactiveDate
     *
     * @return datetime
     */
    public function getInactiveDate()
    {
        return $this->inactiveDate;
    }
    

    /**
     * @param boolean $createViewAcademicUpdate
     */
    public function setCreateViewAcademicUpdate($createViewAcademicUpdate)
    {
        $this->createViewAcademicUpdate = $createViewAcademicUpdate;
    }
    
    /**
     * @return boolean
     */
    public function getCreateViewAcademicUpdate()
    {
        return $this->createViewAcademicUpdate;
    }
    
    /**
     * @param boolean $viewAllAcademicUpdateCourses
     */
    public function setViewAllAcademicUpdateCourses($viewAllAcademicUpdateCourses)
    {
        $this->viewAllAcademicUpdateCourses = $viewAllAcademicUpdateCourses;
    }
    
    /**
     * @return boolean
     */
    public function getViewAllAcademicUpdateCourses()
    {
        return $this->viewAllAcademicUpdateCourses;
    }
    
    /**
     * @param boolean $viewAllFinalGrades
     */
    public function setViewAllFinalGrades($viewAllFinalGrades)
    {
        $this->viewAllFinalGrades = $viewAllFinalGrades;
    }
    
    /**
     * @return boolean
     */
    public function getViewAllFinalGrades()
    {
        return $this->viewAllFinalGrades;
    }
    
    /**
     * @param boolean $viewCourses
     */
    public function setViewCourses($viewCourses)
    {
        $this->viewCourses = $viewCourses;
    }
    
    /**
     * @return boolean
     */
    public function getViewCourses()
    {
        return $this->viewCourses;
    }
}