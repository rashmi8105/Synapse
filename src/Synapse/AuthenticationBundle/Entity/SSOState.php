<?php

namespace Synapse\AuthenticationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="saml_sso_state")
 */
class SSOState
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}