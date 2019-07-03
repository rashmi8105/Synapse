<?php
namespace Synapse\RestBundle\Converter;

use Synapse\CoreBundle\Entity\Contactinfo;
use Synapse\CoreBundle\Entity\OrganizationRole;
use Synapse\CoreBundle\Entity\Person;
use Synapse\RestBundle\Entity\CoordinatorDTO;
use Synapse\RestBundle\Entity\InstitutionDTO;
use Synapse\CoreBundle\Entity\OrganizationLang;
use Synapse\CoreBundle\Entity\Rolelang;
use Synapse\RestBundle\Entity\OrganizationlangDTO;
use Synapse\RestBundle\Entity\OrganizationDTO;
use JMS\DiExtraBundle\Annotation as DI;
use Synapse\RestBundle\Entity\RolelangDTO;

/**
 * DTO converter
 *
 * Helper class to convert entities to data transfer objects
 * This class only contain converter that could be shared across different REST end points
 * Some end points could have different implementation accordingly with its needs
 *
 * @DI\Service("dto_converter")
 */
class DTOConverter
{

    /**
     * Transform given Organization to a OrganizationDTO
     *
     * @param OrganizationDTO $organization            
     * @return $organizationDTO
     */
    public function organizationToDTO(OrganizationDTO $organization)
    {
        $organizationDTO = new OrganizationDTO();
        $organizationDTO->setId($organization->getId());
        $organizationDTO->setNickname($organization->getNickname());
        $organizationDTO->setSubdomain($organization->getSubdomain());
        $organizationDTO->setTimezone($organization->getTimezone());
        
        return $organizationDTO;
    }

    /**
     * Converts several institutions to dto
     *
     * @param Institution[] $institutions            
     * @return InstitutionDTO[]
     */
    public function institutionsToDTOs($institutions)
    {
        $dtos = array();
        foreach ($institutions as $institution) {
            $dtos[] = $this->institutionToDTO($institution);
        }
        return $dtos;
    }

    /**
     * Transform given Institution to a InstitutionDTO
     *
     * @param Institution $institution
     *            institution to convert
     * @return InstitutionDTO institution dto
     */
    public function organizationlangToDTO(OrganizationLang $organizationlang)
    {
        $organizationlangDto = new OrganizationlangDTO();
        
        // Organizationlang Entity
        $organizationlangDto->setOrganizationid($organizationlang->getOrganizationid());
        $organizationlangDto->setOrganizationname($organizationlang->getOrganizationname());
        $organizationlangDto->setNickname($organizationlang->getNickname());
        $organizationlangDto->setLangid($organizationlang->getLangid());
        
        // Organization Entity
        $organizationlangDto->setSubdomain($organizationlang->getInstitutions()
            ->getSubdomain());
        $organizationlangDto->setWebsite($organizationlang->getInstitutions()
            ->getWebsite());
        $organizationlangDto->setTimezone($organizationlang->getInstitutions()
            ->getTimezone());
        
        return $organizationlangDto;
    }

    /**
     * Converts several institutions to dto
     *
     * @param Institution[] $institutions            
     * @return InstitutionDTO[]
     */
    public function organizationlangToDTOs($orgnaizationlangs)
    {
        $dtos = array();
        foreach ($orgnaizationlangs as $institution) {
            $dtos[] = $this->organizationlangToDTO($institution);
        }
        return $dtos;
    }

    /**
     * Converts to Organization DTO
     *
     * @param
     *            OrganizationDTO
     * @return Organization
     */
    public function getInstitution(OrganizationDTO $organizationDTO)
    {
        $institution = new Institution();
        
        $institution->setId($organizationDTO->getOrganizationid());
        $institution->setSubdomain($organizationDTO->getSubdomain());
        $institution->setTimezone($organizationDTO->getTimezone());
        
        return $institution;
    }

    /**
     * Converts to OrganizationLang DTO
     *
     * @param
     *            OrganizationDTO
     * @return OrganizationLang
     */
    public function getOrganizationlang(OrganizationDTO $organizationDTO)
    {
        $orglang = new OrganizationLang();
        $orglang->setOrganizationid($organizationDTO->getOrganizationid());
        $orglang->setOrganizationname($organizationDTO->getName());
        $orglang->setNickname($organizationDTO->getNickname());
        
        return $orglang;
    }

    public function getPerson(CoordinatorDTO $coordinatorDTO)
    {
        $person = new Person();
        $person->setFirstname($coordinatorDTO->getFirstname());
        $person->setTitle($coordinatorDTO->getTitle());
        return $person;
    }

    public function getContact(CoordinatorDTO $coordinatorDTO)
    {
        $contact = new Contactinfo();
        if ($coordinatorDTO->getIsmobile()) {
            $contact->setPrimarymobile($coordinatorDTO->getPhone());
        } else {
            $contact->setHomephone($coordinatorDTO->getPhone());
        }
        $contact->setPrimaryemail($coordinatorDTO->getEmail());
        return $contact;
    }


    public function getOrganizationRole(CoordinatorDTO $coordinatorDTO)
    {
        $organizationRole = new Organizationrole();
        $organizationRole->setOrganizationid($coordinatorDTO->getOrganizationid());
        return $organizationRole;
    }

    /**
     * Transform given Rolelang to a RoleDTO
     *
     * @param Rolelang $rolelang            
     * @return RolelangDTo institution dto
     */
    public function roleToDTO(Rolelang $rolelang)
    {
        $role = new RolelangDTO();
        $role->setRoleid($rolelang->getRoleid()
            ->getId());
        $role->setRolelangid($rolelang->getLangid()
            ->getId());
        $role->setRolename($rolelang->getRolename());
        return $role;
    }

    /**
     * Converts several institutions to dto
     * 
     * @param Institution[] $institutions            
     * @return InstitutionDTO[]
     */
    public function roleToDTOs($roles)
    {
        $dtos = array();
        foreach ($roles as $role) {
            $dtos[] = $this->roleToDTO($role);
        }
        return $dtos;
    }
}