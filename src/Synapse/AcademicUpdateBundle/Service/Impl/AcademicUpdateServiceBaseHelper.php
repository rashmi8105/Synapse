<?php
namespace Synapse\AcademicUpdateBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\AcademicUpdateBundle\EntityDto\AcademicUpdateDetailsResponseDto;
use Synapse\CoreBundle\Util\Constants\AcademicUpdateConstant;
use Synapse\CoreBundle\Util\Helper;

/**
 * Class AcademicUpdateServiceBaseHelper
 *
 * Inheritance pattern is incorrect.
 * @package Synapse\AcademicUpdateBundle\Service\Impl
 * @deprecated
 */
class AcademicUpdateServiceBaseHelper extends AbstractService
{

    protected function isNull($value, $returnValue)
    {
        return ($value != null && $value) ? $value : $returnValue;
    }
    
}