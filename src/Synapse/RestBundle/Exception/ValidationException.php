<?php

namespace Synapse\RestBundle\Exception;


/**
 * Exception for validation errors
 */
class ValidationException extends RestException{

    /**
     * @param Error[] $errors
     * @param string $message
     * @param int|string $code
     */
    function __construct(
        $errors = [],
        $message = "Validation errors found",
        $code = "validation_errors",
        $httpCode = 400,
        $developerMessage = "Validation error"
    ) {
        parent::__construct($errors, $message, $code, $httpCode, $developerMessage);
    }


}