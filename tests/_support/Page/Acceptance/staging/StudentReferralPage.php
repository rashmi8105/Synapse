<?php
namespace Page\Acceptance\stage;

class StudentReferralPage
{

    public static $ReferralDate='//div[contains(@class,"date_number")][text()="{{}}"]';
    public static $referralDay='//div[contains(@class,"date_day")][text()="{{}}"]';
    public static $ReferralMonth='//div[contains(@class,"date_date")][contains(text(),"{{}}")]';
    public static $ReferralYr='//div[contains(@class,"date_date")]/span[text()="{{}}"]';
    public static $why='//div[contains(.,"Why:")]//following-sibling::p[contains(text(),"{{}}")]';
    public static $CreatedBy='//*[@ng-bind="referral.created_by"][contains(text(),"{{}}")]';
    public static $CreatedByTitle='//div[@class="who_address"]/p[contains(.,"{{}}")]';
    public static $AssignedTo='//*[@ng-bind="referral.assigned_to"][contains(text(),"{{}}")]';
    public static $AssignedToTitle='//*[@ng-bind="referral.assigned_to"]//following-sibling::span[contains(.,"{{}}")]';
    public static $Description='//p[@ng-bind="referral.description"][contains(text(),"{{}}")]';
   
}
