<?php
namespace Synapse\HelpBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for
 * *
 *
 * @package Synapse\HelpBundle\EntityDto
 *         
 */
class HelpDto
{

    /**
     * Id of a single help item.
     *
     * @var integer
     *
     *      @JMS\Type("integer")
     */
    private $id;

    /**
     * Title(name) of a help item.
     *
     * @var string
     *
     *      @JMS\Type("string")
     *      @Assert\NotBlank()
     *      @Assert\Length(min = 1,
     *      max = 80,
     *      minMessage = "Help Title must be at least {{ limit }} characters long",
     *      maxMessage = "Help Title cannot be longer than {{ limit }} characters long"
     *      )
     *     
     */
    private $title;

    /**
     * Description of a help item.
     *
     * @var string
     *
     *      @JMS\Type("string")
     *      @Assert\Length(min = 0,
     *      max = 140,
     *      minMessage = "Help Description must be at least {{ limit }} characters long",
     *      maxMessage = "Help Description cannot be longer than {{ limit }} characters long"
     *      )
     */
    private $description;

    /**
     * Link to a help document.
     *
     * @var string
     *
     *      @JMS\Type("string")
     *      @Assert\NotBlank()
     */
    private $link;
    
    /**
     * File name associated with a help document.
     *
     * @var string @JMS\Type("string")
     */
    private $fileName;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }
    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }
}