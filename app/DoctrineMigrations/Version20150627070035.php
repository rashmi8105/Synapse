<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150627070035 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $remove = '"remove"';
        $this->addSql("INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Course_Upload_Remove_Definition_ColumnName','Remove');");
        $this->addSql("INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Course_Upload_Remove_Definition_Type','Text');");
        $this->addSql("INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Course_Upload_Remove_Definition_Desc','$remove to be added to remove the record');");
        $this->addSql("INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_PrimaryConn_Definition_ColumnName','PrimaryConnect');");
        $this->addSql("INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_PrimaryConn_Definition_Type','string');");
        $this->addSql("INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_PrimaryConn_Definition_Desc','(Optional)Campus Faculty/StaffID for this student PrimaryDirectConnect');");
        $this->addSql("INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_RiskGroup_Definition_ColumnName','RiskGroupId');");
        $this->addSql("INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_RiskGroup_Definition_Type','string');");
        $this->addSql("INSERT INTO `ebi_config` (`created_by`,`created_at`,`modified_by`,`modified_at`,`deleted_by`,`deleted_at`,`key`,`value`) VALUES (NULL,NULL,NULL,NULL,NULL,NULL,'Student_Upload_RiskGroup_Definition_Desc','(Optional)Assign Risk grouip Id to the Student');");
        
        
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
