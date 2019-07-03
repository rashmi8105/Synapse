<?php
namespace Synapse\RestBundle\Exception;

/**
 * Exception for validation errors
 */
class DemoException extends RestException
{

    /**
     *
     * @param Error[] $errors            
     * @param string $message            
     * @param int|string $code            
     */
    function __construct(
        $message = "This is a demo error.", 
        $userMessage = "A demo error has occured",
        $code = "demo_error",
        $httpCode = 444
    ) {
        parent::__construct($message, $userMessage, $code, $httpCode);
    }
}