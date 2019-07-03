<?php
namespace Synapse\StudentViewBundle\Service\Impl;

use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Service\Impl\EmailService;
use Synapse\RestBundle\Exception\ValidationException;
use Synapse\CoreBundle\Util\Helper;
use Synapse\CoreBundle\Util\Constants\AppointmentsConstant;

class StudentAppointmentServiceHelper extends AbstractService
{

    const FEATURE_NAME = 'featureName';
    
    const FIELD_CONNECTED = "connected";
    
    static $reponseFeatureKey = array(
        'referrals',
        'notes',
        'log_contacts',
        'booking',
        'student_referral_notification',
        'reason_routing'
    );
    
    protected function isObjectExist($object, $message, $key, $errorConst = '', $logger)
    {
        if (! isset($object) || empty($object)) {
            $logger->error("Student View Appointments - Is object exist - " . $errorConst . $message . " " . $key);
            throw new ValidationException([
                $message
            ], $message, $key);
        }
    }

    protected function getDateByTimezone($timez, $rFormat)
    {
        try {
            $currentNow = new \DateTime('now', new \DateTimeZone($timez));
            $currentNow->setTimezone(new \DateTimeZone('UTC'));
        } catch (Exception $e) {
            $currentNow = new \DateTime('now');
        }
        $currentDate = $currentNow->format($rFormat);
        return $currentDate;
    }

    protected function isNull($value, $returnValue)
    {
        return ($value != null && $value) ? $value : $returnValue;
    }

    protected function isMultiCampus($value, $trueValue, $falseValue)
    {
        if (isset($value) && $value > 1) {
            return $trueValue;
        } else {
            return $falseValue;
        }
    }

    protected function getDateRange($frequency, $currentDate)
    {
        if ($frequency == 'week') {
            $dateRange[AppointmentsConstant::FROM_DATE] = $currentDate;
            $dateRange[AppointmentsConstant::TO_DATE] = $this->getNext($currentDate, $key = 'sunday');
        } elseif ($frequency == 'month') {
            $dateRange[AppointmentsConstant::FROM_DATE] = $currentDate;
            $dateRange[AppointmentsConstant::TO_DATE] = $this->getNext($currentDate, $key = 'last day of this month');
        } else {
            $dateRange[AppointmentsConstant::FROM_DATE] = $currentDate;
            $dateRange[AppointmentsConstant::TO_DATE] = $this->getNext($currentDate, $key = 'today');
        }
        return $dateRange;
    }

    protected function getNext($currentDate, $key = 'sunday')
    {
        $parts = explode("-", $currentDate);
        $curDay = explode(" ", $parts[2]);
        $nextDate = date("Y-m-d", strtotime($key, mktime(0, 0, 0, $parts[1], $curDay[0], $parts[0])));
        return $nextDate;
    }

    protected function getCurrentAcademicTerm($currentDate, $academicTerm, $timez)
    {
        if (isset($academicTerm) && count($academicTerm) > 0) {
            $endslot = $academicTerm[0]['endDate'];
            Helper::setOrganizationDate($endslot, $timez);
            $dateRange[AppointmentsConstant::FROM_DATE] = $currentDate;
            $dateRange[AppointmentsConstant::TO_DATE] = $endslot->format(AppointmentsConstant::DATEONLY_FORMAT);
        } else {
            $dateRange[AppointmentsConstant::FROM_DATE] = "";
            $dateRange[AppointmentsConstant::TO_DATE] = "";
        }
        return $dateRange;
    }

    protected function dateValidation($date1, $date2, $vaildationType = null)
    {
        if ($vaildationType == "pastAppointment") {
            if ($date1 > $date2) {
                throw new ValidationException([
                    'Past appointment cannot be cancelled'
                ], 'Past appointment cannot be cancelled', 'appointment_cancel_error');
            }
        } else {
            if ($date2 < $date1) {
                throw new ValidationException([
                    'End date/time should be greater then start date/time'
                ], 'End date/time should be greater then start date/time', 'Invalid_Date');
            }
        }
    }

    /**
     * Send Email Notification to User
     *
     * @param object $emailTemplate
     * @param array $tokenValues
     * @param array $emailDetails
     * @param EmailService $emailService
     * @param bool $sendEmailToNonParticipant - false will not send email to the non-participant students
     * 
     * @deprecated - Please use the appropriate code related to the Mapworks Action framework for notification and email control.
     * 
     */
    protected function sendEmailNotification($emailTemplate, $tokenValues, $emailDetails, $emailService, $sendEmailToNonParticipant = false)
    {
        $emailResponse = [];
        if ($emailTemplate) {
            $emailBody = $emailTemplate->getBody();
            $emailBody = $emailService->generateEmailMessage($emailBody, $tokenValues);
            $bcc = $emailTemplate->getEmailTemplate()->getBccRecipientList();
            $subject = $emailTemplate->getSubject();
            $from = $emailTemplate->getEmailTemplate()->getFromEmailAddress();
            $emailResponse['email_detail'] = array(
                'from' => $from,
                'subject' => $subject,
                'bcc' => $bcc,
                'body' => $emailBody,
                'to' => $emailDetails['staff_email'],
                'emailKey' => $emailDetails['email_key'],
                'organizationId' => $emailDetails['orgId']
            );
        }
        $emailInstance = $emailService->sendEmailNotification($emailResponse['email_detail']);
        $emailService->sendEmail($emailInstance, false, null, $sendEmailToNonParticipant);
    }

    protected function mapOrganizationFeatureArray($features)
    {
        $orgFeature = array();
        if (count($features) > 0) {
            foreach ($features as $feature) {
                $orgFeature[$feature['organization_id']][] = $feature;
            }
        }
        return $orgFeature;
    }
    
    protected function bindOrgFeatures($results)
    {
        $response = [];
        $index = 0;
        if (count($results) > 0) {
            foreach ($results as $result) {
                if (isset($result[self::FEATURE_NAME]) && ! empty($result[self::FEATURE_NAME])) {
                    $response[strtolower(str_replace(" ", "_", $result[self::FEATURE_NAME]))] = (bool) $result[self::FIELD_CONNECTED];
                } else {
                    $response[self::$reponseFeatureKey[$index]] = (bool) $result[self::FIELD_CONNECTED];
                }
                $index ++;
            }
        }
        foreach (self::$reponseFeatureKey as $ky) {
            if (! array_key_exists($ky, $response)) {
                $response[$ky] = false;
            }
        }
        return $response;
    }
}
