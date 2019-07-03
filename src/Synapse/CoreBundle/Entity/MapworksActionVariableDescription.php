<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;


/**
 * MapworksActionVariableDescription
 *
 * @ORM\Table(name="mapworks_action_variable_description")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\MapworksActionVariableDescriptionRepository")
 * @JMS\ExclusionPolicy("all")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class MapworksActionVariableDescription extends BaseEntity
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
     * @var string
     * @ORM\Column(name="variable", type="string", length=100, nullable=true)
     * @JMS\Expose
     */
    private $variable;

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=300, nullable=true)
     * @JMS\Expose
     */
    private $description;

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
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @param string $variable
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;
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


}