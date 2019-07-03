<?php
namespace Synapse\DataBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 *
 *
 * @package Synapse\RestBundle\Entity
 *         
 */
class ProfileBlocksDto
{

    /**
     * Id of a profile block.
     *
     * @var string @JMS\Type("integer")
     *     
     */
    private $profileBlockId;

    /**
     * Name of a profile block.
     *
     * @var string @JMS\Type("string")
     *      @Assert\NotBlank()
     *      @Assert\Length(min = 1,
     *      max = 50,
     *      minMessage = "profileBlockName must be at least {{ limit }} characters long",
     *      maxMessage = "profileBlockName cannot be longer than {{ limit }} characters long"
     *      )
     */
    private $profileBlockName;

    /**
     * Profile items within a profile block.
     *
     * @JMS\Type("array")
     */
    private $profileItems;

    /**
     *
     * @param mixed $profileBlockId            
     */
    public function setProfileBlockId($profileBlockId)
    {
        $this->profileBlockId = $profileBlockId;
    }

    /**
     *
     * @return mixed
     */
    public function getProfileBlockId()
    {
        return $this->profileBlockId;
    }

    /**
     *
     * @param string $profileBlockName            
     */
    public function setProfileBlockName($profileBlockName)
    {
        $this->profileBlockName = $profileBlockName;
    }

    /**
     *
     * @return string
     */
    public function getProfileBlockName()
    {
        return $this->profileBlockName;
    }

    /**
     *
     * @param array $profileItems            
     */
    public function setProfileItems($profileItems)
    {
        $this->profileItems = $profileItems;
    }

    /**
     *
     * @return array
     */
    public function getProfileItems()
    {
        return $this->profileItems;
    }
}