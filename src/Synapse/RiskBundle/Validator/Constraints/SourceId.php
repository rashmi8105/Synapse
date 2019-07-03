<?php
namespace Synapse\RiskBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
/**
 * @Annotation
 */
class SourceId extends Constraint
{
    public $message = '"%string%" should not be blank';
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}