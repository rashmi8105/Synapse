<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class ToolSelectionDto
{

    /**
     * Tool Id
     *
     * @var int
     * @JMS\Type("integer")
     * @Assert\NotBlank()
     */
    private $toolId;

    /**
     * Tool Name
     *
     * @var string
     * @JMS\Type("string")
     */
    private $toolName;

    /**
     * Tool short_code
     *
     * @var string
     * @JMS\Type("string")
     */
    private $shortCode;

    /**
     * Tool can_access_with_aggregate_only_permission
     *
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $canAccessWithAggregateOnlyPermission;

    /**
     * Tool Selection
     *
     * @var boolean
     * @JMS\Type("boolean")
     */
    private $selection;

    /**
     * @return integer
     */
    public function getToolId()
    {
        return $this->toolId;
    }

    /**
     * @param integer $toolId
     */
    public function setToolId($toolId)
    {
        $this->toolId = $toolId;
    }

    /**
     * @return string
     */
    public function getToolName()
    {
        return $this->toolName;
    }

    /**
     * @param string $toolName
     */
    public function setToolName($toolName)
    {
        $this->toolName = $toolName;
    }

    /**
     * @return string
     */
    public function getShortCode()
    {
        return $this->shortCode;
    }

    /**
     * @param string $shortCode
     */
    public function setShortCode($shortCode)
    {
        $this->shortCode = $shortCode;
    }

    /**
     * @return boolean
     */
    public function getCanAccessWithAggregateOnlyPermission()
    {
        return $this->canAccessWithAggregateOnlyPermission;
    }

    /**
     * @param boolean $canAccessWithAggregateOnlyPermission
     */
    public function setCanAccessWithAggregateOnlyPermission($canAccessWithAggregateOnlyPermission)
    {
        $this->canAccessWithAggregateOnlyPermission = $canAccessWithAggregateOnlyPermission;
    }

    /**
     * @return boolean
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param boolean $selection
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
    }
}
