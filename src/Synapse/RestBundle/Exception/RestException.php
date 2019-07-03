<?php

namespace Synapse\RestBundle\Exception;

use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\RestBundle\Entity\Error;

/**
 * Base rest exception
 */
class RestException extends SynapseException{

    const ERRORS = 'errors';
    /**
     * @param Error[] $errors
     * @param string $message
     * @param int|string $code
     */
    function __construct($errors = [], $message, $code = 'generic_exception', $httpCode = 500, $developerMessage = null)
    {
        parent::__construct($message, $developerMessage, $code, $httpCode);
        $this->setErrors($errors);
    }


    public function getErrors()
    {
        $info = $this->getInfo();
        return array_key_exists(self::ERRORS, $info) ? $info[self::ERRORS] : [];
    }

    /**
     * @param Error[] $errors
     */
    public function setErrors($errors)
    {
        $this->info[self::ERRORS] = $errors;
    }

}