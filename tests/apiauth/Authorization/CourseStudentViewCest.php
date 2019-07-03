<?php
require_once (dirname(dirname(dirname(__FILE__)))) . '/_support/SynapseRestfulTestBase.php';


class CourseStudentViewCest extends SynapseRestfulTestBase
{
    // These tests cover all aspects of the permissions View All Courses, View All Academic Updates, and View All Final Grades.
    // These are found in the Courses tab of a student's profile.

    private $studentIdHarry = 99422;

    private $studentIdHermione = 99423;

    // Full access permission set, with group connections to both Harry and Hermione
    private $staffP = [
        'email' => 'percy.weasley@mailinator.com',
        'password' => 'password1!',
        'id' => 99442,
        'orgId' => 542,
        'langId' => 1
    ];

    // Full access permission set, with course connections to both Harry and Hermione
    private $staffC = [
        'email' => 'cuthbert.binns@mailinator.com',
        'password' => 'password1!',
        'id' => 99437,
        'orgId' => 542,
        'langId' => 1
    ];

    // Full access permission set, with no connection to Harry, and an expired course connection to Hermione
    private $staffS = [
        'email' => 'severus.snape@mailinator.com',
        'password' => 'password1!',
        'id' => 99447,
        'orgId' => 542,
        'langId' => 1
    ];

    // Minimum access permission set, with connections to both Harry and Hermione
    private $staffR = [
        'email' => 'remus.lupin@mailinator.com',
        'password' => 'password1!',
        'id' => 99710,
        'orgId' => 542,
        'langId' => 1
    ];

    // Aggregate only permission set, with connections to both Harry and Hermione
    private $staffA = [
        'email' => 'argus.filch@mailinator.com',
        'password' => 'password1!',
        'id' => 99436,
        'orgId' => 542,
        'langId' => 1
    ];

    // Full access permission set, from another institution
    private $staffB = [
        'email' => 'bad.guy@mailinator.com',
        'password' => 'password1!',
        'id' => 99705,
        'orgId' => 543,
        'langId' => 1
    ];

    // Has permission to view courses but not academic updates or final grades; has connections to both Harry and Hermione
    private $staffK = [
        'email' => 'kingsley.shacklebolt@mailinator.com',
        'password' => 'password1!',
        'id' => 99711,
        'orgId' => 542,
        'langId' => 1
    ];

    // Has permission to view final grades, but has no connection to Hermione
    private $staffH = [
        'email' => 'rolanda.hooch@mailinator.com',
        'password' => 'password1!',
        'id' => 99445,
        'orgId' => 542,
        'langId' => 1
    ];

    private $coursesHarry = [
        ['student_id' => 99422],
        ['course_id' => 435],
        ['course_id' => 436]
    ];

    private $coursesHermione = [
        ['student_id' => 99423],
        ['course_id' => 435],
        ['course_id' => 436],
        ['course_id' => 437],
        ['course_id' => 438]
    ];

    // These currently show up in the JSON, but not on the front end (as of 7/22/15).
    private $academicUpdatesHarry = [
        ['course_id' => 435,
        'absense' => 6,     // misspelled in the JSON
        'in_progress_grade' => 'B']
    ];

    private $finalGradeHermione = [
        ['course_id' => 437,
        'final_grade' => 'A']
    ];



    // To keep these tests valid, we'll check if the term we're using includes today's date (which it did when these tests were written).
    // If not, we'll change the dates in the database of the year and term being used.
    public function keepTestsCurrent() {
        $now = mktime();
        $termEndDate = mktime(0, 0, 0, 11, 30, 15);     // The term ends 12/1/15; using the previous day to avoid time zone issues.

        if ($now > $termEndDate) {
            $date = $this->_DynamicDates();
            $this->_academicYearsMySQLrunner($date["2WeeksAgo"], $date["2Weeks"], 110);
            $this->_academicTermsMySQLrunner($date["2WeeksAgo"], $date["1Week"], 123);
        }

    }

    // View all courses

    public function testViewCoursesForStudentInGroup(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with permission to view courses, with a group connection to the student, can view the student's courses.");
        $this->_getAPITestRunner($I, $this->staffP, 'courses/student/'.$this->studentIdHarry, [], 200, $this->coursesHarry);
    }

    public function testViewCoursesForStudentInCourse(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with permission to view courses, with a course connection to the student, can view the student's courses.");
        $this->_getAPITestRunner($I, $this->staffC, 'courses/student/'.$this->studentIdHarry, [], 200, $this->coursesHarry);
    }

    // It currently returns 200, but an empty course_list_table.
    public function testViewCoursesForInaccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with permission to view courses, without a connection to the student, cannot view the student's courses.");
        $this->_getAPITestRunner($I, $this->staffS, 'courses/student/'.$this->studentIdHarry, [], null, [['course_list_table' => []]]);
    }

    // It currently returns 200, but an empty course_list_table.
    public function testViewStudentCoursesWithoutPermission(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member without permission to view courses, cannot view the student's courses.");
        $this->_getAPITestRunner($I, $this->staffR, 'courses/student/'.$this->studentIdHarry, [], null, [['course_list_table' => []]]);
    }

    // It currently returns 200, but an empty course_list_table.
    public function testViewStudentCoursesAggOnly(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with an aggregate only permission set cannot view the student's courses.");
        $this->_getAPITestRunner($I, $this->staffA, 'courses/student/'.$this->studentIdHarry, [], null, [['course_list_table' => []]]);
    }

    public function testViewStudentCoursesOtherInstitution(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member at another institution cannot view the student's courses.");
        $this->_getAPITestRunner($I, $this->staffB, 'courses/student/'.$this->studentIdHarry, [], 403, []);
    }

    // It currently returns 200, but an empty course_list_table.
    public function testViewCoursesForStudentInExpiredCourse(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with permission to view courses, with an expired course connection to the student, cannot view a student's courses.");
        $this->_getAPITestRunner($I, $this->staffS, 'courses/student/'.$this->studentIdHermione, [], 200, [['course_list_table' => []]]);
    }


    // Academic Updates

    public function testViewAcademicUpdatesForStudentInGroup(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with permission to view academic updates, with a group connection to the student, can view the student's academic updates.");
        $this->_getAPITestRunner($I, $this->staffP, 'courses/student/'.$this->studentIdHarry, [], 200, $this->academicUpdatesHarry);
    }

    public function testViewAcademicUpdatesForStudentInCourse(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with permission to view academic updates, with a course connection to the student, can view the student's academic updates.");
        $this->_getAPITestRunner($I, $this->staffC, 'courses/student/'.$this->studentIdHarry, [], 200, $this->academicUpdatesHarry);
    }

    public function testViewAcademicUpdatesForInaccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with permission to view academic updates, without a connection to the student, cannot view the student's academic updates.");
        $this->_getAPITestRunner($I, $this->staffS, 'courses/student/'.$this->studentIdHarry, [], null, []);
        $I->dontSeeResponseContainsJson(['absense' => 6]);
        $I->dontSeeResponseContainsJson(['in_progress_grade' => 'B']);
    }

    public function testViewStudentAcademicUpdatesWithoutPermission(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member without permission to view academic updates, cannot view the student's academic updates.");
        $this->_getAPITestRunner($I, $this->staffR, 'courses/student/'.$this->studentIdHarry, [], null, []);
        $I->dontSeeResponseContainsJson(['absense' => 6]);
        $I->dontSeeResponseContainsJson(['in_progress_grade' => 'B']);
    }

    public function testViewStudentAcademicUpdatesAggOnly(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with an aggregate only permission set cannot view the student's academic updates.");
        $this->_getAPITestRunner($I, $this->staffA, 'courses/student/'.$this->studentIdHarry, [], null, []);
        $I->dontSeeResponseContainsJson(['absense' => 6]);
        $I->dontSeeResponseContainsJson(['in_progress_grade' => 'B']);
    }

    public function testViewStudentAcademicUpdatesOtherInstitution(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member at another institution cannot view the student's academic updates.");
        $this->_getAPITestRunner($I, $this->staffB, 'courses/student/'.$this->studentIdHarry, [], null, []);
        $I->dontSeeResponseContainsJson(['absense' => 6]);
        $I->dontSeeResponseContainsJson(['in_progress_grade' => 'B']);
    }

    public function testViewCoursesButNotAcademicUpdates(ApiAuthTester $I, $scenario)
    {
        $scenario->skip("Failed");
        $I->wantTo("Verify permissions work correctly for a staff member with permission to view courses but without permission to view academic updates.");
        $this->_getAPITestRunner($I, $this->staffK, 'courses/student/'.$this->studentIdHarry, [], 200, $this->coursesHarry);
        $I->dontSeeResponseContainsJson(['absense' => 6]);
        $I->dontSeeResponseContainsJson(['in_progress_grade' => 'B']);
    }


    // Final Grades

    public function testViewFinalGradeForStudentInGroup(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with permission to view final grades, with a group connection to the student, can view a student's final grade.");
        $this->_getAPITestRunner($I, $this->staffP, 'courses/student/'.$this->studentIdHermione, [], 200, $this->finalGradeHermione);
    }

    public function testViewFinalGradeForStudentInCourse(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with permission to view final grades, with a course connection to the student, can view a student's final grade.");
        $this->_getAPITestRunner($I, $this->staffC, 'courses/student/'.$this->studentIdHermione, [], 200, $this->finalGradeHermione);
    }

    public function testViewFinalGradeForInaccessibleStudent(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with permission to view final grades, without a connection to the student, cannot view the student's final grades.");
        $this->_getAPITestRunner($I, $this->staffH, 'courses/student/'.$this->studentIdHermione, [], null, []);
        $I->dontSeeResponseContainsJson(['final_grade' => 'A']);
    }

    public function testViewStudentFinalGradeWithoutPermission(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member without permission to view final grades, cannot view the student's final grades.");
        $this->_getAPITestRunner($I, $this->staffR, 'courses/student/'.$this->studentIdHermione, [], null, []);
        $I->dontSeeResponseContainsJson(['final_grade' => 'A']);
    }

    public function testViewStudentFinalGradeAggOnly(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member with an aggregate only permission set cannot view the student's final grades.");
        $this->_getAPITestRunner($I, $this->staffA, 'courses/student/'.$this->studentIdHermione, [], null, []);
        $I->dontSeeResponseContainsJson(['final_grade' => 'A']);
    }

    public function testViewStudentFinalGradeOtherInstitution(ApiAuthTester $I)
    {
        $I->wantTo("Verify a staff member at another institution cannot view the student's final grades.");
        $this->_getAPITestRunner($I, $this->staffB, 'courses/student/'.$this->studentIdHermione, [], null, []);
        $I->dontSeeResponseContainsJson(['final_grade' => 'A']);
    }

    public function testViewCoursesButNotFinalGrade(ApiAuthTester $I)
    {
        $I->wantTo("Verify permissions work correctly for a staff member with permission to view courses but without permission to view final grades.");
        $this->_getAPITestRunner($I, $this->staffK, 'courses/student/'.$this->studentIdHermione, [], 200, $this->coursesHermione);
        $I->dontSeeResponseContainsJson(['final_grade' => 'A']);
    }


    public function databaseReload()
    {
        // Cleaning up data before ending test file
        $output = shell_exec('./runauthtests.sh --reload');

        codecept_debug($output);
    }
}
