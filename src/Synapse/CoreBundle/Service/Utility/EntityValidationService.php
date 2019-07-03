<?php

namespace Synapse\CoreBundle\Service\Utility;

use Gedmo\ReferenceIntegrity\Mapping\Validator;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\Container;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Exception\DataProcessingExceptionHandler;
use Synapse\CoreBundle\Exception\SynapseException;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\SynapseConstant;

/**
 * @DI\Service("entity_validation_service")
 */
class EntityValidationService extends AbstractService
{
    const SERVICE_KEY = 'entity_validation_service';

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var DataProcessingUtilityService
     */
    private $dataProcessingUtilityService;

    /**
     * EntityValidationService constructor
     *
     * @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver"),
     *            "logger" = @DI\Inject("logger"),
     *            "container" = @DI\Inject("service_container")
     *            })
     *
     * @param $repositoryResolver
     * @param $logger
     * @param $container
     */
    public function __construct($repositoryResolver, $logger, $container)
    {
        parent::__construct($repositoryResolver, $logger);

        //scaffolding
        $this->container = $container;

        $this->validator = $this->container->get(SynapseConstant::VALIDATOR);

        // Services
        $this->dataProcessingUtilityService = $this->container->get(DataProcessingUtilityService::SERVICE_KEY);
    }

    /**
     * Validates a doctrine entity, and either throws the passed error or enqueues
     *
     * @param BaseEntity $doctrineEntity
     * @param SynapseException|DataProcessingExceptionHandler|null $errorObject
     * @param string|null $validationGroup
     * @param bool $throwException
     * @return array|SynapseException|DataProcessingExceptionHandler
     * @throws SynapseException|DataProcessingExceptionHandler
     */
    public function validateDoctrineEntity($doctrineEntity, $errorObject = null, $validationGroup = null, $throwException = true)
    {
        $errorObject = $this->buildErrorObjectBasedOffOfValidatedErrorEntity($doctrineEntity, $errorObject, $validationGroup);
        if ($throwException) {
            $errorObject = $this->throwErrorIfContains($errorObject, $validationGroup);
        }
        return $errorObject;
    }

    /**
     * DO NOT USE THIS FUNCTION OUTSIDE OF ITS CURRENT USAGES UNLESS A SKYFACTOR ARCHITECT APPROVES IT
     *
     * @param DataProcessingExceptionHandler $errorObject
     * @param string|null $errorType
     * @return SynapseException
     * @throws DataProcessingExceptionHandler
     */
    public function throwErrorIfContains($errorObject, $errorType = null)
    {
        if ($errorObject->doesErrorHandlerContainError($errorType)) {
            throw $errorObject;
        }
        return $errorObject;
    }

    /**
     * build error object
     *
     * @param BaseEntity $doctrineEntity
     * @param SynapseException|DataProcessingExceptionHandler|null $errorObject
     * @param string|null $validationGroup
     * @return mixed
     */
    public function buildErrorObjectBasedOffOfValidatedErrorEntity($doctrineEntity, $errorObject, $validationGroup = null)
    {
        $errors = $this->validator->validate($doctrineEntity, null, $validationGroup);
        $errorArray = $this->buildEntityValidationErrorArray($errors);
        if (!empty($errorObject)) {
            $errorObject->enqueueErrorsOntoExceptionObject($errorArray, $validationGroup);
        }
        return $errorObject;
    }

    /**
     * Validate doctrine entity groups
     *
     * @param BaseEntity $doctrineEntity
     * @param SynapseException|DataProcessingExceptionHandler|null $errorObject
     * @param array $validationGroupArray : ['validation group name' => 'throw Exception flag for validation group']
     * @return mixed
     */
    public function validateAllDoctrineEntityValidationGroups($doctrineEntity, $errorObject, $validationGroupArray = [])
    {
        foreach ($validationGroupArray as $validationGroup => $throwException) {
            $errorObject = $this->validateDoctrineEntity($doctrineEntity, $errorObject, $validationGroup, $throwException);
        }
        return $errorObject;
    }


    /**
     * Nullify fields to be cleared
     *
     * @param BaseEntity $doctrineEntity
     * @param array $clearFields
     * @param array $clearAttributesMappedToDBFields
     * @return BaseEntity
     */
    public function nullifyFieldsToBeCleared($doctrineEntity, $clearFields , $clearAttributesMappedToDBFields = [])
    {

        $entityAttributesArray = $this->dataProcessingUtilityService->getAllAttributesOfDoctrineEntity($doctrineEntity);
        foreach($clearFields as $field){
            if (array_key_exists($field, $clearAttributesMappedToDBFields)) {
                $field = $clearAttributesMappedToDBFields[$field];
            }
            $convertedAttribute = $this->replaceUnderScoreToCamelCase($field);
            if (in_array($convertedAttribute, $entityAttributesArray)) {
                $setFunction = 'set' . ucfirst($convertedAttribute);
                $doctrineEntity->$setFunction(null);
            }
        }
        return $doctrineEntity;
    }


    /**
     * Replace underscore to camel case for a string
     *
     * @param string $stringToConvert
     * @return string
     */
    public function replaceUnderScoreToCamelCase($stringToConvert)
    {
        $convertedString = str_replace(' ', '', ucwords(str_replace('_', ' ', $stringToConvert)));
        return lcfirst($convertedString);
    }


    /**
     * Restored error properties for an entity
     *
     * @param BaseEntity $editedDoctrineEntity
     * @param BaseEntity $cloneOfOriginalDoctrineEntity
     * @param DataProcessingExceptionHandler $dataProcessingExceptionHandler
     * @return BaseEntity
     */
    public function restoreErroredProperties($editedDoctrineEntity, $cloneOfOriginalDoctrineEntity, $dataProcessingExceptionHandler)
    {
        $errors = $dataProcessingExceptionHandler->getAllErrors();
        foreach ($errors as $error) {
            foreach ($error as $field => $value) {
                $getFunction = 'get' . $field;
                $setFunction = 'set' . $field;
                $editedDoctrineEntity->$setFunction($cloneOfOriginalDoctrineEntity->$getFunction());
                break;
            }
        }
        return $editedDoctrineEntity;

    }
}