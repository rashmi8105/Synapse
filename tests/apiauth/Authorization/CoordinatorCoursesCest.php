<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';

class CoordinatorCoursesCest extends SynapseRestfulTestBase
{
    // These tests will see what happens when a coordinator
    // edits, modifies and deletes courses and course information
    private $coordinator = [
        'email' => 'albus.dumbledore@mailinator.com',
        'password' => 'password1!',
    ];
    private $invalidCoordinator = [
        'email' => 'bad.guy@mailinator.com',
        'password' => 'password1!',
    ];
    private $faculty = [
        'email' => 'minerva.mcgonagall@mailinator.com',
        'password' => 'password1!',
    ];
    private $student = [
        'email' => 'harry.potter@mailinator.com',
        'password' => 'password1!',
    ];

    private $coursesList = [
        'data' =>
            array (
                'total_course' => 4,
                'total_faculty' => 4,
                'total_students' => 25,
                'course_list_table' =>
                    array (
                        0 =>
                            array (
                                'year' => 'Jahr',
                                'term' => 'Herbst',
                                'college' => 'CODE1',
                                'department' => 'CRED',
                                'course' =>
                                    array (
                                        0 =>
                                            array (
                                                'course_id' => 435,
                                                'unique_course_section_id' => 0,
                                                'subject_course' => 'CRE1',
                                                'section_id' => '101',
                                                'total_faculty' => 2,
                                                'total_students' => 9,
                                            ),
                                    ),
                            ),
                        1 =>
                            array (
                                'year' => 'Jahr',
                                'term' => 'Herbst',
                                'college' => 'CODE2',
                                'department' => 'HISTD',
                                'course' =>
                                    array (
                                        0 =>
                                            array (
                                                'course_id' => 436,
                                                'unique_course_section_id' => 0,
                                                'subject_course' => 'HIST1',
                                                'section_id' => '101',
                                                'total_faculty' => 2,
                                                'total_students' => 8,
                                            ),
                                    ),
                            ),
                        2 =>
                            array (
                                'year' => 'Jahr',
                                'term' => 'Herbst',
                                'college' => 'CODE4',
                                'department' => 'MAGD',
                                'course' =>
                                    array (
                                        0 =>
                                            array (
                                                'course_id' => 438,
                                                'unique_course_section_id' => 0,
                                                'subject_course' => 'MAG1',
                                                'section_id' => '101',
                                                'total_faculty' => 2,
                                                'total_students' => 12,
                                            ),
                                    ),
                            ),
                    ),
            ),
    ];

    private $WithinCourseList = [
        'data' =>
            array (
                'total_students' => 9,
                'total_faculties' => 2,
                'course_id' => 435,
                'course_name' => 'Care of Magical Creatures',
                'subject_code' => 'CRE1',
                'section_number' => '101',
                'faculty_details' =>
                    array (
                        0 =>
                            array (
                                'faculty_id' => 99437,
                                'first_name' => 'Cuthbert',
                                'last_name' => 'Binns',
                                'permissionset_id' => 36300,
                                'email' => 'cuthbert.binns@mailinator.com',
                                'id' => 'F003',
                            ),
                        1 =>
                            array (
                                'faculty_id' => 99446,
                                'first_name' => 'Rubeus',
                                'last_name' => 'Hagrid',
                                'permissionset_id' => 36299,
                                'email' => 'rubeus.hagrid@mailinator.com',
                                'id' => 'F012',
                            ),
                    ),
                'student_details' =>
                    array (
                        0 =>
                            array (
                                'student_id' => 99414,
                                'first_name' => 'Cho ',
                                'last_name' => 'Chang',
                                'email' => 'cho.chang@mailinator.com',
                                'id' => 'S005',
                                'student_status' => '1',
                                'academic_updates' => true,
                            ),
                        1 =>
                            array (
                                'student_id' => 99415,
                                'first_name' => 'Colin ',
                                'last_name' => 'Creevey',
                                'email' => 'colin.creevey@mailinator.com',
                                'id' => 'S006',
                                'student_status' => '1',
                                'academic_updates' => true,
                            ),
                        2 =>
                            array (
                                'student_id' => 99413,
                                'first_name' => 'Cedric ',
                                'last_name' => 'Diggory',
                                'email' => 'cedric.diggory@mailinator.com',
                                'id' => 'S004',
                                'student_status' => '1',
                                'academic_updates' => true,
                            ),
                        3 =>
                            array (
                                'student_id' => 99423,
                                'first_name' => 'Hermione ',
                                'last_name' => 'Granger',
                                'email' => 'hermione.granger@mailinator.com',
                                'id' => 'S014',
                                'student_status' => '1',
                                'academic_updates' => true,
                            ),
                        4 =>
                            array (
                                'student_id' => 99411,
                                'first_name' => 'Angelina ',
                                'last_name' => 'Johnson',
                                'email' => 'angelina.johnson@mailinator.com',
                                'id' => 'S002',
                                'student_status' => '1',
                                'academic_updates' => true,
                            ),
                        5 =>
                            array (
                                'student_id' => 99422,
                                'first_name' => 'Harry ',
                                'last_name' => 'Potter',
                                'email' => 'harry.potter@mailinator.com',
                                'id' => 'S013',
                                'student_status' => '1',
                                'academic_updates' => true,
                            ),
                        6 =>
                            array (
                                'student_id' => 99412,
                                'first_name' => 'Arian ',
                                'last_name' => 'Pucey',
                                'email' => 'arian.pucey@mailinator.com',
                                'id' => 'S003',
                                'student_status' => '1',
                                'academic_updates' => true,
                            ),
                        7 =>
                            array (
                                'student_id' => 99410,
                                'first_name' => 'Alicia ',
                                'last_name' => 'Spinnet',
                                'email' => 'alicia.spinnet@mailinator.com',
                                'id' => 'S001',
                                'student_status' => '1',
                                'academic_updates' => true,
                            ),
                        8 =>
                            array (
                                'student_id' => 99432,
                                'first_name' => 'Ron ',
                                'last_name' => 'Weasley',
                                'email' => 'ron.weasley@mailinator.com',
                                'id' => 'S023',
                                'student_status' => '1',
                                'academic_updates' => true,
                            ),
                    ),
            ),

    ];

    private $changePermissionParameters = [
        'student_id' => 99432,
        'course_id' => 435,
        'organization_id' => "542" ,
        'permissionset_id' => 36297 ,
        'person_id' => 99446
    ];

    private $parametersForFacultyInsertion = [
        'course_id' => 435,
        'person_id' => 99440,
        'type' => "faculty"
    ];
    private $parametersForInvalidFacultyInsertion = [
        'course_id' => 435,
        'person_id' => 99444,
        'type' => "faculty"
    ];
    private $viewForFacultyInsertion = [
        'data' =>
            array (
                'type' => 'faculty',
                'course_id' => 435,
                'person_id' => 99440,
            ),
    ];

    private $parametersForStudentInsertion = [
        'course_id' => 435,
        'person_id' => 99416,
        'type' => "student"
    ];
    private $parametersForInvalidStudentInsertion = [
        'course_id' => 435,
        'person_id' => 99434,
        'type' => "student"
    ];

    private $viewForStudentInsertion = [
        'data' =>
            array (
                'type' => 'student',
                'course_id' => 435,
                'person_id' => 99416,
            ),
    ];
    private $parametersForFacultyAsAStudentInsertion = [
        'course_id' => 435,
        'person_id' => 99446,
        'type' => "student"
    ];
    private $parametersForStudentAsAFacultyInsertion = [
        'course_id' => 435,
        'person_id' => 99416,
        'type' => "faculty"
    ];
    private $parametersForBadGuyAsAFacultyInsertion = [
        'course_id' => 435,
        'person_id' => 99705,
        'type' => "faculty"
    ];
    private $parametersForBadGuyAsAStudentInsertion = [
        'course_id' => 435,
        'person_id' => 99705,
        'type' => "student"
    ];
    private $parametersForBadStudentAsAStudentInsertion = [
        'course_id' => 435,
        'person_id' => 99713,
        'type' => "student"
    ];
    private $parametersForBadStudentAsAFacultyInsertion = [
        'course_id' => 435,
        'person_id' => 99713,
        'type' => "faculty"
    ];
    private $StudentChangePermissionParameters = [
        'student_id' => 99432,
        'course_id' => 435,
        'organization_id' => "542" ,
        'permissionset_id' => 36296 ,
        'person_id' => 99446
    ];

    private $StudentParametersForFacultyInsertion = [
        'course_id' => 435,
        'person_id' => 99704,
        'type' => "faculty"
    ];

    private $StudentParametersForStudentInsertion = [
        'course_id' => 435,
        'person_id' => 99419,
        'type' => "student"
    ];
    private $facultyChangePermissionParameters = [
        'student_id' => 99432,
        'course_id' => 435,
        'organization_id' => "542" ,
        'permissionset_id' => 43146 ,
        'person_id' => 99446
    ];

    private $facultyParametersForFacultyInsertion = [
        'course_id' => 435,
        'person_id' => 99711,
        'type' => "faculty"
    ];

    private $facultyParametersForStudentInsertion = [
        'course_id' => 435,
        'person_id' => 99417,
        'type' => "student"
    ];

    private $error403 = [  'errors' =>
        array (
            0 =>
                array (
                    'code' => '403',
                    'user_message' => 'Unauthorized access: coordinator-setup',
                    'developer_message' => '',
                    'info' =>
                        array (
                        ),
                ),
        ),];
    private $unauthorizedAccess = [
        'errors' =>
            array (
                0 =>
                    array (
                        'code' => '403',
                        'user_message' => 'Unauthorized access to organization: 542',
                        'developer_message' => '',
                        'info' =>
                            array (
                            ),
                    ),
            ),
    ];
    private $CourseNotFound = [
        'errors' =>
            array (
                0 => 'Course not found',
            ),
    ];


    public function testViewCoursesTab(ApiAuthTester $He){
        $He->wantTo('As a coordinator, I Want to view my own courses page');
        $this->_getAPITestRunner($He, $this->coordinator, 'courses?user_type=coordinator&year=current&term=current&college=all&department=all&filter=&viewmode=json', [], 200, $this->coursesList);
    }

    public function testViewSingleCourse(ApiAuthTester $I){
        $I->wantTo('As a coordinator, I want to view a single course');
        $this->_getAPITestRunner($I, $this->coordinator,
                                    'courses/435?user_type=coordinator&viewmode=json', [], 200, $this->WithinCourseList);
    }

    // Requires API interaction
    public function testChangePermissionOfACoordinator(ApiAuthTester $I){
        $I->wantTo('As a coordinator, I want to change the permission set of a faculty member in their course');
        $this->_putAPITestRunner($I, $this->coordinator, 'courses/permissions', $this->changePermissionParameters, 204, []);
    }


    public function testDeleteAFacultyFromACourse(ApiAuthTester $I){
        $I->wantTo('As a coordinator, I should be able to delete a Faculty member from a course');
        $this->_deleteAPITestRunner($I, $this->coordinator, 'courses/435/faculty/99437', [], 204, []);
    }


    public function testDeleteAStudentFromACourse(ApiAuthTester $I){
        $I->wantTo('As a coordinator, I should be able to delete a student from a course');
        $this->_deleteAPITestRunner($I, $this->coordinator, 'courses/435/student/99422', [], 204, []);
    }

    public function testAddAStudentIntoACourse(ApiAuthTester $I){
        $I->wantTo('As a Coordinator, I should be able to add a student into a course');
        $this->_postAPITestRunner($I, $this->coordinator, 'courses/roster', $this->parametersForFacultyInsertion, 201, $this->viewForFacultyInsertion);
    }

    public function testAddAFacultyIntoACourse(ApiAuthTester $I){
        $I->wantTo('As a Coordinator, I should be able to add a faculty member into a course');
        $this->_postAPITestRunner($I, $this->coordinator, 'courses/roster', $this->parametersForStudentInsertion, 201, $this->viewForStudentInsertion);
    }


    /************************these are the insertions that should not work even done by coordinator ***************************/
    // add a faculty as a student
    public function testAddAFacultyAsAStudentIntoACourse(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo('As a Coordinator, I should be able to add a faculty member into a course');
        $this->_postAPITestRunner($I, $this->coordinator, 'courses/roster', $this->parametersForFacultyAsAStudentInsertion, 400, []);
    }

    // add a student as a faculty
    public function testAddAStudentAsAFacultyIntoACourse(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo('As a Coordinator, I should not be able to add a student as a faculty member into a course');
        $this->_postAPITestRunner($I, $this->coordinator, 'courses/roster', $this->parametersForStudentAsAFacultyInsertion, 400, []);
    }

    // add Mr. Bad Guy as a student and faculty member && student
    public function testAddBadGuyAsAFacultyIntoACourse(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('As a Coordinator, I should not be able to add someone from outside the university as a faculty into a course');
        $this->_postAPITestRunner($I, $this->coordinator, 'courses/roster', $this->parametersForBadGuyAsAFacultyInsertion, 400, []);
    }

    public function testAddBadGuyAsAStudentIntoACourse(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('As a Coordinator, I should not be able to add someone from outside the university as a student into a course');
        $this->_postAPITestRunner($I, $this->coordinator, 'courses/roster', $this->parametersForBadGuyAsAStudentInsertion, 400, []);
    }

    // Student from a different course
    public function testAddBadStudentAsAFacultyIntoACourse(ApiAuthTester $I, $scenario)
    {
        $I->wantTo('As a Coordinator, I should not be able to add someone from outside the university as a faculty into a course');
        $this->_postAPITestRunner($I, $this->coordinator, 'courses/roster', $this->parametersForBadStudentAsAFacultyInsertion, 400, []);
    }

    public function testAddBadStudentAsAStudentIntoACourse(ApiAuthTester $I, $scenario)
    {
        //$scenario->skip("Failed");
        $I->wantTo('As a Coordinator, I should not be able to add someone from outside the university as a student into a course');
        $this->_postAPITestRunner($I, $this->coordinator, 'courses/roster', $this->parametersForBadStudentAsAStudentInsertion, 400, []);
    }

    /***********************************FACULTY TESTS *********************************/
    public function testFacultyViewCoursesTab(ApiAuthTester $I, $scenario){
        $I->wantTo('As a faculty, I should not be able to view the Course Tab');
        $this->_getAPITestRunner($I, $this->faculty, 'courses?user_type=coordinator&year=current&term=current&college=all&department=all&filter=&viewmode=json', [], 403, $this->error403);
    }

    public function testFacultyViewSingleCourse(ApiAuthTester $I, $scenario){
        $I->wantTo('As a faculty, I should not be able to view a single course');
        $this->_getAPITestRunner($I, $this->faculty,
            'courses/435?user_type=coordinator&viewmode=json', [], 403, $this->error403);
    }
    
    // Requires API interaction
    public function testFacultyChangePermissionOfACoordinator(ApiAuthTester $I, $scenario){
        $I->wantTo('As a faculty, I should not be able to change the permission set of a faculty member in their course');
        $this->_putAPITestRunner($I, $this->faculty, 'courses/permissions', $this->facultyChangePermissionParameters, 403, []);
    }

    public function testFacultyDeleteAFacultyFromACourse(ApiAuthTester $I, $scenario){
        $I->wantTo('As a faculty, I should not be able to delete a Faculty member from a course');
        $this->_deleteAPITestRunner($I, $this->faculty, 'courses/435/faculty/99437', [], 403, []);
    }

    public function testFacultyDeleteAStudentFromACourse(ApiAuthTester $I){
        $I->wantTo('As a faculty, I should not be able to delete a student from a course');
        $this->_deleteAPITestRunner($I, $this->faculty, 'courses/435/student/99422', [], 403, []);
    }

    public function testFacultyAddAStudentIntoACourse(ApiAuthTester $I){
        $I->wantTo('As a faculty, I should not be able to add a student into a course');
        $this->_postAPITestRunner($I, $this->faculty, 'courses/roster', $this->facultyParametersForStudentInsertion, 403, []);
    }

    public function testFacultyAddAFacultyIntoACourse(ApiAuthTester $I){
        $I->wantTo('As a faculty, I should not be able to add a faculty member into a course');
        $this->_postAPITestRunner($I, $this->faculty, 'courses/roster', $this->facultyParametersForFacultyInsertion, 403, []);
    }

    /***********************************BAD GUY TESTS *********************************/

    public function testInvalidCoordinatorViewCoursesTab(ApiAuthTester $I, $scenario){
        //$scenario->skip("Failed");
        $I->wantTo('As a coordinator from another university, I should not be able to view the Course Tab');
        $this->_getAPITestRunner($I, $this->invalidCoordinator, 'courses?user_type=coordinator&year=current&term=current&college=all&department=all&filter=&viewmode=json', [], 201, [$this->error403]);
    }

    public function testInvalidCoordinatorViewSingleCourse(ApiAuthTester $I, $scenario){
        $I->wantTo('As a coordinator from another university, I shoudl not be able to view a single course');
        $this->_getAPITestRunner($I, $this->invalidCoordinator,
            'courses/435?user_type=coordinator&viewmode=json', [], 400, [$this->CourseNotFound]);
    }
    // Requires API interaction
    public function testInvalidCoordinatorChangePermissionOfACoordinator(ApiAuthTester $I, $scenario){
        $I->wantTo('As a coordinator from another university, I should not be able to change the permission set of a faculty member in their course');
        $this->_putAPITestRunner($I, $this->invalidCoordinator, 'courses/permissions', $this->changePermissionParameters, 403, [$this->unauthorizedAccess]);
    }

    public function testInvalidCoordinatorDeleteAFacultyFromACourse(ApiAuthTester $I){
        $I->wantTo('As a coordinator from another university, I should not be able to delete a Faculty member from a course');
        $this->_deleteAPITestRunner($I, $this->invalidCoordinator, 'courses/435/faculty/99437', [], 403, $this->unauthorizedAccess);
    }

    public function testInvalidCoordinatorDeleteAStudentFromACourse(ApiAuthTester $I){
        $I->wantTo('As a coordinator from another university, I should not be able to delete a student from a course');
        $this->_deleteAPITestRunner($I, $this->invalidCoordinator, 'courses/435/student/99422', [], 403, $this->unauthorizedAccess);
    }

    public function testInvalidCoordinatorAddAStudentIntoACourse(ApiAuthTester $I, $scenario){
        $I->wantTo('As a coordinator from another university, I should not be able to add a student into a course');
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'courses/roster', $this->parametersForInvalidStudentInsertion, 403, [$this->unauthorizedAccess]);
    }

    public function testInvalidCoordinatorAddAFacultyIntoACourse(ApiAuthTester $I, $scenario){
        $I->wantTo('As a coordinator from another university, I should not be able to add a faculty member into a course');
        $this->_postAPITestRunner($I, $this->invalidCoordinator, 'courses/roster', $this->parametersForInvalidFacultyInsertion, 403, [$this->unauthorizedAccess]);
    }

    /***********************************STUDENT TESTS *********************************/

    public function testStudentViewCoursesTab(ApiAuthTester $I){
        $I->wantTo('As a student, I should not be able to view the Course Tab');
        $this->_getAPITestRunner($I, $this->student, 'courses?user_type=coordinator&year=current&term=current&college=all&department=all&filter=&viewmode=json', [], 403, []);
    }

    public function testStudentViewSingleCourse(ApiAuthTester $I){
        $I->wantTo('As a student, I should not be able to view a single course');
        $this->_getAPITestRunner($I, $this->student,
            'courses/435?user_type=coordinator&viewmode=json', [], 403, []);
    }

    // Requires API interaction
    public function testStudentChangePermissionOfACoordinator(ApiAuthTester $I, $scenario){
        $I->wantTo('As a student, I should not be able to change the permission set of a faculty member in their course');
        $this->_putAPITestRunner($I, $this->student, 'courses/permissions', $this->StudentChangePermissionParameters, 403, []);
    }

    public function testStudentDeleteAFacultyFromACourse(ApiAuthTester $I, $scenario){
        $I->wantTo('As a student, I should not be able to delete a Faculty member from a course');
        $this->_deleteAPITestRunner($I, $this->student, 'courses/435/faculty/99422', [], 403, []);
    }

    public function testStudentDeleteAStudentFromACourse(ApiAuthTester $I){
        $I->wantTo('As a student, I should not be able to delete a student from a course');
        $this->_deleteAPITestRunner($I, $this->student, 'courses/435/student/99432', [], 403, []);
    }

    public function testStudentAddAStudentIntoACourse(ApiAuthTester $I){
        $I->wantTo('As a student, I should not be able to add a student into a course');
        $this->_postAPITestRunner($I, $this->student, 'courses/roster', $this->StudentParametersForStudentInsertion, 403, []);
    }

    public function testStudentAddAFacultyIntoACourse(ApiAuthTester $I){
        $I->wantTo('As a Student, I should not be able to add a faculty member into a course');
        $this->_postAPITestRunner($I, $this->student, 'courses/roster', $this->StudentParametersForFacultyInsertion, 403, []);
    }


    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }

}
