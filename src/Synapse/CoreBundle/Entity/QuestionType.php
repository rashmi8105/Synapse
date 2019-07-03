<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Synapse\CoreBundle\Entity\BaseEntity;

/**
 * QuestionType
 *
 * @ORM\Table(name="question_type")
 * @ORM\Entity
 */
class QuestionType extends BaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="string", length=4, precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }
}
