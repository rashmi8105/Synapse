<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * MapworksActionVariable
 *
 * @ORM\Table(name="mapworks_action_variable")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\MapworksActionVariableRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class MapworksActionVariable extends BaseEntity
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var MapworksAction
     *
     * @ORM\ManyToOne(targetEntity="MapworksAction")
     * @ORM\JoinColumn(name="mapworks_action_id", referencedColumnName="id")
     * @JMS\Expose
     */
    private $mapworksAction;

    /**
     * @var MapworksActionVariableDescription
     *
     * @ORM\ManyToOne(targetEntity="MapworksActionVariableDescription")
     * @ORM\JoinColumn(name="mapworks_action_variable_description_id", referencedColumnName="id")
     * @JMS\Expose
     */
    private $mapworksActionVariableDescription;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return MapworksAction
     */
    public function getMapworksAction()
    {
        return $this->mapworksAction;
    }

    /**
     * @param MapworksAction $mapworksAction
     */
    public function setMapworksAction($mapworksAction)
    {
        $this->mapworksAction = $mapworksAction;
    }

    /**
     * @return MapworksActionVariableDescription
     */
    public function getMapworksActionVariableDescription()
    {
        return $this->mapworksActionVariableDescription;
    }

    /**
     * @param MapworksActionVariableDescription $mapworksActionVariableDescription
     */
    public function setMapworksActionVariableDescription($mapworksActionVariableDescription)
    {
        $this->mapworksActionVariableDescription = $mapworksActionVariableDescription;
    }
}