<?php
namespace Synapse\AcademicUpdateBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;

/**
 * Data Transfer Object for Academic Update
 *
 * @package Synapse\RestBundle\Entity
 */
class AcademicUpdateResponseDto
{

    /**
     * academic_updates_open
     *
     * @var string @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsResponseDto>")
     *     
     */
    private $academicUpdatesOpen;
    
    /**
     * academic_updates_closed
     *
     * @var string @JMS\Type("array<Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsResponseDto>")
     *
     */
    private $academicUpdatesClosed;
    
    /**
     *
     * @param array $academicUpdatesOpen
     */
    public function setAcademicUpdatesOpen($academicUpdatesOpen)
    {
        $this->academicUpdatesOpen = $academicUpdatesOpen;
    }
    
    /**
     *
     * @return array
     */
    public function getAcademicUpdatesOpen()
    {
        return $this->academicUpdatesOpen;
    }
    
    /**
     *
     * @param array $academicUpdatesClosed
     */
    public function setAcademicUpdatesClosed($academicUpdatesClosed)
    {
        $this->academicUpdatesClosed = $academicUpdatesClosed;
    }
    
    /**
     *
     * @return array
     */
    public function getAcademicUpdatesClosed()
    {
        return $this->academicUpdatesClosed;
    }
}