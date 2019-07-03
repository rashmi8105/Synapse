<?php
namespace Synapse\MultiCampusBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class PromoteUserDto
{

    /**
     * Id of person being promoted.
     *
     * @var integer @JMS\Type("integer")
     *      @Assert\NotBlank()
     */
    private $userId;

    /**
     * Tier level of a person.
     *
     * @var string @JMS\Type("string")
     *      @Assert\NotBlank()
     *      @Assert\Choice(choices = {"primary", "secondary"}, message = "Please select valid tier level")
     *     
     */
    private $tierLevel;

    /**
     * Id of a tier.
     *
     * @var integer @JMS\Type("integer")
     *      @Assert\NotBlank()
     */
    private $tierId;

    /**
     *
     * @param int $userId            
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     *
     * @param string $tierLevel            
     */
    public function setTierLevel($tierLevel)
    {
        $this->tierLevel = $tierLevel;
    }

    /**
     *
     * @return string
     */
    public function getTierLevel()
    {
        return $this->tierLevel;
    }

    /**
     *
     * @param int $tierId            
     */
    public function setTierId($tierId)
    {
        $this->tierId = $tierId;
    }

    /**
     *
     * @return int
     */
    public function getTierId()
    {
        return $this->tierId;
    }
}