<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-13222; Create new tables and alter old ones with new columns
 */
class Version20180316174311 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        // create new tables
        $this->addSql("
CREATE TABLE `synapse`.`ebi_download_type` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `download_type` VARCHAR(45) NOT NULL,
  `download_display_name` VARCHAR(45) NOT NULL,
  `created_at` DATETIME NULL,
  `modified_at` DATETIME NULL,
  `deleted_at` DATETIME NULL,
  `created_by` INT(11) NULL,
  `modified_by` INT(11) NULL,
  `deleted_by` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `IDX_23CCE5ABDE12AB56` (`created_by` ASC),
  INDEX `IDX_23CCE5AB25F94802` (`deleted_by` ASC),
  INDEX `IDX_23CCE5AB1F6FA0AF` (`modified_by` ASC),
  CONSTRAINT `FK_23CCE5ABDE12AB56`
    FOREIGN KEY (`created_by`)
    REFERENCES `synapse`.`person` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_23CCE5AB25F94802`
    FOREIGN KEY (`modified_by`)
    REFERENCES `synapse`.`person` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_23CCE5AB1F6FA0AF`
    FOREIGN KEY (`deleted_by`)
    REFERENCES `synapse`.`person` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);");

        $this->addSql("
CREATE TABLE `upload_ebi_metadata_column_header_download_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upload_id` int(11) DEFAULT NULL,
  `ebi_metadata_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `ebi_download_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_38A3E151DE12AB56` (`created_by`),
  KEY `IDX_38A3E15125F94802` (`modified_by`),
  KEY `IDX_38A3E1511F6FA0AF` (`deleted_by`),
  KEY `IDX_38A3E151CCCFBA31` (`upload_id`),
  KEY `IDX_38A3E151BB49FE75` (`ebi_metadata_id`),
  KEY `IDX_38A3E1512B7FEA0` (`ebi_download_type_id`),
  CONSTRAINT `FK_38A3E151DE12AB56` FOREIGN KEY (`deleted_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_38A3E15125F94802` FOREIGN KEY (`modified_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_38A3E1511F6FA0AF` FOREIGN KEY (`ebi_metadata_id`) REFERENCES `ebi_metadata` (`id`),
  CONSTRAINT `FK_38A3E151CCCFBA31` FOREIGN KEY (`upload_id`) REFERENCES `upload` (`id`),
  CONSTRAINT `FK_38A3E151BB49FE75` FOREIGN KEY (`created_by`) REFERENCES `person` (`id`),
  CONSTRAINT `FK_38A3E1512B7FEA0` FOREIGN KEY (`ebi_download_type_id`) REFERENCES ebi_download_type (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
");

        $this->addSql("
ALTER TABLE `synapse`.`upload_column_header_map`
ADD COLUMN `ebi_download_type_id` INT(11) NULL AFTER `upload_column_header_id`,
ADD INDEX `IDX_EBCE7172B7FEA0` (`ebi_download_type_id` ASC);
ALTER TABLE `synapse`.`upload_column_header_map`
ADD CONSTRAINT `FK_EBCE7172B7FEA0`
  FOREIGN KEY (`ebi_download_type_id`)
  REFERENCES `synapse`.`ebi_download_type` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;");

        $this->addSql("ALTER TABLE `synapse`.`upload` ADD COLUMN `upload_display_name` VARCHAR(255) NOT NULL AFTER `upload_name`;");
        $this->addSql("ALTER TABLE `synapse`.`upload_column_header` ADD COLUMN `upload_column_display_name` VARCHAR(255) NOT NULL AFTER `upload_column_name`;");


   }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
