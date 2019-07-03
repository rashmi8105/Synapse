<?php
/**
 * Created by PhpStorm.
 * User: imeyers
 * Date: 8/3/15
 * Time: 12:38 PM
 */

require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class studentActionsCest extends SynapseRestfulTestBase
{
    private $student = [
        'email' => 'harry.potter@mailinator.com',
        'password' => 'password1!',
    ];

    private $coordinator = [
        'email' => 'albus.dumbledore@mailinator.com',
        'password' => 'password1!',
    ];

    private $faculty = [
        'email' => 'pomona.sprout@mailinator.com',
        'password' => 'password1!',
    ];

    private $invalidStudent = [
        'email' => 'tom.riddle@mailinator.com',
        'password' => 'password1!',
    ];

    private $invalidCoordinator = [
        'email' => 'bad.guy@mailinator.com',
        'password' => 'password1!',
    ];



    private $deleteMe = ['I want to' => 'View This'];

    public function testViewStudentsSurveyTab(ApiAuthTester $I){
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->student, "surveys?studentId=99422&list-type=list&viewType=student", [], 200, []);
        $this->_getAPITestRunner($I, $this->student, "surveys?studentId=99422&list-type=report&viewType=student", [], 200, []);
    }
    public function testViewStudentsAppointmentsTab(ApiAuthTester $I){
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->student, "students/99422/appointments?type=upcoming", [], 200, []);
    }
    public function testViewStudentsCampusConnectionsTab(ApiAuthTester $I){
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->student, "students/99422/campusconnections", [], 200, []);
    }
    public function testViewStudentsCampusResourcesTab(ApiAuthTester $I){
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->student, "students/99422/campusresources", [], 200, []);
    }
    public function testViewStudentsCoursesTab(ApiAuthTester $I){
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->student, "students/99422/courses?view=student", [], 200, []);
    }
    public function testViewStudentsReferralsTab(ApiAuthTester $I){
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->student, "students/99422/referrals?view=student", [], 200, []);
    }
    public function testViewStudentsCampuses(ApiAuthTester $I){
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->student, "students/99422/campuses", [], 200, []);
    }



    public function testCoordinatorViewStudentsSurveyTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->coordinator, "surveys?studentId=99422&list-type=list&viewType=student", [], 200, []);
        $this->_getAPITestRunner($I, $this->coordinator, "surveys?studentId=99422&list-type=report&viewType=student", [], 200, []);
    }
    public function testCoordinatorViewStudentsAppointmentsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->coordinator, "students/99422/appointments?type=upcoming", [], 200, []);
    }
    public function testCoordinatorViewStudentsCampusConnectionsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->coordinator, "students/99422/campusconnections", [], 200, []);
    }
    public function testCoordinatorViewStudentsCampusResourcesTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->coordinator, "students/99422/campusresources", [], 200, []);
    }
    public function testCoordinatorViewStudentsCoursesTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->coordinator, "students/99422/courses?view=student", [], 200, []);
    }
    public function testCoordinatorViewStudentsReferralsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->coordinator, "students/99422/referrals?view=student", [], 200, []);
    }
    public function testCoordinatorViewStudentsCampuses(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->coordinator, "students/99422/campuses", [], 200, []);
    }



    public function testFacultyViewStudentsSurveyTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->faculty, "surveys?studentId=99422&list-type=list&viewType=student", [], 200, []);
        $this->_getAPITestRunner($I, $this->faculty, "surveys?studentId=99422&list-type=report&viewType=student", [], 200, []);
    }
    public function testFacultyViewStudentsAppointmentsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->faculty, "students/99422/appointments?type=upcoming", [], 200, []);
    }
    public function testFacultyViewStudentsCampusConnectionsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->faculty, "students/99422/campusconnections", [], 200, []);
    }
    public function testFacultyViewStudentsCampusResourcesTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->faculty, "students/99422/campusresources", [], 200, []);
    }
    public function testFacultyViewStudentsCoursesTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->faculty, "students/99422/courses?view=student", [], 200, []);
    }
    public function testFacultyViewStudentsReferralsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->faculty, "students/99422/referrals?view=student", [], 200, []);
    }
    public function testFacultyViewStudentsCampuses(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->faculty, "students/99422/campuses", [], 200, []);
    }



    public function testInvalidStudentViewStudentsSurveyTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidStudent, "surveys?studentId=99422&list-type=list&viewType=student", [], 200, []);
        $this->_getAPITestRunner($I, $this->invalidStudent, "surveys?studentId=99422&list-type=report&viewType=student", [], 200, []);
    }
    public function testInvalidStudentViewStudentsAppointmentsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidStudent, "students/99422/appointments?type=upcoming", [], 200, []);
    }
    public function testInvalidStudentViewStudentsCampusConnectionsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidStudent, "students/99422/campusconnections", [], 200, []);
    }
    public function testInvalidStudentViewStudentsCampusResourcesTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidStudent, "students/99422/campusresources", [], 200, []);
    }
    public function testInvalidStudentViewStudentsCoursesTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidStudent, "students/99422/courses?view=student", [], 200, []);
    }
    public function testInvalidStudentViewStudentsReferralsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidStudent, "students/99422/referrals?view=student", [], 200, []);
    }
    public function testInvalidStudentViewStudentsCampuses(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidStudent, "students/99422/campuses", [], 200, []);
    }



    public function testInvalidCoordinatorViewStudentsSurveyTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidCoordinator, "surveys?studentId=99422&list-type=list&viewType=student", [], 200, []);
        $this->_getAPITestRunner($I, $this->invalidCoordinator, "surveys?studentId=99422&list-type=report&viewType=student", [], 200, []);
    }
    public function testInvalidCoordinatorViewStudentsAppointmentsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->student, "students/99422/appointments?type=upcoming", [], 200, []);
    }
    public function testInvalidCoordinatorViewStudentsCampusConnectionsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidCoordinator, "students/99422/campusconnections", [], 200, []);
    }
    public function testInvalidCoordinatorViewStudentsCampusResourcesTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidCoordinator, "students/99422/campusresources", [], 200, []);
    }
    public function testInvalidCoordinatorViewStudentsCoursesTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidCoordinator, "students/99422/courses?view=student", [], 200, []);
    }
    public function testInvalidCoordinatorViewStudentsReferralsTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidCoordinator, "students/99422/referrals?view=student", [], 200, []);
    }
    public function testInvalidCoordinatorViewStudentsCampuses(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo("As a student, view my own student tab.");
        $this->_getAPITestRunner($I, $this->invalidCoordinator, "students/99422/campuses", [], 200, []);
    }

    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
