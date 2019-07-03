<?php
namespace Synapse\RiskBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class RiskGroupDto
{

    /**
     * risk group id
     *
     * @var integer
     *
     *      @JMS\Type("integer")
     */
    private $id;

    /**
     * $riskModelName
     *
     * @var string
     *
     *      @JMS\Type("string")
     *      @Assert\NotBlank(message = "Risk Group Name should not be blank")
     */
    private $groupName;

    /**
     * $riskModelName
     *
     * @var string
     *
     *      @JMS\Type("string")
     */
    private $groupDescription;

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     *
     * @param string $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     *
     * @return string
     */
    public function getGroupDescription()
    {
        return $this->groupDescription;
    }

    /**
     *
     * @param string $groupDescription
     */
    public function setGroupDescription($groupDescription)
    {
        $this->groupDescription = $groupDescription;
    }
}