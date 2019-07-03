<?php

namespace Synapse\CoreBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Synapse\CoreBundle\Repository\AccessTokenRepository")
 */
class AccessToken extends BaseAccessToken
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
    
    
    public  function getClient(){

        return $this->client;
    }
}