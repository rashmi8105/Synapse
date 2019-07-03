<?php
namespace Synapse\StaticListBundle\Service\Impl;

use Synapse\RestBundle\Exception\ValidationException;
use Synapse\StaticListBundle\Util\Constants\StaticListConstant;
use Synapse\RestBundle\Entity\TotalStudentsListDto;
use Synapse\CoreBundle\Util\Constants\PersonConstant;

class StaticListServiceHelper
{

    protected function isObjectExist($object, $message, $key)
    {
        if (! ($object)) {
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    protected function checkEmpty($data)
    {
        if (strlen(trim($data)) == 0) {
            throw new ValidationException([
                StaticListConstant::STATICLIST_EMPTY_VALIDATE
            ], StaticListConstant::STATICLIST_EMPTY_VALIDATE, StaticListConstant::STATICLIST_EMPTY_VALIDATE_KEY);
        }
    }

    protected function checkLimitOnlyMax($data, $maxLimit, $msg, $key)
    {
        if (strlen($data) > $maxLimit) {
            throw new ValidationException([
                $msg
            ], $msg, $key);
        }
    }

    protected function justThrow($msg, $key)
    {
        throw new ValidationException([
            $msg
        ], $msg, $key);
    }

    const BASEQUERY_CONST1 =" from person p
                            left join contacts c on (p.id = c.person_id_student)
                            left join risk_level rl on (p.risk_level = rl.id)
                            left join risk_model_levels rml on (rml.risk_level = rl.id)
                            left join intent_to_leave il on (p.intent_to_leave = il.id)
                            left join contact_types ct on (c.contact_types_id = ct.id)
                            left join referrals r on (p.id = r.person_id_student)
                            left join org_person_student ops on (p.id = ops.person_id)
                            left outer join activity_log lc on (lc.person_id_student = p.id and lc.deleted_at is null)";
    

   const  BASEQUERY_CONST2 = "where p.id in";
                                        
                                     

}