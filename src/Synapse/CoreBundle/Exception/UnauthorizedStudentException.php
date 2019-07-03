<?php
namespace Synapse\CoreBundle\Exception;

/**
 * Exception for missing entities
 */
class UnauthorizedStudentException extends SynapseException
{

    /**
     *
     * @param string $message            
     * @param int|string $code            
     */
    function __construct(
        $message = "Student is currently inactive",
        $userMessage = "This student is currently marked inactive",
        $code = "inactive_student",
        $httpCode = 401
    ) {
        parent::__construct($message, $userMessage, $code, $httpCode);
    }
}