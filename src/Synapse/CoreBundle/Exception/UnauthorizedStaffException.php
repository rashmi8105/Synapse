<?php
namespace Synapse\CoreBundle\Exception;

/**
 * Exception for missing entities
 */
class UnauthorizedStaffException extends SynapseException
{

    /**
     *
     * @param string $message            
     * @param int|string $code            
     */
    function __construct(
        $message = "Staff is currently inactive",
        $userMessage = "This staff member is marked as inactive",
        $code = "inactive_staff", $httpCode = 401
    ) {
        parent::__construct($message, $userMessage, $code, $httpCode);
    }
}