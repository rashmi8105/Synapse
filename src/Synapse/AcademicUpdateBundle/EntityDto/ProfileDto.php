<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;


class ProfileDto
{
   
    /**
     * Institution Specific Profile-items.
     *
     * @var array
     * @JMS\Type("array")
     *
     */
    private $isps;
    /**
     * EBI-specific Profile items.
     *
     * @var array
     * @JMS\Type("array")
     *
     */
    private $ebi;

    /**
     * Set EBI-specific profile items to a profile.
     *
     * @param array $ebi
     */
    public function setEbi($ebi)
    {
        $this->ebi = $ebi;
    }

    /**
     * Returns EBI-specific profile items from a profile.
     *
     * @return array
     */
    public function getEbi()
    {
        return $this->ebi;
    }

    /**
     * Set Institution Specific Profile-items to a profile.
     *
     * @param array $isps
     */
    public function setIsps($isps)
    {
        $this->isps = $isps;
    }

    /**
     * Returns Institution Specific Profile-items from a profile.
     *
     * @return array
     */
    public function getIsps()
    {
        return $this->isps;
    }

    
   
}