<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150915100331 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $columnName = '$$column_name$$';
        $required = '$$required$$';
        $description = '$$description$$';
        $validValues = '$$valid_values$$';
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
 					<p><span class="italicStyler">Data Type:</span>Numbers Only</p>
 				</div>
 			</div>
 			<div class="validvalues align1">
 				<p style="margin-bottom : 15px;">$validValues</p>
 			</div>
 			</div>' WHERE `ebi_template_key` = 'Pdf_NumberType_Body_Template'
CDATA;
        $this->addSql($query);
        
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
