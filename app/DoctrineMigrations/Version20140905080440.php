<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140905080440 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE org_group (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, parent_group_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, group_name VARCHAR(100) DEFAULT NULL, INDEX IDX_938DB7A732C8A3DE (organization_id), INDEX IDX_938DB7A761997596 (parent_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_group_faculty (id INT AUTO_INCREMENT NOT NULL, org_permissionset_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, org_group_id INT DEFAULT NULL, person_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, is_invisible TINYBLOB DEFAULT NULL, INDEX IDX_120C44437ABB76BC (org_permissionset_id), INDEX IDX_120C444332C8A3DE (organization_id), INDEX IDX_120C444382FB49A4 (org_group_id), INDEX IDX_120C4443217BBB47 (person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_group_students (id INT AUTO_INCREMENT NOT NULL, person_id INT DEFAULT NULL, org_group_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_A46C1796217BBB47 (person_id), INDEX IDX_A46C179682FB49A4 (org_group_id), INDEX IDX_A46C179632C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE org_permissionset (id INT AUTO_INCREMENT NOT NULL, organization_id INT DEFAULT NULL, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, modified_by INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL, deleted_by INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, permissionset_name VARCHAR(100) DEFAULT NULL, is_archived TINYBLOB DEFAULT NULL, INDEX IDX_FD169C2A32C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        //$this->addSql('CREATE TABLE person_entity (Person_id INT NOT NULL, Entity_id INT NOT NULL, INDEX IDX_928D74DEA38A39E4 (Person_id), INDEX IDX_928D74DE3D4FFFE (Entity_id), PRIMARY KEY(Person_id, Entity_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE org_group ADD CONSTRAINT FK_938DB7A732C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_group ADD CONSTRAINT FK_938DB7A761997596 FOREIGN KEY (parent_group_id) REFERENCES org_group (id)');
        $this->addSql('ALTER TABLE org_group_faculty ADD CONSTRAINT FK_120C44437ABB76BC FOREIGN KEY (org_permissionset_id) REFERENCES org_permissionset (id)');
        $this->addSql('ALTER TABLE org_group_faculty ADD CONSTRAINT FK_120C444332C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_group_faculty ADD CONSTRAINT FK_120C444382FB49A4 FOREIGN KEY (org_group_id) REFERENCES org_group (id)');
        $this->addSql('ALTER TABLE org_group_faculty ADD CONSTRAINT FK_120C4443217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_group_students ADD CONSTRAINT FK_A46C1796217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE org_group_students ADD CONSTRAINT FK_A46C179682FB49A4 FOREIGN KEY (org_group_id) REFERENCES org_group (id)');
        $this->addSql('ALTER TABLE org_group_students ADD CONSTRAINT FK_A46C179632C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE org_permissionset ADD CONSTRAINT FK_FD169C2A32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        //$this->addSql('ALTER TABLE person_entity ADD CONSTRAINT FK_928D74DEA38A39E4 FOREIGN KEY (Person_id) REFERENCES person (id)');
        //$this->addSql('ALTER TABLE person_entity ADD CONSTRAINT FK_928D74DE3D4FFFE FOREIGN KEY (Entity_id) REFERENCES entity (id)');
        $this->addSql('ALTER TABLE organization CHANGE logo_file_name logo_file_name VARCHAR(5000) DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD17632C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_34DCD17632C8A3DE ON person (organization_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE org_group DROP FOREIGN KEY FK_938DB7A761997596');
        $this->addSql('ALTER TABLE org_group_faculty DROP FOREIGN KEY FK_120C444382FB49A4');
        $this->addSql('ALTER TABLE org_group_students DROP FOREIGN KEY FK_A46C179682FB49A4');
        $this->addSql('ALTER TABLE org_group_faculty DROP FOREIGN KEY FK_120C44437ABB76BC');
        $this->addSql('DROP TABLE org_group');
        $this->addSql('DROP TABLE org_group_faculty');
        $this->addSql('DROP TABLE org_group_students');
        $this->addSql('DROP TABLE org_permissionset');
        $this->addSql('DROP TABLE person_entity');
        $this->addSql('ALTER TABLE organization CHANGE logo_file_name logo_file_name LONGBLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD17632C8A3DE');
        $this->addSql('DROP INDEX IDX_34DCD17632C8A3DE ON person');
        $this->addSql('ALTER TABLE person DROP organization_id');
    }
}
