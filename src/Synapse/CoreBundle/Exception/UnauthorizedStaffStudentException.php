<?php
namespace Synapse\CoreBundle\Exception;

/**
 * Exception for missing entities
 */
class UnauthorizedStaffStudentException extends SynapseException
{

    /**
     *
     * @param string $message            
     * @param int|string $code            
     */
    function __construct(
        $message = "User is currently inactive",
        $userMessage = "This user is currently marked inactive",
        $code = "inactive_user", $httpCode = 401
    ) {
        parent::__construct($message, $userMessage, $code, $httpCode);
    }
}