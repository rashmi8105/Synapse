<?php
namespace Synapse\RiskBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RiskIndicator extends Constraint
{

    public $message = 'Risk Indicator Not Valid';
}