<?php
namespace Synapse\CoreBundle\Exception;

/**
 * Exception for missing entities
 */
class UnauthorizedException extends SynapseException
{

    /**
     *
     * @param string $message            
     * @param int|string $code            
     */
    function __construct(
        $message = "Institution is currently inactive",
        $userMessage = "Your institution is not currently active",
        $code = "inactive_institution",
        $httpCode = 401
        ) {
        parent::__construct($message, $userMessage, $code, $httpCode);
    }
}