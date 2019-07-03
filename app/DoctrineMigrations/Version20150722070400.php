<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150722070400 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $columnName = '$$column_name$$';
        $description = '$$description$$';
        $length = '$$length$$';
        $required = '$$required$$';
        $minValue = '$$min_value$$';
        $maxValue = '$$max_value$$';
        $valueList = '$$value_list$$';
        $query = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<div id="outerContainer">
 			<div class="align1 subHeadingDiv">
 				<div class="columnNameContainer details"><p class="idHeading">$columnName &nbsp;<span style="font-style:italic;color:#666;font-size:16px;"> $required</span></p></div>
 				<div class="columnNameContainer dataTypeContainer"> <p>$description</p></div>
 			</div>
 			<div class="align1 userInfo">
 				<p class="userInfoHeading">Upload Information</p>
 				<div class="horizontalDottedLine"></div>
 			</div>
 			<div class="align1 userInfoDetails">
 				<div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span class="boldStyler">$columnName</span></p></div>
 				<div class="columnNameContainer dataTypeContainer">
 					<p><span class="italicStyler">Data Type:</span>Text</p>
 					<p>(Max Length: $length characters)</p>
 				</div>
 			</div></div>'
WHERE `ebi_template_key` = 'Pdf_TextType_Body_Template';
CDATA;
        $this->addSql($query);
        
        
        $pdfStringTypeQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<div id="outerContainer">
 			<div class="align1 subHeadingDiv">
 				<div class="columnNameContainer details"><p class="idHeading">$columnName &nbsp;<span style="font-style:italic;color:#666;font-size:16px;"> $required</span></p></div>
 				<div class="columnNameContainer dataTypeContainer"> <p>$description</p></div>
 			</div>
 			<div class="align1 userInfo">
 				<p class="userInfoHeading">Upload Information</p>
 				<div class="horizontalDottedLine"></div>
 			</div>
 			<div class="align1 userInfoDetails">
 				<div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span class="boldStyler">$columnName</span></p></div>
 				<div class="columnNameContainer dataTypeContainer">
 					<p><span class="italicStyler">Data Type:</span>Letters and Numbers</p>
 					<p>(Max Length: $length characters)</p>
 				</div>
 			</div></div>'
WHERE `ebi_template_key` = 'Pdf_StringType_Body_Template';
CDATA;
        $this->addSql($pdfStringTypeQuery);
        
        
        
        $pdfNumberTypeQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<div id="outerContainer">
 			<div class="align1 subHeadingDiv">
 				<div class="columnNameContainer details"><p class="idHeading">$columnName &nbsp;<span style="font-style:italic;color:#666;font-size:16px;"> $required</span></p></div>
 				<div class="columnNameContainer dataTypeContainer"> <p>$description</p></div>
 			</div>
 			<div class="align1 userInfo">
 				<p class="userInfoHeading">Upload Information</p>
 				<div class="horizontalDottedLine"></div>
 			</div>
 			<div class="align1 userInfoDetails">
 				<div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span class="boldStyler">$columnName</span></p></div>
 				<div class="columnNameContainer dataTypeContainer">
 					<p><span class="italicStyler">Data Type:</span>Numbers Only</p>
 				</div>
 			</div>
 			<div class="validvalues align1">
 				<p style="margin-bottom : 15px;">Valid Values: Minimum : $minValue Maximum : $maxValue</p>
 			</div>
 			</div>'
WHERE `ebi_template_key` = 'Pdf_NumberType_Body_Template';
CDATA;
        $this->addSql($pdfNumberTypeQuery);
        
        
        
        $pdfDateTypeQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<div id="outerContainer">
 			<div class="align1 subHeadingDiv">
 				<div class="columnNameContainer details"><p class="idHeading">$columnName &nbsp;<span style="font-style:italic;color:#666;font-size:16px;"> $required</span></p></div>
 				<div class="columnNameContainer dataTypeContainer"> <p>$description</p></div>
 			</div>
 			<div class="align1 userInfo">
 				<p class="userInfoHeading">Upload Information</p>
 				<div class="horizontalDottedLine"></div>
 			</div>
 			<div class="align1 userInfoDetails">
 				<div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span class="boldStyler">$columnName</span></p></div>
 				<div class="columnNameContainer dataTypeContainer">
 					<p><span class="italicStyler">Data Type:</span>Date</p>
 				</div>
 			</div></div>'
WHERE `ebi_template_key` = 'Pdf_DateType_Body_Template';
CDATA;
        $this->addSql($pdfDateTypeQuery);
        
        
        
        $pdfCategoryTypeQuery = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '<div id="outerContainer">
 			<div class="align1 subHeadingDiv">
 				<div class="columnNameContainer details"><p class="idHeading">$columnName &nbsp;<span style="font-style:italic;color:#666;font-size:16px;"> $required</span></p></div>
 				<div class="columnNameContainer dataTypeContainer"> <p>$description</p></div>
 			</div>
 			<div class="align1 userInfo">
 				<p class="userInfoHeading">Upload Information</p>
 				<div class="horizontalDottedLine"></div>
 			</div>
 			<div class="align1 userInfoDetails">
 				<div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span class="boldStyler">$columnName</span></p></div>
 				<div class="columnNameContainer dataTypeContainer">
 					<p><span class="italicStyler">Data Type:</span>Category</p>
 				</div>
 			</div>
 			<div class="validvalues align1">
 				<p>Valid Values:</p>
 				<ul class="valueslist">
 					$valueList
 				</ul>
 			</div>
 			</div>'
WHERE `ebi_template_key` = 'Pdf_CategoryType_Body_Template';
CDATA;
        $this->addSql($pdfCategoryTypeQuery);
        
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

       
    }
}
