<?php
namespace Synapse\CoreBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\RestBundle\Exception\ValidationException;

class PersonHelperService extends AbstractService
{

    protected function isPersonFound($coordinators)
    {
        if (! isset($coordinators)) {
            throw new ValidationException([
                PersonConstant::ERROR_PERSON_NOT_FOUND
            ], PersonConstant::ERROR_PERSON_NOT_FOUND, PersonConstant::ERROR_PERSON_NOT_FOUND_KEY);
        }
    }

    protected function getLastUpdated($lastUpdated, $coordinator)
    {
        if (is_null($lastUpdated)) {
            $lastUpdated = $coordinator->getModifiedAt();
        } else {
            if ($coordinator->getModifiedAt() > $lastUpdated) {
                $lastUpdated = $coordinator->getModifiedAt();
            }
        }
        return $lastUpdated;
    }

    protected function getUserRole($isCoordinator, $person)
    {
        if ($isCoordinator) {
            $userRole = "Coordinator";
        } else {
            
            $orgPersonFacultyRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_FACULTY_REPO);
            $orgPersonFaculty = $orgPersonFacultyRepository->findOneBy(array(
                PersonConstant::FIELD_PERSON => $person
            ));
            if($orgPersonFaculty)
            {
                $userRole = "Staff";
            }else{
                $userRole = "Student";
            }
        }
        return $userRole;
    }

    protected function isContactExist($contact)
    {
        if (! $contact) {
            throw new ValidationException([
                'Contact Not Found.'
            ], 'Contact Not Found.', 'contact_not_found');
        }
    }

    protected function isMailSend($send, $email)
    {
        if ($send) {
            $message = "MyAccount updated email sent successfully to " . $email;
        } else {
            $message = "MyAccount updated email sending failed to " . $email;
        }
        return $message;
    }

    /**
     * checking overall access of features elements if all feature value are "false" it will return "false" for user.
     *
     * @param unknown $feature            
     * @return boolean
     */
    protected function getUserAccess($feature)
    {
        if (! empty($feature)) {
         foreach ($feature as $key => $featureItem) {
                if($key == 'direct_referral' || $key == 'reason_routed_referral'){
                    foreach($featureItem as $refItem){
                        if ((isset($refItem['view']) && $refItem['view']) || (isset($refItem['create']) && $refItem['create'])) {
                            return true;
                        }
                    }
                }
                else if ((isset($featureItem['view']) && $featureItem['view']) || (isset($featureItem['create']) && $featureItem['create'])) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function isDuplicate($errors)
    {
        if (count($errors) > 0) {
            $errorsString = "";
            foreach ($errors as $error) {
                $errorsString .= $error->getMessage();
            }
            throw new ValidationException([
                $errorsString
            ], $errorsString, 'contact_duplicate_Error');
        }
    }

}
