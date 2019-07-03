<?php
namespace Synapse\CoreBundle\Exception;

/**
 * Exception for missing entities
 */
class EntityNotFoundException extends SynapseException
{

    /**
     *
     * @param string $message            
     * @param int|string $code            
     */
    function __construct(
        $message = "The requested entity was not found",
        $userMessage = "The resource you are looking for cannot be found",
        $code = "entity_not_found",
        $httpCode = 404
    ) {
        parent::__construct($message, $userMessage, $code, $httpCode);
    }
}