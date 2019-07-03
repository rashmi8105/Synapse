<?php
namespace Synapse\MultiCampusBundle\Job;

use BCC\ResqueBundle\ContainerAwareJob;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Synapse\MultiCampusBundle\Service\Impl\CampusService;
use Synapse\MultiCampusBundle\EntityDto\CampusDto;
use Synapse\MultiCampusBundle\Entity\OrgConflict;

use Synapse\CoreBundle\Util\Constants\TierConstant;

class MoveCampus extends ContainerAwareJob
{     
    public function run($args)
    {        
        $this->repositoryResolver = $this->getContainer()->get('repository_resolver');
        $destinationId = $args['destinationId'];        
        $sourceOrg = $args['sourceId'];        
        $this->campusRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);        
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        $this->conflictRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_ENTITY);
        $listCampuses = $this->campusRepository->listCampuses($destinationId, '3');        
        $campuses = array_column($listCampuses, TierConstant::ORGID);        
        if (($key = array_search($sourceOrg, $campuses)) !== false) {
            unset($campuses[$key]);
        }
        if (! empty($campuses)) {
            foreach ($campuses as $campus) {
                $conflictPersons = '';
                $conflictPersons = $this->personRepository->getConflictPersons($sourceOrg, $campus);                
                if (! empty($conflictPersons)) {
                    foreach ($conflictPersons as $conflictPerson) {
                        $personId = '';
                        $personId[] = $conflictPerson['source'];
                        $personId[] = $conflictPerson['destination'];
                        $personStudentStaff = $this->personRepository->getConflictPersonsByRole($personId);                
                        if (! empty($personStudentStaff)) {                            
                            $this->saveConflicts($personStudentStaff, $campus, $sourceOrg);
                            $this->conflictRepository->flush();
                        }
                    }
                }
            }
        }
        // Make the solo campus into hierarchy campus
        $campusObj = $this->campusRepository->find($sourceOrg);
        $campusObj->setParentOrganizationId($destinationId);
        $campusObj->setTier('3');
        $this->campusRepository->flush();        
    }
    
    private function saveConflicts($personStudentStaff, $campus, $sourceOrg)
    {
        $this->campusRepository = $this->repositoryResolver->getRepository(TierConstant::TIER_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        $this->conflictRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_ENTITY);
        $this->personStudentRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_PERSON_STUDENT_REPO);
        $this->personFacultyRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_PERSON_FACULTY_REPO);
        $i = 0;
        foreach ($personStudentStaff as $person) {
            $destination = $this->campusRepository->find($campus);
            $facultyId = '';
            $studentId = '';
            if ($person[TierConstant::FACULTY_ID] != '') {
                $facultyObj = $this->personFacultyRepository->find($person['facultyId']);
                $facultyId = $facultyObj->getPerson()->getId();
            }
            if ($person[TierConstant::STUDENT_ID] != '') {
                $studentObj = $this->personStudentRepository->find($person['studentId']);
                $studentId = $studentObj->getPerson()->getId();
            }
            //$isExist = $this->conflictRepository->isConflictExist($destination->getId(), $facultyId, $studentId);            
            //if ($isExist == 0) {                
                $orgConflict = new OrgConflict();
                if ($person[TierConstant::FACULTY_ID] != '') {
                    
                    $facultyObj->setStatus(2);
                    $orgConflict->setFacultyId($facultyObj->getPerson());
                    $tierType = ($person['facultyOrg'] == $sourceOrg) ? '0' : '3';
                }
                if ($person[TierConstant::STUDENT_ID] != '') {
                    
                    $studentObj->setStatus(2);
                    $orgConflict->setStudentId($studentObj->getPerson());
                    $tierType = ($person['studentOrg'] == $sourceOrg) ? '0' : '3';
                }
                $source = $this->campusRepository->find($sourceOrg);
                $orgConflict->setSrcOrgId($source);
                $orgConflict->setDstOrgId($destination);
                $orgConflict->setStatus('conflict');
                $orgConflict->setOwningOrgTierCode($tierType);
                $this->conflictRepository->create($orgConflict);
                $batchSize = 20;
                if (($i % $batchSize) === 0) {
                    $this->conflictRepository->flush();
                }
                $i ++;
            //} else {
            //    break;
            //}
        }
    }
}