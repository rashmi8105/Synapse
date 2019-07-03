<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;


class StaffDto
{
    /**
     * Determines whether all staff apply to a Request or not.
     *
     * @var boolean
     * @JMS\Type("boolean")
     *
     */
    private $isAll;
    
    /**
     * String that holds the IDs of selected staff members for a Request.
     *
     * @var string
     * @JMS\Type("string")
     *
     */
    private $selectedStaffIds;

    /**
     * Set whether all staff apply to a Request or not.
     *
     * @param boolean $isAll
     */
    public function setIsAll($isAll)
    {
        $this->isAll = $isAll;
    }

    /**
     * Returns whether all staff apply to a Request or not.
     *
     * @return boolean
     */
    public function getIsAll()
    {
        return $this->isAll;
    }

    /**
     * Set the IDs of the staff members that apply to a Request.
     *
     * @param string $selectedStaffIds
     */
    public function setSelectedStaffIds($selectedStaffIds)
    {
        $this->selectedStaffIds = $selectedStaffIds;
    }

    /**
     * Returns the IDs of staff members that apply to a Request.
     *
     * @return string
     */
    public function getSelectedStaffIds()
    {
        return $this->selectedStaffIds;
    }


   
   

}