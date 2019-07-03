<?php
namespace Synapse\MultiCampusBundle\Service\Impl;

use JMS\DiExtraBundle\Annotation as DI;
use Synapse\CoreBundle\Service\Impl\AbstractService;
use Synapse\CoreBundle\Util\Constants\TierConstant;
use Synapse\CoreBundle\Util\Constants\PersonConstant;
use Synapse\MultiCampusBundle\EntityDto\ConflictPersonDetailsDto;

class ConflictsServiceHelper extends AbstractService
{
    /**
     *  Returns the home campus Organization.
     *
     * @param ConflictPersonDetailsDto $source
     * @param ConflictPersonDetailsDto $destination
     * @return string
     */
    protected function homeCampus($source, $destination)
    {
        $homeCampus = null;
        if ($source->getIsHome()) {
            $homeCampus = $source->getOrganizationId();
        }
        if ($destination->getIsHome()) {
            $homeCampus = $destination->getOrganizationId();
        }
        return $homeCampus;
    }

    protected function masterRecord($source, $destination)
    {
        $masterId = '';
        if ($source->getIsMaster()) {
            $masterId = $source->getPersonId();
        }
        if ($destination->getIsMaster()) {
            $masterId = $destination->getPersonId();
        }
        return $masterId;
    }

    protected function saveMergeType($source, $destination)
    {
        $sourceConflicts = $this->orgConflictRepo->find($source->getConflictId());
        if ($sourceConflicts) {
            $sourceMergeType = ($source->getMergeType()) ? $source->getMergeType() : NULL;
            $sourceConflicts->setMergeType($sourceMergeType);
        }
        $destinationConflicts = $this->orgConflictRepo->find($destination->getConflictId());
        if ($destinationConflicts) {
            $destinationMergeType = ($destination->getMergeType()) ? $destination->getMergeType() : NULL;
            $destinationConflicts->setMergeType($destinationMergeType);
        }
    }

    protected function saveHomeMaster($source, $destination)
    {
        $sourceConflicts = $this->orgConflictRepo->find($source->getConflictId());
        $destinationConflicts = $this->orgConflictRepo->find($destination->getConflictId());
        $sourceConflicts->setRecordType(NULL);
        $destinationConflicts->setRecordType(NULL);
        $hybrid = false;
        if ($source->getIsHome() && $source->getIsMaster()) {
            $sourceConflicts->setRecordType(TierConstant::FIELD_OTHER);
            $hybrid = true;
        } elseif ($destination->getIsHome() && $destination->getIsMaster()) {
            $destinationConflicts->setRecordType(TierConstant::FIELD_OTHER);
            $hybrid = true;
        } else {
            $hybrid = false;
        }
        if (! $hybrid) {
            $this->setConflictRecordType($source, $sourceConflicts, $destination, $destinationConflicts);
        }
    }

    private function setConflictRecordType($source, $sourceConflicts, $destination, $destinationConflicts)
    {
        $return = true;
        if ($source->getIsMaster()) {
            $sourceConflicts->setRecordType(TierConstant::FIELD_MASTER);
        } elseif ($destination->getIsMaster()) {
            $destinationConflicts->setRecordType(TierConstant::FIELD_MASTER);
        } else {
            $return = true;
        }
        if ($source->getIsHome()) {
            $sourceConflicts->setRecordType('home');
        } elseif ($destination->getIsHome()) {
            $destinationConflicts->setRecordType('home');
        } else {
            $return = true;
        }
        return $return;
    }

    protected function studentStatusChange($sourcePerson, $sourceCampus, $targetPerson, $destinationCampus)
    {
        $this->orgPersonStudentRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_STUDENT);
        $personSourceStudent = $this->orgPersonStudentRepository->findOneBy(array(
            TierConstant::PERSON_FIELD => $sourcePerson,
            TierConstant::FIELD_ORGANIZATION => $sourceCampus
        ));
        if ($personSourceStudent) {
            $personSourceStudent->setStatus('1');
        }
        
        $personTargetStudent = $this->orgPersonStudentRepository->findOneBy(array(
            TierConstant::PERSON_FIELD => $targetPerson,
            TierConstant::FIELD_ORGANIZATION => $destinationCampus
        ));
        if (! empty($personTargetStudent)) {
            $personTargetStudent->setStatus('1');
        }
    }

    protected function facultyStatusChange($sourcePerson, $sourceCampus, $targetPerson, $destinationCampus)
    {
        $this->orgPersonFacultyRepository = $this->repositoryResolver->getRepository(PersonConstant::PERSON_FACULTY_REPO);
        $personSourceFaculty = $this->orgPersonFacultyRepository->findOneBy(array(
            TierConstant::PERSON_FIELD => $sourcePerson,
            TierConstant::FIELD_ORGANIZATION => $sourceCampus
        ));
        if ($personSourceFaculty) {
            $personSourceFaculty->setStatus('1');
        }
        $personTargetFaculty = $this->orgPersonFacultyRepository->findOneBy(array(
            TierConstant::PERSON_FIELD => $targetPerson,
            TierConstant::FIELD_ORGANIZATION => $destinationCampus
        ));
        if ($personTargetFaculty) {
            $personTargetFaculty->setStatus('1');
        }
    }

    protected function autoResolvedConflicts($conflictId)
    {
        $this->orgConflictRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        $conflictDetails = $this->orgConflictRepo->find($conflictId);
        $campusId = ($conflictDetails->getOwningOrgTierCode() == 0) ? $conflictDetails->getSrcOrgId()->getId() : $conflictDetails->getDstOrgId()->getId();
        if (! empty($conflictDetails->getFacultyId())) {
            $personId = $conflictDetails->getFacultyId()->getId();
            $personFaculty = $this->personFaculty->findOneBy([
                PersonConstant::PERSON => $personId,
                TierConstant::FIELD_ORGANIZATION => $campusId
            ]);
            if ($conflictDetails->getOwningOrgTierCode() == 3) {
                $isDualConflict = $this->orgConflictRepo->isDualConflicts($personId);
            } else {
                $isDualConflict = 1;
            }
            if (! empty($personFaculty) && $isDualConflict <= 1) {
                $personFaculty->setStatus(1);
            }
        }
        if (! empty($conflictDetails->getStudentId())) {
            $personId = $conflictDetails->getStudentId()->getId();
            $personStudent = $this->personStudent->findOneBy([
                PersonConstant::PERSON => $personId,
                TierConstant::FIELD_ORGANIZATION => $campusId
            ]);
            if ($conflictDetails->getOwningOrgTierCode() == 3) {
                $isDualConflict = $this->orgConflictRepo->isDualConflicts($personId);
            } else {
                $isDualConflict = 1;
            }
            if (! empty($personStudent) && $isDualConflict <= 1) {
                $personStudent->setStatus(1);
            }
        }
        $conflictDetails->setStatus('merged');
        $this->orgConflictRepo->flush();
        $this->orgConflictRepo->remove($conflictDetails);
    }

    private function isSourceDualConflicts($conflictRecords, $sourcePerson)
    {
        $this->orgConflictRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        $dualConflictsSource = 0;
        if ($conflictRecords[0]->getMulticampusUser()) {
            $dualConflictsSource = $this->orgConflictRepo->isDualConflicts($sourcePerson);
        }
        return $dualConflictsSource;
    }

    private function isTargetDualConflicts($conflictRecords, $targetPerson)
    {
        $this->orgConflictRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        $dualConflictsTargets = 0;
        if ($conflictRecords[1]->getMulticampusUser()) {
            $dualConflictsTargets = $this->orgConflictRepo->isDualConflicts($targetPerson);
        }
        return $dualConflictsTargets;
    }

    protected function sourceDualConflicts($userType, $conflictRecords, $sourcePerson, $sourceCampus, $targetPerson, $destinationCampus)
    {
        if ($userType == TierConstant::FIELD_STUDENT || $userType == TierConstant::FIELD_HYBRID) {
            $dualConflictsSource = $this->isSourceDualConflicts($conflictRecords, $sourcePerson);
            if ($dualConflictsSource <= 1) {
                $this->studentStatusChange($sourcePerson, $sourceCampus, $targetPerson, $destinationCampus);
            }
        }
    }

    protected function targetDualConflicts($userType, $conflictRecords, $sourcePerson, $sourceCampus, $targetPerson, $destinationCampus)
    {
        if ($userType == TierConstant::FIELD_STAFF || $userType == TierConstant::FIELD_HYBRID) {
            $dualConflictsTargets = $this->isTargetDualConflicts($conflictRecords, $targetPerson);
            if ($dualConflictsTargets <= 1) {
                $this->facultyStatusChange($sourcePerson, $sourceCampus, $targetPerson, $destinationCampus);
            }
        }
    }

    protected function conflictListCSV($studentSummary, $facultySummary, $hybridSummary)
    {
        $summaryDetailsCsv = [];
        $summaryDetailsCsv = $this->getStudentsConflict($studentSummary);
        if (isset($facultySummary[TierConstant::CONFLICTS]) && count($facultySummary[TierConstant::CONFLICTS]) > 0) {
            foreach ($facultySummary[TierConstant::CONFLICTS] as $facultyConflict) {
                foreach ($facultyConflict[TierConstant::CONFLICT_RECORDS] as $facultyArray) {
                    $tempArray = [];
                    $tempArray[TierConstant::CREATED_ON] = $facultyArray->getCreatedOn()->format(TierConstant::YMD);
                    $tempArray[TierConstant::FIELD_FIRSTNAME] = $facultyArray->getFirstname();
                    $tempArray[TierConstant::FIELD_LASTNAME] = $facultyArray->getLastname();
                    $tempArray[TierConstant::FIELD_EMAIL] = $facultyArray->getEmail();
                    $tempArray[TierConstant::CAMPUSID] = $facultyArray->getCampusId();
                    $tempArray[TierConstant::FIELD_EXTERNALID] = $facultyArray->getExternalId();
                    $summaryDetailsCsv[] = $tempArray;
                }
            }
        }
        if (isset($hybridSummary[TierConstant::CONFLICTS]) && count($hybridSummary[TierConstant::CONFLICTS]) > 0) {
            foreach ($hybridSummary[TierConstant::CONFLICTS] as $hybridConflict) {
                foreach ($hybridConflict[TierConstant::CONFLICT_RECORDS] as $hybridArray) {
                    $tempArray = [];
                    $tempArray[TierConstant::CREATED_ON] = $hybridArray->getCreatedOn()->format(TierConstant::YMD);
                    $tempArray[TierConstant::FIELD_FIRSTNAME] = $hybridArray->getFirstname();
                    $tempArray[TierConstant::FIELD_LASTNAME] = $hybridArray->getLastname();
                    $tempArray[TierConstant::FIELD_EMAIL] = $hybridArray->getEmail();
                    $tempArray[TierConstant::CAMPUSID] = $hybridArray->getCampusId();
                    $tempArray[TierConstant::FIELD_EXTERNALID] = $hybridArray->getExternalId();
                    $summaryDetailsCsv[] = $tempArray;
                }
            }
        }
        return $summaryDetailsCsv;
    }

    private function getStudentsConflict($studentSummary)
    {
        $summaryDetailsCsv = '';
        if (isset($studentSummary[TierConstant::CONFLICTS]) && count($studentSummary[TierConstant::CONFLICTS]) > 0) {
            foreach ($studentSummary[TierConstant::CONFLICTS] as $studentConflict) {
                foreach ($studentConflict[TierConstant::CONFLICT_RECORDS] as $studentArray) {
                    $createdDate = ($studentArray->getCreatedOn()) ? $studentArray->getCreatedOn()->format(TierConstant::YMD) : '';
                    $tempArray = [];
                    $tempArray[TierConstant::CREATED_ON] = $createdDate;
                    $tempArray[TierConstant::FIELD_FIRSTNAME] = $studentArray->getFirstname();
                    $tempArray[TierConstant::FIELD_LASTNAME] = $studentArray->getLastname();
                    $tempArray[TierConstant::FIELD_EMAIL] = $studentArray->getEmail();
                    $tempArray[TierConstant::CAMPUSID] = $studentArray->getCampusId();
                    $tempArray[TierConstant::FIELD_EXTERNALID] = $studentArray->getExternalId();
                    $summaryDetailsCsv[] = $tempArray;
                }
            }
        }
        return $summaryDetailsCsv;
    }

    protected function markHome($homeCampus, $sourceCampus, $sourcePerson, $targetPerson)
    {
        $this->personStudentRepository = $this->repositoryResolver->getRepository(TierConstant::ORG_PERSON_STUDENT_REPO);
        if ($homeCampus) {
            $personId = ($sourceCampus == $homeCampus) ? $sourcePerson : $targetPerson;
            $personObj = $this->personStudentRepository->findOneBy(array(
                PersonConstant::PERSON => $personId,
                TierConstant::FIELD_ORGANIZATION => $homeCampus
            ));
            if ($personObj) {
                $personObj->setIsHomeCampus('1');
            }
        }
    }

    private function updateStatus($faculty = null, $student = null, $campusId)
    {
        $this->personFaculty = $this->repositoryResolver->getRepository(TierConstant::ORG_PERSON_FACULTY_REPO);
        $this->personStudent = $this->repositoryResolver->getRepository(TierConstant::ORG_PERSON_STUDENT_REPO);
        if (! empty($faculty)) {
            $personFaculty = $this->personFaculty->findOneBy([
                'person' => $faculty,
                'organization' => $campusId
            ]);
            if (! empty($personFaculty)) {
                $personFaculty->setStatus(1);
            }
        }
        if (! empty($student)) {
            $personStudent = $this->personStudent->findOneBy([
                'person' => $student,
                'organization' => $campusId
            ]);
            if (! empty($personStudent)) {
                $personStudent->setStatus(1);
            }
        }
    }

    protected function autoResolveOtherCampusConflicts($personId, $conflictDto)
    {
        $this->orgConflictRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        $this->personRepository = $this->repositoryResolver->getRepository(TierConstant::PERSON);
        $autoResolveConflictRecords = $this->orgConflictRepo->findAutoResolveConflicts($personId);
        $autoResolveConflicts = $this->getPairConflict($autoResolveConflictRecords, $conflictDto);
        if (! empty($autoResolveConflicts)) {
            foreach ($autoResolveConflicts as $autoResolveConflict) {
                $conflictRec = $this->orgConflictRepo->find($autoResolveConflict);
                $sourceOrg = $conflictRec->getSrcOrgId();
                $destOrg = $conflictRec->getDstOrgId();
                if (! empty($conflictRec->getStudentId())) {
                    $externalId = $conflictRec->getStudentId()->getExternalId();
                }
                if (! empty($conflictRec->getFacultyId())) {
                    $externalId = $conflictRec->getFacultyId()->getExternalId();
                }
                $person = $this->personRepository->findBy([
                    'externalId' => $externalId
                ]);
                if (! empty($person)) {
                    $this->resolvePairConflicts($person, $sourceOrg, $destOrg, $conflictDto);
                }
                $conflictRec->setStatus(TierConstant::MERGED);
                $this->orgConflictRepo->remove($conflictRec);
                $this->orgConflictRepo->flush();
            }
        }
    }

    private function getPairConflict($autoResolveConflictRecords, $conflictDto)
    {
        $autoResolveConflicts = array();
        if (! empty($autoResolveConflictRecords)) {
            foreach ($autoResolveConflictRecords as $autoResolveConflictRecord) {
                if ($conflictDto->getConflictId() != $autoResolveConflictRecord['id']) {
                    $autoResolveConflicts[] = $autoResolveConflictRecord;
                }
            }
        }
        return $autoResolveConflicts;
    }

    private function resolvePairConflicts($person, $sourceOrg, $destOrg, $conflictDto)
    {
        $this->orgConflictRepo = $this->repositoryResolver->getRepository(TierConstant::ORG_CONFLICT_REPO);
        foreach ($person as $autoResolvePerson) {
            $removeConflictRows = $this->orgConflictRepo->findAutoResolveConflictsByPerson($autoResolvePerson->getId(), $sourceOrg, $destOrg);
            if (! empty($removeConflictRows)) {
                foreach ($removeConflictRows as $removeConflictRow) {
                    if ($conflictDto->getAutoResolveId() != $removeConflictRow['id']) {
                        $deleteRow = $this->orgConflictRepo->find($removeConflictRow['id']);
                        $campus = ($deleteRow->getOwningOrgTierCode() == 0) ? $deleteRow->getSrcOrgId()->getId() : $deleteRow->getDstOrgId()->getId();
                        $deleteRow->setStatus(TierConstant::MERGED);
                        $personId = ($deleteRow->getFacultyId()) ? $deleteRow->getFacultyId()->getId() : $deleteRow->getStudentId()->getId();
                        $isDualConflict = $this->orgConflictRepo->isDualConflicts($personId);
                        if ($isDualConflict <= 1) {
                            $this->updateStatus($deleteRow->getFacultyId(), $deleteRow->getStudentId(), $campus);
                            $this->personRepository->flush();
                        }
                        $this->orgConflictRepo->flush();
                        $this->orgConflictRepo->remove($deleteRow);
                    }
                }
            }
        }
    }
}