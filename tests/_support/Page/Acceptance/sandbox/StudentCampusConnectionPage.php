<?php
namespace Page\Acceptance\sandbox;

class StudentCampusConnectionPage
{
    public static $FacultyNames='//h2[contains(@ng-bind,"campus_connection.person")][contains(.,"{{}}")]';
    public static $campusScheduleButton='//h2[contains(@ng-bind,"campus_connection.person")][contains(.,"{{}}")]//..//..//following-sibling::button[contains(@class,"book_appointment_desk")]';
    public static $PrimaryConnectionLink='//div[@class="vma_new"]/strong[contains(text(),"Primary Connection")]/../..//h2[contains(text(),"{{}}")]';
    public static $FirstLocation='(//div[@class="ast_table_list"]//tbody//td/p)[1]';
    
    
    
}
