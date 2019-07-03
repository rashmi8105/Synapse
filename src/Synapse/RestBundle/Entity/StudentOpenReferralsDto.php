<?php
namespace Synapse\RestBundle\Entity;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Student
 *
 * @package Synapse\RestBundle\Entity
 */
class StudentOpenReferralsDto
{

    /**
     * referral_id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $referralId;

    /**
     * reason_category_subitem_id
     * 
     * @var integer @JMS\Type("integer")
     */
    private $reasonCategorySubitemId;

    /**
     * reason_category_subitem
     * 
     * @var string @JMS\Type("string")
     */
    private $reasonCategorySubitem;

    /**
     * comment
     * 
     * @var string @JMS\Type("string")
     */
    private $comment;

    /**
     *
     * @return int
     */
    public function getReferralId()
    {
        return $this->referralId;
    }

    /**
     *
     * @param string $referralId            
     */
    public function setReferralId($referralId)
    {
        $this->referralId = $referralId;
    }

    /**
     *
     * @return int
     */
    public function getReasonCategorySubitemId()
    {
        return $this->reasonCategorySubitemId;
    }

    /**
     *
     * @param string $reasonCategorySubitemId            
     */
    public function setReasonCategorySubitemId($reasonCategorySubitemId)
    {
        $this->reasonCategorySubitemId = $reasonCategorySubitemId;
    }

    /**
     *
     * @param string $reasonCategorySubitem            
     */
    public function setReasonCategorySubitem($reasonCategorySubitem)
    {
        $this->reasonCategorySubitem = $reasonCategorySubitem;
    }

    /**
     *
     * @return string
     */
    public function getReasonCategorySubitem()
    {
        return $this->reasonCategorySubitem;
    }

    /**
     *
     * @param string $comment            
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}