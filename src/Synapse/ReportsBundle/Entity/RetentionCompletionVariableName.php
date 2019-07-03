<?php
namespace Synapse\ReportsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * RetentionCompletionVariableName
 *
 * @ORM\Table(name="retention_completion_variable_name",indexes={@ORM\Index(name="years_from_retention_track_idx",columns={"years_from_retention_track"})})
 * @ORM\Entity(repositoryClass="Synapse\ReportsBundle\Repository\RetentionCompletionVariableNameRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @JMS\ExclusionPolicy("all")
 */
class RetentionCompletionVariableName extends BaseEntity
{

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="years_from_retention_track", type="integer", nullable=true)
     * @JMS\Expose
     */
    private $yearsFromRetentionTrack;

    /**
     *
     * @var string @ORM\Column(name="type", type="string",length=25, nullable=true,)
     * @JMS\Expose
     */
    private $type;

    /**
     *
     * @var string @ORM\Column(name="name_text", type="string", length=100, nullable=false)
     * @JMS\Expose
     */
    private $nameText;

    /**
     *
     * @var string @ORM\Column(name="variable", type="string", length=100, nullable=false)
     * @JMS\Expose
     */
    private $variable;

    /**
     *
     * @var int @ORM\Column(name="sequence", type="integer", nullable=true)
     * @JMS\Expose
     */
    private $sequence;




    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getYearsFromRetentionTrack()
    {
        return $this->yearsFromRetentionTrack;
    }

    /**
     * @param integer $yearsFromRetentionTrack
     */
    public function setYearsFromRetentionTrack($yearsFromRetentionTrack)
    {

        $this->yearsFromRetentionTrack = $yearsFromRetentionTrack;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }


    /**
     * @return string
     */
    public function getNameText()
    {
        return $this->nameText;
    }

    /**
     * @param string $nameText
     */
    public function setNameText($nameText)
    {

        $this->nameText = $nameText;
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
     * @return int
     */
    public function getSequence()
    {
        return $this->sequence;
    }


    /**
     * @param int $sequence
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }


}