<?php
namespace Synapse\RiskBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BucketDetail extends Constraint
{

    public $message = 'Bucket Values Overlapping or Invalid Min & Max values';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}