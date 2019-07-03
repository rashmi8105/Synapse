<?php

namespace Synapse\MapworksToolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * MapworksTool
 *
 * @ORM\Table(name="mapworks_tool")
 * @ORM\Entity(repositoryClass="Synapse\MapworksToolBundle\Repository\MapworksToolRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @JMS\ExclusionPolicy("all")
 */
class MapworksTool extends BaseEntity
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;


    /**
     *
     * @var string @ORM\Column(name="tool_name", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     * @JMS\Expose
     */
    private $toolName;


    /**
     *
     * @var string @ORM\Column(name="short_code", type="string", length=15, precision=0, scale=0, nullable=false, unique=false)
     * @JMS\Expose
     */
    private $shortCode;

    /**
     *
     * @var integer @ORM\Column(name="can_access_with_aggregate_only_permission", type="integer")
     * @JMS\Expose
     */
    private $canAccessWithAggregateOnlyPermission;

    /**
     *
     * @var integer @ORM\Column(name="tool_order", type="integer")
     * @JMS\Expose
     */
    private $toolOrder;

    /**
     * Set toolName
     *
     * @param string $toolName
     * @return MapworksTool
     */
    public function setToolName($toolName)
    {
        $this->toolName = $toolName;
        return $this;
    }

    /**
     * Get toolName
     *
     * @return string
     */
    public function getToolName()
    {
        return $this->toolName;
    }

    /**
     * Set shortCode
     *
     * @param string $shortCode
     * @return MapworksTool
     */
    public function setShortCode($shortCode)
    {
        $this->shortCode = $shortCode;
        return $this;
    }

    /**
     * Get shortCode
     *
     * @return string
     */
    public function getShortCode()
    {
        return $this->shortCode;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $canAccessWithAggregateOnlyPermission
     */
    public function setCanAccessWithAggregateOnlyPermission($canAccessWithAggregateOnlyPermission)
    {
        $this->canAccessWithAggregateOnlyPermission = $canAccessWithAggregateOnlyPermission;
    }

    /**
     * @return integer
     */
    public function getCanAccessWithAggregateOnlyPermission()
    {
        return $this->canAccessWithAggregateOnlyPermission;
    }

    /**
     * @return int
     */
    public function getToolOrder()
    {
        return $this->toolOrder;
    }

    /**
     * @param int $toolOrder
     */
    public function setToolOrder($toolOrder)
    {
        $this->toolOrder = $toolOrder;
    }


}
