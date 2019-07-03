<?php
namespace Page\Acceptance\uat;

class StudentCourseAndAcademicUpdatePage
{
    public static $SubAndCourseNum='//*[@ng-bind="course.subject_course"][contains(.,"{{}}")]';
    public static $Section='//td[@ng-bind="course.section_id"][contains(.,"{{}}")]';
    public static $CourseTitle='//*[@ng-bind="course.course_title"][contains(.,"{{}}")]';
    public static $FacultyName='//*[@ng-bind="course.all_faculty_name"][contains(.,"{{}}")]';
    public static $Time='//td//strong[text()="<<>>"]/../..//td/strong[@ng-bind="course.all_faculty_name"]/../p[contains(text(),"{{}}")]';
    public static $Location='//td//strong[text()="<<>>"]/../..//td/strong[@ng-bind="course.all_faculty_name"]/../p[contains(text(),"{{}}")]';
    public static $TopCourse='//a[@title="View Courses"]';
    public static $Absences='//strong[@ng-bind="course.course_title"][contains(.,"{{}}")]//..//..//following-sibling::strong[@ng-bind="course.absense"][contains(.,"<<>>")]';
    public static $InProgressGrade='//strong[@ng-bind="course.course_title"][contains(.,"{{}}")]//..//..//following-sibling::strong[contains(@ng-bind,"course.in_progress_grade")][contains(.,"<<>>")]';
    public static  $Comment='//strong[@ng-bind="course.course_title"][contains(.,"{{}}")]//..//..//following-sibling::p[contains(@ng-bind,"course.comments")][contains(.,"<<>>")]';
    
}
