<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class PermissionSetDto
{

    /**
     * Id of a permission template.
     *
     * @JMS\Type("integer")
     */
    private $permissionTemplateId;

    /**
     * Language id. Always 1(English).
     *
     * @JMS\Type("integer")
     */
    private $langId;

    /**
     * Name of a permission template.
     * 
     * @var string
     * @JMS\Type("string")
     * @Assert\NotBlank()
     */
    private $permissionTemplateName;

    /**
     * Status of a permission template.
     * 
     * @var string
     * @JMS\Type("string")
     */
    private $permissionTemplateStatus;

    /**
     * Date that a permission template was last updated.
     *
     * @JMS\Type("DateTime")
     */
    private $permissionTemplateLastUpdated;

    /**
     * Date that a permission template was archived last.
     *
     * @JMS\Type("DateTime")
     */
    private $permissionTemplateLastArchived;

    /**
     * Object representing the access level of a permission set.
     * 
     * @var Object
     * @JMS\Type("Synapse\RestBundle\Entity\AccessLevelDto")
     * @Assert\NotBlank()
     */
    private $accessLevel;
    
    /**
     * Object containing the courses that a permission set has access to.
     *
     * @var Object
     * @JMS\Type("Synapse\RestBundle\Entity\CoursesAccessDto")
     */
    private $coursesAccess;
    
    /**
     * If True, this permission set has access to risk indicators.
     * 
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $riskIndicator;

    /**
     * If True, this permission set has access to a student's intentToLeave.
     * 
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $intentToLeave;

    /**
     * Object representing the profile blocks that a permission set has access to.
     * 
     * @var Object
     * @JMS\Type("array<Synapse\RestBundle\Entity\BlockDto>")
     */
    private $profileBlocks;

    /**
     * Object representing the survey blocks that a permission set has access to.
     * 
     * @var Object
     * @JMS\Type("array<Synapse\RestBundle\Entity\BlockDto>")
     */
    private $surveyBlocks;

    /**
     * Object representing the features that a permission set has access to.
     * 
     * @var Object
     * @JMS\Type("array<Synapse\RestBundle\Entity\FeatureBlockDto>")
     */
    private $features;

    /**
     * Date that a permission set was last updated.
     *
     * @JMS\Type("DateTime")
     */
    private $lastUpdated;

    /**
     * Date that a permission set was last archived.
     *
     * @JMS\Type("DateTime")
     */
    private $lastArchived;

    /**
     *
     * @param boolean $intentToLeave            
     */
    public function setIntentToLeave($intentToLeave)
    {
        $this->intentToLeave = $intentToLeave;
    }

    /**
     *
     * @return boolean
     */
    public function getIntentToLeave()
    {
        return $this->intentToLeave;
    }

    /**
     *
     * @param string $permissionTemplateName            
     */
    public function setPermissionTemplateName($permissionTemplateName)
    {
        $this->permissionTemplateName = $permissionTemplateName;
    }

    /**
     *
     * @return string
     */
    public function getPermissionTemplateName()
    {
        return $this->permissionTemplateName;
    }

    /**
     *
     * @param Object $profileBlocks            
     */
    public function setProfileBlocks($profileBlocks)
    {
        $this->profileBlocks = $profileBlocks;
    }

    /**
     *
     * @return Object
     */
    public function getProfileBlocks()
    {
        return $this->profileBlocks;
    }

    /**
     *
     * @param boolean $riskIndicator            
     */
    public function setRiskIndicator($riskIndicator)
    {
        $this->riskIndicator = $riskIndicator;
    }

    /**
     *
     * @return boolean
     */
    public function getRiskIndicator()
    {
        return $this->riskIndicator;
    }

    /**
     *
     * @param Object $accessLevel            
     */
    public function setAccessLevel($accessLevel)
    {
        $this->accessLevel = $accessLevel;
    }

    /**
     *
     * @return Object
     */
    public function getAccessLevel()
    {
        return $this->accessLevel;
    }

    /**
     * @param Object $coursesAccess
     */
    public function setCoursesAccess($coursesAccess)
    {
        $this->coursesAccess = $coursesAccess;
    }
    
    /**
     * @return Object
     */
    public function getCoursesAccess()
    {
        return $this->coursesAccess;
    }
    
    /**
     *
     * @param mixed $surveyBlocks            
     */
    public function setSurveyBlocks($surveyBlocks)
    {
        $this->surveyBlocks = $surveyBlocks;
    }

    /**
     *
     * @return mixed
     */
    public function getSurveyBlocks()
    {
        return $this->surveyBlocks;
    }

    /**
     *
     * @param mixed $features            
     */
    public function setFeatures($features)
    {
        $this->features = $features;
    }

    /**
     *
     * @return mixed
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     *
     * @param integer $langId            
     */
    public function setLangId($langId)
    {
        $this->langId = $langId;
    }

    /**
     *
     * @return integer
     */
    public function getLangId()
    {
        return $this->langId;
    }

    /**
     *
     * @param mixed $permissionTemplateId            
     */
    public function setPermissionTemplateId($permissionTemplateId)
    {
        $this->permissionTemplateId = $permissionTemplateId;
    }

    /**
     *
     * @return mixed
     */
    public function getPermissionTemplateId()
    {
        return $this->permissionTemplateId;
    }

    /**
     *
     * @param mixed $lastUpdated            
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     *
     * @return mixed
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     *
     * @param mixed $lastArchived            
     */
    public function setLastArchived($lastArchived)
    {
        $this->lastArchived = $lastArchived;
    }

    /**
     *
     * @return mixed
     */
    public function getLastArchived()
    {
        return $this->lastArchived;
    }

    /**
     *
     * @param string $permissionTemplateStatus            
     */
    public function setPermissionTemplateStatus($permissionTemplateStatus)
    {
        $this->permissionTemplateStatus = $permissionTemplateStatus;
    }

    /**
     *
     * @return string
     */
    public function getPermissionTemplateStatus()
    {
        return $this->permissionTemplateStatus;
    }

    /**
     *
     * @param mixed $permissionTemplateLastUpdated            
     */
    public function setPermissionTemplateLastUpdated($permissionTemplateLastUpdated)
    {
        $this->permissionTemplateLastUpdated = $permissionTemplateLastUpdated;
    }

    /**
     *
     * @return mixed
     */
    public function getPermissionTemplateLastUpdated()
    {
        return $this->permissionTemplateLastUpdated;
    }

    /**
     *
     * @param mixed $permissionTemplateLastArchived            
     */
    public function setPermissionTemplateLastArchived($permissionTemplateLastArchived)
    {
        $this->permissionTemplateLastArchived = $permissionTemplateLastArchived;
    }

    /**
     *
     * @return mixed
     */
    public function getPermissionTemplateLastArchived()
    {
        return $this->permissionTemplateLastArchived;
    }
}