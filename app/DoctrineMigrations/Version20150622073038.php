<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150622073038 extends AbstractMigration
{

    /**
     *
     * @param Schema $schema            
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql("set @exist := (SELECT count(*) FROM information_schema.TABLE_CONSTRAINTS WHERE
        CONSTRAINT_SCHEMA = DATABASE() AND
        CONSTRAINT_NAME   = 'FK_9050C5B4CB90598E' AND
        CONSTRAINT_TYPE   = 'FOREIGN KEY');
        set @sqlstmt := if( @exist > 0,'ALTER TABLE ebi_question DROP FOREIGN KEY FK_9050C5B4CB90598E', 'select ''INFO: No Index Found.''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;");
        
        $this->addSql("set @exist := (SELECT count(*) FROM information_schema.TABLE_CONSTRAINTS WHERE
        CONSTRAINT_SCHEMA = DATABASE() AND
        CONSTRAINT_NAME   = 'FK_CA58D9ADCB90598E' AND
        CONSTRAINT_TYPE   = 'FOREIGN KEY');
        set @sqlstmt := if( @exist > 0,'ALTER TABLE org_question DROP FOREIGN KEY FK_CA58D9ADCB90598E', 'select ''INFO: No Index Found.''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;");
        
        $this->addSql("set @exist := (SELECT count(*) FROM information_schema.TABLE_CONSTRAINTS WHERE
        CONSTRAINT_SCHEMA = DATABASE() AND
        CONSTRAINT_NAME   = 'FK_EFD305F1CB90598E' AND
        CONSTRAINT_TYPE   = 'FOREIGN KEY');
        set @sqlstmt := if( @exist > 0,'ALTER TABLE question_type_lang DROP FOREIGN KEY FK_EFD305F1CB90598E', 'select ''INFO: No Index Found.''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;");
        
        $this->addSql("set @exist := (SELECT count(*) FROM information_schema.TABLE_CONSTRAINTS WHERE
        CONSTRAINT_SCHEMA = DATABASE() AND
        CONSTRAINT_NAME   = 'FK_6ABE9142CB90598E' AND
        CONSTRAINT_TYPE   = 'FOREIGN KEY');
        set @sqlstmt := if( @exist > 0,'ALTER TABLE ind_question DROP FOREIGN KEY FK_6ABE9142CB90598E', 'select ''INFO: No Index Found.''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;");
        
        $this->addSql("set @exist := (SELECT count(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE
        `TABLE_CATALOG` = 'def' AND `TABLE_SCHEMA` = DATABASE() AND
        `TABLE_NAME` = 'ind_question' AND `INDEX_NAME` = 'fk_ind_question_question_type1_idx');
        set @sqlstmt := if( @exist > 0,'DROP INDEX fk_ind_question_question_type1_idx ON ind_question', 'select ''INFO: No Index Found.''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;");
        
        $this->addSql("set @exist := (SELECT count(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE
        `TABLE_CATALOG` = 'def' AND `TABLE_SCHEMA` = DATABASE() AND
        `TABLE_NAME` = 'org_question' AND `INDEX_NAME` = 'IDX_CA58D9ADCB90598E');
        set @sqlstmt := if( @exist > 0,'DROP INDEX IDX_CA58D9ADCB90598E ON org_question', 'select ''INFO: No Index Found.''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;");
        
        $this->addSql("set @exist := (SELECT count(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE
        `TABLE_CATALOG` = 'def' AND `TABLE_SCHEMA` = DATABASE() AND
        `TABLE_NAME` = 'ebi_question' AND `INDEX_NAME` = 'IDX_9050C5B4CB90598E');
        set @sqlstmt := if( @exist > 0,'DROP INDEX IDX_9050C5B4CB90598E ON ebi_question', 'select ''INFO: No Index Found.''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;");
        
        $this->addSql("set @exist := (SELECT count(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE
        `TABLE_CATALOG` = 'def' AND `TABLE_SCHEMA` = DATABASE() AND
        `TABLE_NAME` = 'question_type_lang' AND `INDEX_NAME` = 'IDX_EFD305F1CB90598E');
         set @sqlstmt := if( @exist > 0,'DROP INDEX IDX_EFD305F1CB90598E ON question_type_lang', 'select ''INFO: No Index Found.''');
        PREPARE stmt FROM @sqlstmt;
        EXECUTE stmt;");
        
        $this->addSql('ALTER TABLE question_type CHANGE id id VARCHAR(4) NOT NULL');
       
        $this->addSql('ALTER TABLE ebi_question CHANGE question_type_id question_type_id VARCHAR(4) DEFAULT NULL');
        $this->addSql('ALTER TABLE org_question CHANGE question_type_id question_type_id VARCHAR(4) DEFAULT NULL');
        $this->addSql('ALTER TABLE ind_question CHANGE question_type_id question_type_id VARCHAR(4) DEFAULT NULL');
        $this->addSql('ALTER TABLE question_type_lang CHANGE question_type_id question_type_id VARCHAR(4) DEFAULT NULL');
        
        $this->addSql('ALTER TABLE ebi_question ADD CONSTRAINT FK_9050C5B4CB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE org_question ADD CONSTRAINT FK_CA58D9ADCB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE question_type_lang ADD CONSTRAINT FK_EFD305F1CB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE ind_question ADD CONSTRAINT FK_6ABE9142CB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        
        $this->addSql('CREATE INDEX `fk_ind_question_question_type1_idx` ON `ind_question` (`question_type_id` ASC)');
        $this->addSql('CREATE INDEX `IDX_CA58D9ADCB90598E` ON `org_question` (`question_type_id` ASC)');
        $this->addSql('CREATE INDEX `IDX_EFD305F1CB90598E` ON `ebi_question` (`question_type_id` ASC)');
    }

    /**
     *
     * @param Schema $schema            
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE ebi_question DROP FOREIGN KEY FK_9050C5B4CB90598E');
        $this->addSql('ALTER TABLE org_question DROP FOREIGN KEY FK_CA58D9ADCB90598E');
        $this->addSql('ALTER TABLE question_type_lang DROP FOREIGN KEY FK_EFD305F1CB90598E');
        $this->addSql('ALTER TABLE ind_question DROP FOREIGN KEY FK_6ABE9142CB90598E');
        
        $this->addSql('ALTER TABLE question_type CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE ebi_question CHANGE question_type_id question_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ind_question CHANGE question_type_id question_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE org_question CHANGE question_type_id question_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_type_lang CHANGE question_type_id question_type_id INT DEFAULT NULL');
        
        $this->addSql('ALTER TABLE ebi_question ADD CONSTRAINT FK_9050C5B4CB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE org_question ADD CONSTRAINT FK_CA58D9ADCB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE question_type_lang ADD CONSTRAINT FK_EFD305F1CB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        $this->addSql('ALTER TABLE ind_question ADD CONSTRAINT FK_6ABE9142CB90598E FOREIGN KEY (question_type_id) REFERENCES question_type (id)');
        
        $this->addSql('CREATE INDEX `fk_ind_question_question_type1_idx` ON `ind_question` (`question_type_id` ASC)');
        $this->addSql(' CREATE INDEX `fk_org_question_question_type1_idx` ON `org_question` (`question_type_id` ASC)');
        $this->addSql('CREATE INDEX `fk_ebi_question_question_type1_idx` ON `ebi_question` (`question_type_id` ASC)');
    }
}
