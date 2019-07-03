<?php
namespace Synapse\StudentViewBundle\Service;

interface StudentCampusConnectionServiceInterface
{
    public function getCampusConnectionsForStudent($studentId);
}