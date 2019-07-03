<?php
namespace Synapse\MapworksToolBundle\EntityDto;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MapworksToolPdfDTO
 *
 * @package Synapse\MapworksToolBundle\EntityDto
 */
class MapworksToolPdfDTO
{

    /**
     * id of the Person using the tool
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $personId;

    /**
     * id of the Tool (whose page will be converted to PDF)
     *
     * @var int
     * @JMS\Type("integer")
     */
    private $toolId;

    /**
     * specified amount of zoom on the tool's html page
     *
     * @var float
     * @JMS\Type("float")
     */
    private $zoom;

    /**
     * @return int
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * @param int $personId
     */
    public function setPersonId($personId)
    {
        $this->personId = $personId;
    }

    /**
     * @return int
     */
    public function getToolId()
    {
        return $this->toolId;
    }

    /**
     * @param int $toolId
     */
    public function setToolId($toolId)
    {
        $this->toolId = $toolId;
    }

    /**
     * @return float
     */
    public function getZoom()
    {
        return $this->zoom;
    }

    /**
     * @param float $zoom
     */
    public function setZoom($zoom)
    {
        $this->zoom = $zoom;
    }

}
