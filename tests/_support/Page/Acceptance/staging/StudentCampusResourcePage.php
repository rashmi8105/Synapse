<?php
namespace Page\Acceptance\stage;

class StudentCampusResourcePage {
    public static $campusOnStd='//h2[@ng-bind="campus_resource.resource_name"][text()="{{}}"]';
    public static $PageHeading='//h1[text()="{{}}"]';
    public static $referralDesc='//*[@ng-bind="referral.description"][text()="{{}}"]';
    public static $VisitLink='//h2[@ng-bind="campus_resource.resource_name"][text()="{{}}"]//..//following-sibling::div[@class="vcr_web"]/a';

}
