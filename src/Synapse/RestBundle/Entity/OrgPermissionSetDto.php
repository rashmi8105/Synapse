<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class OrgPermissionSetDto implements DtoInterface
{

    /**
     * Unique id of the permission template.
     *
     * @JMS\Type("integer")
     */
    private $permissionTemplateId;

    /**
     * Organization's id.
     *
     * @JMS\Type("integer")
     * @Assert\NotBlank(message = "Organization Id should not be blank")
     */
    private $organizationId;

    /**
     * Name of the permission template.
     * 
     * @var string
     * @JMS\Type("string")
     * @Assert\NotBlank(message = "Permission Template Name should not be blank")
     */
    private $permissionTemplateName;

    /**
     * Object representing the access level of a permission set.
     * 
     * @var AccessLevelDto
     * @JMS\Type("Synapse\RestBundle\Entity\AccessLevelDto")
     */
    private $accessLevel;
    
    /**
     * Object representing the courses accessible to a permission set.
     *
     * @var CoursesAccessDto
     * @JMS\Type("Synapse\RestBundle\Entity\CoursesAccessDto")
     */
    private $coursesAccess;

    /**
     * If True, then users with this permission set have access to all risk-indicator information.
     * 
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $riskIndicator;

    /**
     * If True, then users with this permission set have access to all intent-to-leave information.
     * 
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $intentToLeave;


    /**
     * If True, then users with this permission set have access to all retention-completion information.
     *
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $retentionCompletion;
    
    /**
     * If True, then users with this permission set have access to all current and future ISQs.
     *
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $currentFutureIsq;
    
    /**
     * Array of reports that are accessible by a permission set.
     *
     * @var array
     * @JMS\Type("array<Synapse\RestBundle\Entity\ReportSelectionDto>")
     */
    private $reportsAccess;

    /**
     * Array of profile block objects.
     *
     * @var array
     * @JMS\Type("array<Synapse\RestBundle\Entity\BlockDto>")
     */
    private $profileBlocks;

    /**
     * Array of institution-specific profile block objects.
     * 
     * @var array
     * @JMS\Type("array<Synapse\RestBundle\Entity\IspBlockDto>")
     */
    private $isp;

    /**
     * Array of survey block objects.
     * 
     * @var array
     * @JMS\Type("array<Synapse\RestBundle\Entity\BlockDto>")
     */
    private $surveyBlocks;

    /**
     * Array of feature block objects.
     * 
     * @var array
     * @JMS\Type("array<Synapse\RestBundle\Entity\FeatureBlockDto>")
     */
    private $features;

    /**
     * Array of institution-specific question block objects.
     * 
     * @var array
     * @JMS\Type("array<Synapse\RestBundle\Entity\IsqBlockDto>")
     */
    private $isq;

    /**
     * Date that a permission set was last updated.
     *
     * @JMS\Type("DateTime")
     */
    private $lastUpdated;

    /**
     * total number of tools
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $totalCount;

    /**
     * Array of tools the user has selected.
     *
     * @var ToolSelectionDto
     * @JMS\Type("array<Synapse\RestBundle\Entity\ToolSelectionDto>")
     */
    private $tools;

    /**
     * Array of groups within a permission set.
     *
     * @JMS\Type("array")
     */
    private $groups;

    /**
     *
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     *
     * @param mixed $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     *
     * @param array $group
     */
    public function addGroup(array $group)
    {
        if (empty($this->groups)) {
            $this->groups = $group;
        } else {
            $this->groups += $group;
        }
    }

    /**
     *
     * @return mixed
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     *
     * @param mixed $organizationId
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;
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
     * @param string $permissionTemplateName
     */
    public function setPermissionTemplateName($permissionTemplateName)
    {
        $this->permissionTemplateName = $permissionTemplateName;
    }

    /**
     * @return array
     */
    public function getReportsAccess()
    {
        return $this->reportsAccess;
    }

    /**
     *
     * @param array $reportsAccess
     */
    public function setReportsAccess($reportsAccess)
    {
        $this->reportsAccess = $reportsAccess;
    }

    /**
     * @return BlockDto[]
     */
    public function getProfileBlocks()
    {
        return $this->profileBlocks;
    }

    /**
     *
     * @param BlockDto[] $profileBlocks
     */
    public function setProfileBlocks($profileBlocks)
    {
        $this->profileBlocks = $profileBlocks;
    }

    /**
     * @param BlockDto $profileBlock
     */
    public function addProfileBlock(BlockDto $profileBlock)
    {
        $this->profileBlocks[] = $profileBlock;
    }

    /**
     *
     * @return AccessLevelDto
     */
    public function getAccessLevel()
    {
        return $this->accessLevel;
    }

    /**
     *
     * @param AccessLevelDto $accessLevel
     */
    public function setAccessLevel($accessLevel)
    {
        $this->accessLevel = $accessLevel;
    }

    /**
     * @return CoursesAccessDto
     */
    public function getCoursesAccess()
    {
        return $this->coursesAccess;
    }

    /**
     * @param CoursesAccessDto $coursesAccess
     */
    public function setCoursesAccess($coursesAccess)
    {
        $this->coursesAccess = $coursesAccess;
    }

    /**
     * @return mixed
     */
    public function getIsp()
    {
        return $this->isp;
    }

    /**
     *
     * @param mixed $isp
     */
    public function setIsp($isp)
    {
        $this->isp = $isp;
    }

    /**
     * @param Object $isp
     */
    public function addIsp($isp)
    {
        $this->isp[] = $isp;
    }

    /**
     * @return array
     */
    public function getIsq()
    {
        return $this->isq;
    }

    /**
     *
     * @param array $isq
     */
    public function setIsq($isq)
    {
        $this->isq = $isq;
    }

    /**
     * @param Object $isq
     */
    public function addIsq($isq)
    {
        $this->isq[] = $isq;
    }

    /**
     * @return BlockDto[]
     */
    public function getSurveyBlocks()
    {
        return $this->surveyBlocks;
    }

    /**
     *
     * @param BlockDto[] $surveyBlocks
     */
    public function setSurveyBlocks($surveyBlocks)
    {
        $this->surveyBlocks = $surveyBlocks;
    }

    /**
     * @param BlockDto $surveyBlock
     */
    public function addSurveyBlock(BlockDto $surveyBlock)
    {
        $this->surveyBlocks[] = $surveyBlock;
    }

    /**
     * @return array
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     *
     * @param array $features
     */
    public function setFeatures($features)
    {
        $this->features = $features;
    }

    /**
     * @param FeatureBlockDto $featureBlock
     */
    public function addFeature(FeatureBlockDto $featureBlock)
    {
        $this->features[] = $featureBlock;
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
    public function getLastUpdated()
    {
        return $this->lastUpdated;
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
     * @return boolean
     */
    public function getIntentToLeave()
    {
        return $this->intentToLeave;
    }

    /**
     *
     * @param boolean $intentToLeave
     */
    public function setIntentToLeave($intentToLeave)
    {
        $this->intentToLeave = $intentToLeave;
    }


    /**
     * @return boolean|null
     */
    public function getRetentionCompletion()
    {
        return $this->retentionCompletion;
    }

    /**
     * @param boolean $retentionCompletion
     */
    public function setRetentionCompletion($retentionCompletion)
    {
        $this->retentionCompletion = $retentionCompletion;
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
    public function getCurrentFutureIsq()
    {
        return $this->currentFutureIsq;
    }

    /**
     *
     * @param boolean $currentFutureIsq
     */
    public function setCurrentFutureIsq($currentFutureIsq)
    {
        $this->currentFutureIsq = $currentFutureIsq;
    }

    /**
     *
     * @return integer
     */
    public function getTotalCount(){
        return $this->totalCount;
    }

    /**
     *
     * @param integer $totalCount
     */
    public function setTotalCount($totalCount){
        $this->totalCount = $totalCount;
    }

    /**
     * @return ToolSelectionDto
     */
    public function getTools()
    {
        return $this->tools;
    }

    /**
     * @param ToolSelectionDto $tools
     */
    public function setTools($tools)
    {
        $this->tools = $tools;
    }

    /**
     * Assign entity properties using an array
     *
     * @param array $attributes assoc array of values to assign
     * @return null
     */
    public function fromArray(array $attributes)
    {
        $this->permissionTemplateId = (int)$attributes['permissionTemplateId'] ?: null;
        $this->organizationId = (int)$attributes['organizationId'] ?: null;
        $this->permissionTemplateName = isset($attributes['permissionTemplateName']) ? $attributes['permissionTemplateName'] : null;
        $this->accessLevel = isset($attributes['accessLevel']) ? $attributes['accessLevel'] : null;
        $this->coursesAccess = isset($attributes['coursesAccess']) ? $attributes['coursesAccess'] : null;
        $this->riskIndicator = isset($attributes['riskIndicator']) ? (bool)$attributes['riskIndicator'] : null;
        $this->intentToLeave = isset($attributes['intentToLeave']) ? (bool)$attributes['intentToLeave'] : null;
        $this->retentionCompletion = isset($attributes['retentionCompletion']) ? (bool)$attributes['retentionCompletion'] : null;
        $this->currentFutureIsq = isset($attributes['currentFutureIsq']) ? (bool)$attributes['currentFutureIsq'] : null;
        $this->reportsAccess = isset($attributes['reportsAccess']) ? $attributes['reportsAccess'] : [];
        $this->profileBlocks = isset($attributes['profileBlocks']) ? $attributes['profileBlocks'] : [];
        $this->isp = isset($attributes['isp']) ? $attributes['isp'] : [];
        $this->surveyBlocks = isset($attributes['surveyBlocks']) ? $attributes['surveyBlocks'] : [];
        $this->features = isset($attributes['features']) ? $attributes['features'] : [];
        $this->isq = isset($attributes['isq']) ? $attributes['isq'] : [];
        $this->lastUpdated = !empty($attributes['lastUpdated']) ? new \DateTime($attributes['lastUpdated']) : null;
        $this->groups = isset($attributes['groups']) ? $attributes['groups'] : null;
        $this->totalCount = isset($attributes['totalCount']) ? $attributes['totalCount'] : null;
        $this->tools = isset($attributes['tools']) ? $attributes['tools'] : null;
    }
}
