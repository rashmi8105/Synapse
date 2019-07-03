<?php

namespace Synapse\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * FeatureMaster
 *
 * @ORM\Table(name="feature_master")
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\FeatureMasterRepository")
 */
class FeatureMaster extends BaseEntity
{
   
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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

}
