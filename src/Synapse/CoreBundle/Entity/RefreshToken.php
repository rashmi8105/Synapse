<?php

namespace Synapse\CoreBundle\Entity;

use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\RefreshTokenRepository")
 */
class RefreshToken extends BaseRefreshToken
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="Synapse\CoreBundle\Entity\Person")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
 
    
    protected $token;
}