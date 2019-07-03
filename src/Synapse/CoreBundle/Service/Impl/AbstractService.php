<?php
namespace Synapse\CoreBundle\Service\Impl;

use Monolog\Logger;
use Symfony\Component\Validator\ConstraintViolationList;
use Synapse\CoreBundle\Entity\BaseEntity;
use Synapse\CoreBundle\Repository\RepositoryResolver;
use Synapse\RestBundle\Exception\ValidationException;

abstract class AbstractService
{

    /**
     *
     * @var Logger logger
     */
    protected $logger;

    /**
     *
     * @var RepositoryResolver
     */
    protected $repositoryResolver;

    public function __construct($repositoryResolver, $logger)
    {
        $this->logger = $logger;
        $this->repositoryResolver = $repositoryResolver;
    }

    public function setSecurity($obj)
    {
        $this->securityContext = $obj;
    }

    public function validateEmpty($data, $errorMsg, $errorKey)
    {
        if (! $data) {
            $this->logger->error(" Abstract Service  -  ValidateEmpty - " . "Error Message" . $errorMsg . "Error Key " . $errorKey);
            throw new ValidationException([
                $errorMsg
            ], $errorMsg, $errorKey);
        }
    }

    public function getShareOptionPermission($asset, $assetName) {
        $shareOptions = $asset->getShareOptions();
        $shareOptionPermission = '';
        if($shareOptions)
        {
            $shareOptions = $shareOptions[0];
            if($assetName == 'referrals'){
                $publicCreate = ['reason-routed-'.$assetName.'-public-create', $assetName.'-public-create'];
                $privateCreate = ['reason-routed-'.$assetName.'-private-create', $assetName.'-private-create'];
                $teamsCreate = ['reason-routed-'.$assetName.'-teams-create', $assetName.'-teams-create'];

                $shareOptionPermission = ($shareOptions->getPublicShare()) ? $publicCreate : '';
                $shareOptionPermission = ($shareOptions->getPrivateShare()) ? $privateCreate : $shareOptionPermission;
                $shareOptionPermission = ($shareOptions->getTeamsShare()) ? $teamsCreate : $shareOptionPermission;
            }
            else {
                $shareOptionPermission = ($shareOptions->getPublicShare()) ? $assetName.'-public-create' : '';
                $shareOptionPermission = ($shareOptions->getPrivateShare()) ? $assetName.'-private-create' : $shareOptionPermission;
                $shareOptionPermission = ($shareOptions->getTeamsShare()) ? $assetName.'-teams-create' : $shareOptionPermission;
            }
        }
        if($shareOptionPermission == '') {
            if($assetName == 'referrals'){
                $shareOptionPermission = $publicCreate;
            }
            else {
                $shareOptionPermission = $assetName.'-public-create';
            }
        }
        return $shareOptionPermission;
    }

    /**
     * Generate error array for person entity validation error
     *
     * @param ConstraintViolationList $errors
     * @return array
     */
    public function buildEntityValidationErrorArray($errors)
    {
        $errorArray = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorArray[$error->getPropertyPath()] = $error->getMessage();
            }
        }
        return $errorArray;
    }

    /**
     * Throws the specified exception if the object is empty.
     *
     * @param BaseEntity $object
     * @param \Exception $exception
     * @return BaseEntity | null
     * @throws \Exception
     */
    public function isObjectEmpty($object, $exception)
    {
        if ((!isset($object) || empty($object)) && ($exception instanceof \Exception)) {
            throw $exception;
        }

        return $object;
    }
}