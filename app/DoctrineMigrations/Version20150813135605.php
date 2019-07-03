<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150813135605 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $colName = '$$column_name$$';
        $req = '$$required$$';
        $description = '$$description$$';
        $length = '$$length$$';
        $optionalTitle = '$$optionalTitle$$';
        $optional = '$$optional$$';
        
        
        $query = <<<CDATA
        UPDATE `synapse`.`ebi_template_lang` SET `body`=' <div id="outerContainer"> <div class="align1 subHeadingDiv"> <div class="columnNameContainer details"><p class="idHeading">$colName &nbsp;<span style="font-style:italic;color:#666;font-size:16px;"> $req</span></p></div> <div class="columnNameContainer dataTypeContainer"> <p>$description</p></div> </div> <div class="align1 userInfo"> <p class="userInfoHeading">Upload Information</p> <div class="horizontalDottedLine"></div> </div> <div class="align1 userInfoDetails"> <div class="columnNameContainer columnNameContainer2"><p><span class="italicStyler">Column Name:</span> <span class="boldStyler">$colName</span></p></div> <div class="columnNameContainer dataTypeContainer"> <p><span class="italicStyler">Data Type:</span>Text</p> <p>(Max Length: $length characters)</p> </div></div> <div class="validvalues align1"> <p>$optionalTitle</p> <ul class="valueslist"> $optional </ul> </div> </div>' WHERE `ebi_template_key`='Pdf_TextType_Body_Template' and`lang_id`='1';
CDATA;
        $this->addSQL($query);
        
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
