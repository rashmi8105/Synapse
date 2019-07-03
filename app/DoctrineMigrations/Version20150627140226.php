<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150627140226 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql("DROP FUNCTION IF EXISTS `GetGroupStudentCount`");
        $query = <<<CDATA
CREATE FUNCTION `GetGroupStudentCount` (GivenID  VARCHAR(1024)) RETURNS varchar(1024) CHARSET latin1
DETERMINISTIC
BEGIN
        
    DECLARE rv,q,queue,queue_children,front_id VARCHAR(1024);
    DECLARE queue_length,pos INT;
        
    SET rv = '';
    SET queue = GivenID;
    SET queue_length = 1;
        
    WHILE queue_length > 0 DO
        SET front_id = queue;
        IF queue_length = 1 THEN
            SET queue = '';
        ELSE
            SET pos = LOCATE(',',queue) + 1;
            SET q = SUBSTR(queue,pos);
            SET queue = q;
        END IF;
        SET queue_length = queue_length - 1;
        
        SELECT IFNULL(qc,'') INTO queue_children
        FROM (SELECT GROUP_CONCAT(id) qc
        FROM org_group WHERE parent_group_id = front_id) A;
        
        IF LENGTH(queue_children) = 0 THEN
            IF LENGTH(queue) = 0 THEN
                SET queue_length = 0;
            END IF;
        ELSE
            IF LENGTH(rv) = 0 THEN
                SET rv = queue_children;
            ELSE
                SET rv = CONCAT(rv,',',queue_children);
            END IF;
            IF LENGTH(queue) = 0 THEN
                SET queue = queue_children;
            ELSE
                SET queue = CONCAT(queue,',',queue_children);
            END IF;
            SET queue_length = LENGTH(queue) - LENGTH(REPLACE(queue,',','')) + 1;
        END IF;
    END WHILE;
        
	set @cnt := (SELECT count(*) FROM org_group_students WHERE deleted_at IS NULL AND org_group_id IN (rv));
        
    RETURN @cnt;
END
CDATA;
        $this->addSql($query);
        
        $this->addSql("DROP FUNCTION IF EXISTS `GetSubGroupFacultyCount`");
        
        $query = <<<CDATA
        CREATE FUNCTION `GetSubGroupFacultyCount` (GivenID  VARCHAR(1024)) RETURNS varchar(1024) CHARSET latin1
DETERMINISTIC
BEGIN
        
    DECLARE rv,q,queue,queue_children,front_id VARCHAR(1024);
    DECLARE queue_length,pos INT;
        
    SET rv = '';
    SET queue = GivenID;
    SET queue_length = 1;
        
    WHILE queue_length > 0 DO
        SET front_id = queue;
        IF queue_length = 1 THEN
            SET queue = '';
        ELSE
            SET pos = LOCATE(',',queue) + 1;
            SET q = SUBSTR(queue,pos);
            SET queue = q;
        END IF;
        SET queue_length = queue_length - 1;
        
        SELECT IFNULL(qc,'') INTO queue_children
        FROM (SELECT GROUP_CONCAT(id) qc
        FROM org_group WHERE parent_group_id = front_id) A;
        
        IF LENGTH(queue_children) = 0 THEN
            IF LENGTH(queue) = 0 THEN
                SET queue_length = 0;
            END IF;
        ELSE
            IF LENGTH(rv) = 0 THEN
                SET rv = queue_children;
            ELSE
                SET rv = CONCAT(rv,',',queue_children);
            END IF;
            IF LENGTH(queue) = 0 THEN
                SET queue = queue_children;
            ELSE
                SET queue = CONCAT(queue,',',queue_children);
            END IF;
            SET queue_length = LENGTH(queue) - LENGTH(REPLACE(queue,',','')) + 1;
        END IF;
    END WHILE;
        
	set @cnt := (SELECT count(*) FROM org_group_faculty WHERE deleted_at IS NULL AND org_group_id IN (rv));
        
    RETURN @cnt;
END
CDATA;
        $this->addSql($query);
        
        $this->addSql("DROP FUNCTION IF EXISTS `GetSubGroupCount`");
        $query = <<<CDATA
CREATE FUNCTION `GetSubGroupCount` (GivenID  VARCHAR(1024)) RETURNS varchar(1024) CHARSET latin1
DETERMINISTIC
BEGIN

    DECLARE rv,q,queue,queue_children,front_id VARCHAR(1024);
    DECLARE queue_length,pos INT;

    SET rv = '';
    SET queue = GivenID;		
    SET queue_length = 1;

    WHILE queue_length > 0 DO
        SET front_id = queue;
        IF queue_length = 1 THEN
            SET queue = '';
        ELSE
            SET pos = LOCATE(',',queue) + 1;
            SET q = SUBSTR(queue,pos);
            SET queue = q;
        END IF;
        SET queue_length = queue_length - 1;

        SELECT IFNULL(qc,'') INTO queue_children
        FROM (SELECT GROUP_CONCAT(id) qc
        FROM org_group WHERE parent_group_id = front_id) A;

        IF LENGTH(queue_children) = 0 THEN
            IF LENGTH(queue) = 0 THEN
                SET queue_length = 0;
            END IF;
        ELSE
            IF LENGTH(rv) = 0 THEN
                SET rv = queue_children;
            ELSE
                SET rv = CONCAT(rv,',',queue_children);
            END IF;
            IF LENGTH(queue) = 0 THEN
                SET queue = queue_children;
            ELSE
                SET queue = CONCAT(queue,',',queue_children);
            END IF;
            SET queue_length = LENGTH(queue) - LENGTH(REPLACE(queue,',','')) + 1;
        END IF;
    END WHILE;
	
    if LENGTH(rv) > 0 THEN
		SET rv := (select length(rv) - length(replace(rv, ',', '')));
        IF rv != 0 THEN
					SET rv = rv + 1 ;
				ELSE
			SET rv = 1;
		END IF;
		RETURN rv ;
	ELSE 
		return 0;
	END IF;
END
CDATA;
        $this->addSql($query);
        $this->addsql("ALTER TABLE `synapse`.`org_group` ADD INDEX `parent_group_id_IDX` (`parent_group_id` ASC)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()
            ->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}