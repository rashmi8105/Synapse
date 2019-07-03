<?php
namespace Synapse\CoreBundle\Service;

use Synapse\RestBundle\Entity\ProxyDTO;

interface ProxyServiceInterface
{

    /**
     *
     * @param ProxyDto $proxyDto                    
     */
    public function createProxy(ProxyDto $proxyDto);

    /**
     *
     * @param int $userId            
     * @param int $proxiedUserId                       
     */
    public function deleteProxy($userId, $proxiedUserId);
}