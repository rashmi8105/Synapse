<?php
namespace Synapse\RestBundle\Converter;

use Guzzle\Plugin\Md5\Md5ValidatorPlugin;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Entity\PersonDTO;
use Synapse\CoreBundle\Entity\Person;
use Synapse\CoreBundle\Entity\ContactInfo;
use Synapse\CoreBundle\Entity\Organization;

/**
 * Class PersonDtoConverter
 * @DI\Service("persondto_converter")
 */
class PersonDtoConverter
{

    /**
     * Organizations repository
     * 
     * @var Synapse/CoreBundle/Repository/OrganizationRepository
     */
    private $organizationRepository;

    /**
     *
     * @param $repositoryResolver @DI\InjectParams({
     *            "repositoryResolver" = @DI\Inject("repository_resolver")
     *            })
     */
    public function __construct($repositoryResolver)
    {
        $this->organizationRepository = $repositoryResolver->getRepository("SynapseCoreBundle:Organization");
    }

    /**
     * Get Person Entity from PersonDTO
     * 
     * @param
     *            PersonDTO
     * @return Person
     */
    public function getPerson(PersonDTO $personDTO)
    {
        $person = new Person();
        
        $personID = $personDTO->getPersonId();
        $password = $personDTO->getPassword();
        // var_dump($personID); exit;
        if (isset($personID)) {
            $person->setId($personDTO->getPersonId());
            $person->setActivationToken($personDTO->getActivationToken());
        } else {
            $activationToken = mt_rand();
            $person->setActivationToken($activationToken);
        }
        
        $person->setFirstName($personDTO->getFirstName());
        $person->setLastName($personDTO->getLastName());
        $person->setTitle($personDTO->getTitle());
        $person->setDateOfBirth($personDTO->getDateOfBirth());
        $person->setExternalId($personDTO->getExternalId());
        $person->setUsername($personDTO->getUsername());
        $person->setOrganization($this->organizationRepository->findOneById($personDTO->getOrganization()));
        // password set is not required
        /*
         * if (isset($password)) { $encryptPassword = sha1($password); $person->setPassword($encryptPassword); }
         */
        
        $person->setConfidentialityStmtAcceptDate($personDTO->getConfidentialityStmtAcceptDate());
        
        return $person;
    }

    /**
     * Get ContactInfo entity PersonDTO
     * 
     * @param
     *            PersonDTO
     * @return ContactInfo
     */
    public function getContactInfo(PersonDTO $personDTO)
    {
        $contactInfo = new ContactInfo();
        
        $contactInfo->setAddress1($personDTO->getAddress1());
        $contactInfo->setAddress2($personDTO->getAddress2());
        $contactInfo->setAlternateEmail($personDTO->getAlternateEmail());
        $contactInfo->setAlternateMobile($personDTO->getAlternateMobile());
        $contactInfo->setAlternateMobileProvider($personDTO->getAlternateMobileProvider());
        $contactInfo->setCity($personDTO->getCity());
        $contactInfo->setCountry($personDTO->getCountry());
        $contactInfo->setHomePhone($personDTO->getHomePhone());
        $contactInfo->setOfficePhone($personDTO->getOfficePhone());
        $contactInfo->setPrimaryEmail($personDTO->getPrimaryEmail());
        $contactInfo->setPrimaryMobile($personDTO->getPrimaryMobile());
        $contactInfo->setPrimaryMobileProvider($personDTO->getPrimaryMobileProvider());
        $contactInfo->setState($personDTO->getState());
        $contactInfo->setZip($personDTO->getZip());
        
        return $contactInfo;
    }

}
