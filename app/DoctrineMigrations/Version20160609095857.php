<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * This migration script will add expected date format information
 * for Date type Profile items
 */
class Version20160609095857 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $columnName = '$$column_name$$';
        $description = '$$description$$';
        $required = '$$required$$';

        
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
 					<p><span class="italicStyler">Data Type:</span>Date
 				    <br/>
                    <p>Expected Format :</p>
                    <ul>
                    <li>mm/dd/yyyy</li>
                    </ul></p>
 				</div>
 			</div></div>'
WHERE `ebi_template_key` = 'Pdf_DateType_Body_Template';
CDATA;
        $this->addSql($pdfDateTypeQuery);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
