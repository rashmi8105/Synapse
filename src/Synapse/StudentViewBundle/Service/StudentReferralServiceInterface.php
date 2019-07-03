<?php
namespace Synapse\StudentViewBundle\Service;

interface StudentReferralServiceInterface
{
    public function getStudentOpenReferrals($loggedInUser);
}