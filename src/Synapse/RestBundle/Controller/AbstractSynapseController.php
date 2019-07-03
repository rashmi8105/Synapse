<?php
namespace Synapse\RestBundle\Controller;

use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Synapse\RestBundle\Entity\Error;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractSynapseController extends FOSRestController implements ClassResourceInterface
{

    /**
     * Converts validation errors into \Synapse\RestBundle\Entity\Error instances
     * 
     * @param ConstraintViolationListInterface $validationErrors            
     * @return array
     */
    protected function convertValidationErrors(ConstraintViolationListInterface $validationErrors)
    {
        $errors = array();
        foreach ($validationErrors as $validationError) {
            array_push($errors, new Error("validation_error", $validationError, uniqid('event-')));
        }
        return $errors;
    }

    /**
     * Validates several parameters
     * 
     * @param ParamFetcher $paramFetcher            
     * @param string[] $params            
     * @return array
     */
    protected function validateQueryParams($paramFetcher, $params)
    {
        $errors = array();
        foreach ($params as $param) {
            try {
                $paramFetcher->get($param, true);
            } catch (Exception $e) {
                $errors[] = new Error("validation_error", array(
                    "param" => $param,
                    "message" => $e->getMessage()
                ));
            }
        }
        return $errors;
    }
}