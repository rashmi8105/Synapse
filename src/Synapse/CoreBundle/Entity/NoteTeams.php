<?php
namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Synapse\CoreBundle\Entity\BaseEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Note 
 * @ORM\Table(name="note_teams", indexes={@ORM\Index(name="fk_note_teams_note1_idx",columns={"note_id"}),@ORM\Index(name="fk_note_teams_teams1_idx",columns={"teams_id"})})
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\NoteTeamsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * 
 */
class NoteTeams extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * 
     * @JMS\Expose
     */
    private $id;

   /**
     * @var \Synapse\CoreBundle\Entity\Note
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Note")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="note_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $noteId;
    
     /**
     * @var \Synapse\CoreBundle\Entity\Teams
     *
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Teams")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="teams_id", referencedColumnName="id")
     * })
     * @JMS\Expose
     */
    private $teamsId;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\Note $noteId
     */
    public function setNoteId($noteId)
    {
        $this->noteId = $noteId;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\Note
     */
    public function getNoteId()
    {
        return $this->noteId;
    }

    /**
     * @param \Synapse\CoreBundle\Entity\Teams $teamsId
     */
    public function setTeamsId($teamsId)
    {
        $this->teamsId = $teamsId;
    }

    /**
     * @return \Synapse\CoreBundle\Entity\Teams
     */
    public function getTeamsId()
    {
        return $this->teamsId;
    }
    
    
}