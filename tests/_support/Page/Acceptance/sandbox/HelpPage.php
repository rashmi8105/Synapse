<?php
namespace Page\Acceptance\sandbox;

class HelpPage
{
    public static $KnowledgeBaseLink='//a[@title="View Knowledge Base"]';
    public static $addButton='//button[contains(@title,"{{}}")]';
    public static $editButton='//a[contains(text(),"{{}}")]/../../..//li/a[contains(text(),"Edit")]';
    public static $removeButton='//a[contains(text(),"{{}}")]/../../..//li/a[contains(text(),"Remove")]';
    public static $linkTextField='//input[@name="link"]';
    public static $TitleTextField='//input[@name="title"]';
    public static $DescriptionTextField='//textarea[@name="description"]';
    public static $saveButton='//button[text()="save"]';
    public static $RemoveThislinkDocumentButton='//button[@ng-click="confirm()"]';
    public static $AttachDocument='//input[@type="file"]'; 
    public static $MaterialOnList='//a[contains(text(),"{{}}")]';
    
    public static $fileTicketLink = '//a[@title="File a Ticket Here"]';
    public static $errorMessage = "//label[text()='The ticketing service is currently unavailable.  Please try again later.']";
    public static $fileATicketHeading = '//h3[text()="File a Ticket"]';
    public static $fileTicketHintText = '//div[text()="Send your question or comment directly to the Mapworks support team"]';
    public static $fileTicketRequiredFieldText = '//span[text()="Fields marked with asterisks are required"]';
    public static $selectACategory = '//button[contains(@id,"dropdownMenu-list")]';
   // public static $selectACategoryToolTip = '//div[@title="Filter by Category Name"]';
    public static $itemSelected = "//ul[contains(@class,'dropdown-menu')]//li/a[contains(.,'{{}}')]";
    public static $Subject = '//input[@name="subject"]';
    public static $Description = '//textarea[@name="description"]';
    public static $fileTicketSubmitButton='//button[text()="File ticket"]';
    public static $EmailOfCoordniator='//a[contains(@href,"{{}}")]';
     public static $attachFile="//input[@type='file']";

}
