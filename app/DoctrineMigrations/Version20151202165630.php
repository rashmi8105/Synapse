<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151202165630 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('DROP FUNCTION IF EXISTS `safe_index_builder`;');
        $this->addSQL("CREATE DEFINER=`synapsemaster`@`%` FUNCTION `safe_index_builder`(tName varchar(255), theIndex varchar(255), theColumns varchar(255), isAdd bool, isUnique bool, setIgnore bool) RETURNS varchar(255)
	    READS SQL DATA
	    DETERMINISTIC
	    SQL SECURITY INVOKER
		BEGIN
			SET @SQLreturn = '';
	        SET @indexExists = false;
	        SET @sIgnore = '';
	        SET @unq = '';
	        SET @REGEX = 'DROP |;|DELETE |ALTER |CREATE |DISABLE |ENABLE |TRUNCATE |UPDATE |MERGE |INSERT |FROM |SELECT |[/*#]|--|UNION ';
            
	        IF (select tNAME REGEXP @REGEX) OR (select theIndex REGEXP @REGEX) OR (select theColumns REGEXP @REGEX) THEN
				return @SQLreturn;
            END IF;

				IF setIgnore THEN
					SET @sIgnore = 'IGNORE ';
	            END IF;
	            
	            IF isUnique THEN
					SET @unq = 'UNIQUE ';
	            END IF;
	        
	        	IF (SELECT 1 FROM information_schema.statistics WHERE `table_schema` =  DATABASE() AND `table_name` = tName AND `index_name` = theIndex LIMIT 1) THEN
	                    SET @indexExists = true;
				ELSE     
						SET @indexExists = false;
				END IF;
	            
	            
	            IF (isAdd AND @indexExists) THEN 
					SET @SQLreturn = CONCAT('ALTER ', @sIgnore, 'TABLE `', DATABASE(), '`.`', tName, '` DROP INDEX `',  theIndex, '`, ','ADD ', @unq, 'INDEX `', theIndex, '` ', theColumns, ';');
	            ELSEIF (isAdd AND !@indexExists) THEN
					SET @SQLreturn = CONCAT('ALTER ', @sIgnore, 'TABLE `', DATABASE(), '`.`', tName, '` ADD ', @unq ,'INDEX `', theIndex, '` ', theColumns, ';');
	            ELSEIF (!isAdd AND @indexExists) then
					SET @SQLreturn = CONCAT('ALTER ', @sIgnore, 'TABLE `', DATABASE(), '`.`', tName, '` DROP INDEX `',  theIndex, '`;');
	            ELSE
					SET @SQLreturn = '';
	            END IF;
	                    
				RETURN @SQLreturn;
       
        END");
        

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        // 
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }
}
