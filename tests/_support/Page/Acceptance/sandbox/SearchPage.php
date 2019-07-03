<?php
namespace Page\Acceptance\sandbox;

class SearchPage
{
    // include url of current page
    public static $SearchBox='//input[contains(@class,"user-search-input")]';
    public static $SelectStd='//ul[contains(@class,"dropdown-menu")]//a[contains(.,"{{}}")]';
    
    //custom search window///
    public static $SearchRightPanel='//*[contains(@id,"search")]//ul/li/a[contains(.,"{{}}")]';
    public static $SelectCheckBox='//p[contains(.,"{{}}")]//..//following-sibling::span';
    public static $SaveSearchBtn='//input[contains(@title,"Save Search")]';
    public static $SearchBtn='//input[contains(@title,"Click Here to Search")]';
    public static $SaveSearchWin='//div[@id="save-search-box"]';
    public static $FillSearch='//input[@placeholder="name this search"]';
    public static $SelectFaculty='//input[@placeholder="name or email"]';
    public static $saveBtnSmallWin='//input[contains(@class,"shared-search-popup-cont-btn")]';
    public static $ActivityOption='//*[contains(@class,"activities-subtitle")][contains(.,"{{}}")]//..//following-sibling::ul//span[contains(@title,"<<>>")]';
    public static $StudentLink='//a[contains(text(),"{{}}")]';
    public static $StudentNameInSearchSuggestionList='//strong[contains(text(),"{{}}")]';
    public static $SelectOption='//p[contains(text(),"{{}}")]';
    
    //saved search locators
    public static $SearchInList='//span[contains(@class,"searchName")][contains(.,"{{}}")]';
    public static $EditIcon='//span[contains(@class,"searchName")][contains(.,"{{}}")]//..//..//following-sibling::td//a[contains(@class,"edit-img")][not(contains(@class,"hide"))]';
    public static $DeleteIcon='//span[contains(@class,"searchName")][contains(.,"{{}}")]//..//..//following-sibling::td//a[contains(@class,"delete-img")][not(contains(@class,"hide"))]';
    public static $ShareIcon='//span[contains(@class,"searchName")][contains(.,"{{}}")]//..//..//following-sibling::td//a[contains(@class,"share-img")][not(contains(@class,"hide"))]';
    public static $ShareBtn='//*[@value="Share"]';
    public static $DeleteBtn='//button[contains(.,"Delete this Saved Search")]';
    
    public static $SelectAll='//span[@title="Select All"]';
    public static $BulkButton='//span[@class="btnstyle"]';
    public static $ActivityUnderBulk='//ul[contains(@class,"dropdown-menu")]//li[contains(text(),"{{}}")]';
    // include url of current page
    
    //custom search window///
   
    public static $ProfilePanelLinks='//*[@id="shared-search"]//div[contains(@class,"modal")]//ul//li//a[contains(.,"{{}}")]|//*[@id="shared-search"]//div[contains(@class,"modal")]//ul//li//p[contains(.,"{{}}")]';
    public static $ProfileBlockCheckBoxSelection='//p[contains(.,"{{}}")]//..//..//following-sibling::label[contains(.,"<<>>")]//span';
    public static $SaveSearchBtnModalWin='//input[@title="Click Here to Search"]';
    public static $StartDateTextBoxForDateISP='//*[contains(@id,"startDate")]';
    public static $StartDateCalender='//*[contains(@id,"startDate")]//..//following-sibling::ul[contains(@class,"dropdown-menu")]';
    public static $SelectStartDate='//*[contains(@id,"startDate")]//..//following-sibling::ul//button[not(contains(@disabled,"disabled"))]/span[contains(.,"{{}}")]';
    public static $EndDateTextBoxForDateIsp='//*[contains(@id,"endDate")]';
    public static $EndDateCalender='//*[contains(@id,"endDate")]//..//following-sibling::ul[contains(@class,"dropdown-menu")]';
    public static $SelectEndDate='//*[contains(@id,"endDate")]//..//following-sibling::ul//button[not(contains(@disabled,"disabled"))]/span[contains(.,"{{}}")]';
    public static $NumberISPSingleTextBox='//*[contains(@id,"number")]//div//input[@name="single"]';
    //saved search locators
   
    
    
    
}
