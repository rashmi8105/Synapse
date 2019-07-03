<?php

class WebAppTestData {

    public function getTestData($class) {
        if ($class != null) {

            switch (get_class($class)) {
                case "Step\Acceptance\SystemAlertStep": $json = file_get_contents("tests/_data/SystemAlert.json");
                    break;
                case "Step\Acceptance\StaticListStep": $json = file_get_contents("tests/_data/StaticListData.json");
                    break;
                case "Step\Acceptance\AppointmentStep": $json = file_get_contents("tests/_data/AppointmentData.json");
                    break;
                case "TestRailIntegration": $json = file_get_contents("tests/_data/TestRunData.json");
                  break;
                case "Step\Acceptance\ISPStep": $json = file_get_contents("tests/_data/ISPData.json");
                   break;
                case "Step\Acceptance\CampusResoucesStep": $json=file_get_contents("tests/_data/CampusResourcesData.json");
                break;
            case "Step\Acceptance\GroupStep": $json=file_get_contents("tests/_data/GroupData.json");
             break;
                case "Step\Acceptance\GroupUploadStep":$json=file_get_contents("tests/_data/GroupData.json");     
              break;
                case "Step\Acceptance\HelpStep":$json=file_get_contents("tests/_data/HelpData.json");
                    break;
                case "Step\Acceptance\ActivityDownloadStep":
                case "Step\Acceptance\NotificationStep": 
                case "Step\Acceptance\AboutTheStudentStep": $json = file_get_contents("tests/_data/AboutTheStudentData.json");
                    break;
                case "Step\Acceptance\TeamStep":$json = file_get_contents("tests/_data/TeamData.json");
                    break;
                case "Step\Acceptance\StudentUploadStep": $json = file_get_contents("tests/_data/ISPData.json");
                    break;
                 case "Step\Acceptance\CoordinatorUserMgmtStep": $json = file_get_contents("tests/_data/CoordinatorUserMgmtData.json");
                    break;
                 case "Step\Acceptance\ManageStudentStep": $json = file_get_contents("tests/_data/CoordinatorUserMgmtData.json");
                    break;
                case "Step\Acceptance\ManageFacultyStep": $json = file_get_contents("tests/_data/CoordinatorUserMgmtData.json");
                    break;
                case "Step\Acceptance\SearchStep": $json = file_get_contents("tests/_data/SearchData.json");
                    break;
            case "Step\Acceptance\PermissionStep": $json = file_get_contents("tests/_data/PermissionData.json");
                    break;
                
                case "Step\Acceptance\CourseAndAcademicUpdateStep":$json=file_get_contents("tests/_data/CourseAndAcademicUpdateData.json");
                break;
            
                    
            }
            return json_decode($json, TRUE);
        }
        return null;
    }

    public function writeTestData($class, $forKey, $value) {

        $oldData = $this->getTestData($class);
        foreach ($oldData as $key => $entry) {
            if ($key == $forKey) {
                $oldData[$key] = $value;
            }
        }

        switch (get_class($class)) {
            case "Step\Acceptance\SystemAlertStep": file_put_contents("tests/_data/SystemAlert.json", json_encode($oldData, JSON_PRETTY_PRINT));
                break;
            case "Step\Acceptance\StaticListStep": file_put_contents("tests/_data/StaticListData.json", json_encode($oldData, JSON_PRETTY_PRINT));
                break;
            case "Step\Acceptance\AppointmentStep": file_put_contents("tests/_data/AppointmentData.json", json_encode($oldData, JSON_PRETTY_PRINT));
                break;
            case "TestRailIntegration": file_put_contents("tests/_data/TestRunData.json", json_encode($oldData, JSON_PRETTY_PRINT));
                break; 
            case "Step\Acceptance\ISPStep":file_put_contents("tests/_data/ISPData.json",json_encode($oldData, JSON_PRETTY_PRINT));
           break;
            case "Step\Acceptance\CampusResoucesStep": file_put_contents("tests/_data/CampusResourcesData.json",json_encode($oldData, JSON_PRETTY_PRINT));
                break;
            case "Step\Acceptance\GroupStep": file_put_contents("tests/_data/GroupData.json",json_encode($oldData, JSON_PRETTY_PRINT));
                break;
            case "Step\Acceptance\GroupUploadStep": file_put_contents("tests/_data/GroupData.json",json_encode($oldData, JSON_PRETTY_PRINT));
            break;
            case "Step\Acceptance\HelpStep":  file_put_contents("tests/_data/HelpData.json",json_encode($oldData, JSON_PRETTY_PRINT));
                    break;
            case "Step\Acceptance\NotificationStep":
            case "Step\Acceptance\AboutTheStudentStep": file_put_contents("tests/_data/AboutTheStudentData.json", json_encode($oldData, JSON_PRETTY_PRINT));
                break;
            
            case "Step\Acceptance\TeamStep": file_put_contents("tests/_data/TeamData.json",json_encode($oldData, JSON_PRETTY_PRINT));
                    break;
                case "Step\Acceptance\ManageStudentStep": file_put_contents("tests/_data/CoordinatorUserMgmtData.json", json_encode($oldData, JSON_PRETTY_PRINT));
                break;
            case "Step\Acceptance\ManageFacultyStep": file_put_contents("tests/_data/CoordinatorUserMgmtData.json", json_encode($oldData, JSON_PRETTY_PRINT));
                break;
            case "Step\Acceptance\SearchStep": file_put_contents("tests/_data/SearchData.json", json_encode($oldData, JSON_PRETTY_PRINT));
                break;
            case "Step\Acceptance\StudentUploadStep": file_put_contents("tests/_data/ISPData.json", json_encode($oldData, JSON_PRETTY_PRINT));
                break;
             case "Step\Acceptance\CoordinatorUserMgmtStep": file_put_contents("tests/_data/CoordinatorUserMgmtData.json", json_encode($oldData, JSON_PRETTY_PRINT));
                break;
            case "Step\Acceptance\CourseAndAcademicUpdateStep": file_put_contents("tests/_data/CourseAndAcademicUpdateData.json", json_encode($oldData, JSON_PRETTY_PRINT));
            break;
            
            case "Step\Acceptance\PermissionStep": file_put_contents("tests/_data/PermissionData.json", json_encode($oldData, JSON_PRETTY_PRINT));
            break;
                
        }
        return $value;
    }

}
