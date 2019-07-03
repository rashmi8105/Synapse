<?php
namespace Synapse\CoreBundle\Exception;

/**
 * Exception for missing entities
 */
class AccessDeniedException extends SynapseException
{

    /**
     *
     * @param string $message            
     * @param int|string $code            
     */
    function __construct(
        $message = "Access Denied",
        $userMessage = "You do not have permission to access this resource",
        $code = "access_denied",
        $httpCode = 403
    ) {
        parent::__construct($message, $userMessage, $code, $httpCode);
    }
}