<?php

namespace Synapse\CoreBundle\Exception;


use Synapse\CoreBundle\SynapseConstant;

class DataProcessingExceptionHandler extends SynapseException
{


    /**
     * DataProcessingExceptionHandler constructor.
     *
     * @param string $message
     * @param string $userMessage
     */
    public function __construct($message = "", $userMessage = SynapseConstant::DEFAULT_SYSTEM_ERROR_MESSAGE)
    {
        parent::__construct($message, $userMessage);
    }

    /**
     * an array of errors
     * @var array
     */
    private $errorArray = [];

    /**
     * queues an error onto the error array
     *
     * @param string | array $error
     * @param string $key
     * @param string $errorType
     */
    public function addErrors($error, $key = 'error', $errorType = null)
    {
        $this->errorArray[] = [
            $key => $error,
            'type' => $errorType
        ];
    }

    /**
     * return an array list
     *
     * @return array
     */
    public function getAllErrors()
    {
        return $this->errorArray;
    }

    /**
     * sets error array to an empty array
     */
    public function resetAllErrors()
    {
        $this->errorArray = [];
    }

    /**
     * returns true when an error type matches a given type
     *
     * @param string|null $errorType
     * @return bool
     */
    public function doesErrorHandlerContainError($errorType = null)
    {
        foreach ($this->errorArray as $error) {
            if ($error['type'] == $errorType) {
                return true;
            }
        }
        return false;
    }

    /**
     * returns all errors without the type
     *
     * @return array
     */
    public function getPlainErrors() {

        $returnArray = [];
        foreach ($this->errorArray as $error) {
            foreach($error as $errorKey => $errorValue) {
                if ($errorKey !== 'type') {
                    $returnArray[] = [$errorKey => $errorValue];
                }
            }
        }
        return $returnArray;
    }


    /**
     * enqueues errors to the exception object
     *
     * @param array $errorArray
     * @param string $validationGroup
     */
    public function enqueueErrorsOntoExceptionObject($errorArray, $validationGroup)
    {
        if (count($errorArray)) {
            foreach ($errorArray as $errorKey => $errorMessage) {
                $this->addErrors($errorMessage, $errorKey, $validationGroup);
            }
        }
    }

}