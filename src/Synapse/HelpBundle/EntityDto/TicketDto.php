<?php
namespace Synapse\HelpBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data Transfer Object for helpdesk tickets
 * *
 *
 * @package Synapse\HelpBundle\EntityDto
 *
 */
class TicketDto
{

    /**
     * Ticket Id.
     *
     * @var integer
     *
     *      @JMS\Type("integer")
     */
    private $id;

    /**
     * Subject message of a ticket.
     *
     * @var string
     *
     *      @JMS\Type("string")
     *      @Assert\NotBlank()
     *      @Assert\Length(
     *          min = 1,
     *          max = 150,
     *          minMessage = "subject must be at least {{ limit }} characters long",
     *          maxMessage = "subject cannot exceed  {{ limit }} characters."
     *      )
     */
    private $subject;

    /**
     * Description attached to a ticket.
     *
     * @var string
     *
     *      @JMS\Type("string")
     *      @Assert\NotBlank()
     *      @Assert\Length(min = 1,
     *          max = 65535,
     *          minMessage = "description must be at least {{ limit }} characters long",
     *          maxMessage = "description cannot exceed  {{ limit }} characters."
     *      )
     */
    private $description;

    /**
     * Ticket's category.
     *
     * @var string
     *
     *      @JMS\Type("string")
     *      @Assert\NotBlank()
     */
    private $category;

    /**
     * File name of the file attached to a ticket.
     *
     * @var string
     *
     *      @JMS\Type("string")
     */
    private $attachment;

    /**
     * @deprecated
     * requester
     *
     * @var string
     *
     *      @JMS\Type("Synapse\HelpBundle\EntityDto\TicketRequesterDto")
     */
    private $requester;


    /**
     * Gets the id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id.
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the subject
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Gets the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Gets the category.
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Sets the category.
     *
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Gets the attachment.
     *
     * @return string
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * Sets the attachment.
     *
     * @param string $attachment
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * @deprecated
     * Gets the requester.
     *
     * @return string
     */
    public function getRequester()
    {
        return $this->requester;
    }

    /**
     * @deprecated
     * Sets the requester.
     *
     * @param TicketDto $requester
     */
    public function setRequester($requester)
    {
        $this->requester = $requester;
    }
}