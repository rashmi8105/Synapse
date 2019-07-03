<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;


class StaticListDto
{
    /**
     * Determines whether all Static IDs apply to a Request or not.
     *
     * @var boolean
     * @JMS\Type("boolean")
     *
     */
    private $isAll;
    
    /**
     * The string that holds the Ids of the staticIds that apply to a Request.
     *
     * @var string
     * @JMS\Type("string")
     *
     */
    private $selectedStaticIds;

    /**
     * Set whether all staticIds apply to a Request or not.
     *
     * @param boolean $isAll
     */
    public function setIsAll($isAll)
    {
        $this->isAll = $isAll;
    }

    /**
     * Returns whether or not all staticIds apply to a Request or not.
     *
     * @return boolean
     */
    public function getIsAll()
    {
        return $this->isAll;
    }

    /**
     * Set the staticIds that apply to a Request.
     *
     * @param string $selectedStaticIds
     */
    public function setSelectedStaticIds($selectedStaticIds)
    {
        $this->selectedStaticIds = $selectedStaticIds;
    }

    /**
     * Returns the staticIds that apply to a Request.
     *
     * @return string
     */
    public function getSelectedStaticIds()
    {
        return $this->selectedStaticIds;
    }


    

}