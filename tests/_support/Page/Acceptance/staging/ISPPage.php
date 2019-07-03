<?php
namespace Page\Acceptance\stage;

class ISPPage
{
     public static $AddISPButton='//button[contains(@title,"Profile")]';
     public static $datatypedropdown='//button[@id="dropdownMenu-list"]';
     public static $datetype='//span[contains(text(),"{{}}")]';
     public static $NameOfISP='//input[@title="Enter name"]';
     public static $ColumnHeader='//input[contains(@placeholder,"Enter Column Header")]|//input[contains(@placeholder,"enter column header")]';
     public static $answer1='//input[@id="answer"]';
     public static $answer2='//input[@id="val0"]'; 
     public static $Savebutton='//button[@class="btn save"]'; 
     public static $ISPInList='//span[contains(text(),"{{}}")]';
     public static $EditIconforISP='//td//span[contains(text(),"{{}}")]/../../../td//input[@class="editImg"]';
     public static $RemoveIconforISP='//td//span[contains(text(),"{{}}")]/../../../td//input[@class="removeImg"]';
     public static $ConfirmDialogBoxbutton='//button[@ng-click="confirm()"]';
     public static $Description='//textarea[@title="Enter Description"]';
     public static $MinValue='//input[@name="minnumber"]';
     public static $MaxValue='//input[@name="maxnumber"]';
     public static $decimaldropdown='//button[@id="decimalpoints"]';
     public static $decimalValue='//button[@id="decimalpoints"]/..//span[contains(text(),"{{}}")]';
      public static $PaginationContainer = ".//*[@id='mac-pagination-container']/ul/li";
    public static $lastPaginationContainer = ".//*[@id='mac-pagination-container']/ul/li/a[@ng-switch-when='last']";

}
