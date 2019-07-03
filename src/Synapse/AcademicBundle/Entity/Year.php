<?php
namespace Synapse\AcademicBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Year
 *
 * @ORM\Table(name="year")
 * @ORM\Entity(repositoryClass="Synapse\AcademicBundle\Repository\YearRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"id"},message="Year id will be unique.", ignoreNull=false)
 */
class Year extends BaseEntity
{

    /**
     *
     * @var string @ORM\Column(name="id", type="string", length=10, nullable=false)
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}