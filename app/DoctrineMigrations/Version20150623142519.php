<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150623142519 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
		
        $this->addSql('ALTER TABLE Appointments ADD source enum(\'S\', \'G\', \'E\') DEFAULT \'S\', ADD exchange_appointment_id VARCHAR(100) DEFAULT NULL, ADD exchange_master_appointment_id VARCHAR(100) DEFAULT NULL, ADD google_appointment_id VARCHAR(100) DEFAULT NULL, ADD google_master_appointment_id VARCHAR(100) DEFAULT NULL');
		
        $this->addSql('ALTER TABLE office_hours ADD source enum(\'S\', \'G\', \'E\') DEFAULT \'S\', ADD exchange_appointment_id VARCHAR(100) DEFAULT NULL, ADD google_appointment_id VARCHAR(100) DEFAULT NULL, ADD last_synced DATETIME DEFAULT NULL');

        $this->addSql('CREATE INDEX exchange_appointment_id_idx ON office_hours (exchange_appointment_id, last_synced)');
        $this->addSql('CREATE INDEX google_appointment_id_idx ON office_hours (google_appointment_id, last_synced)');

        $this->addSql('ALTER TABLE office_hours_series ADD exchange_master_appointment_id VARCHAR(100) DEFAULT NULL, ADD google_master_appointment_id VARCHAR(100) DEFAULT NULL, ADD last_synced DATETIME DEFAULT NULL');

        $this->addSql('CREATE INDEX exchange_master_appointment_id_idx ON office_hours_series (exchange_master_appointment_id, last_synced)');
        $this->addSql('CREATE INDEX google_master_appointment_id_idx ON office_hours_series (google_master_appointment_id, last_synced)');
		        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
		
        $this->addSql('ALTER TABLE Appointments DROP source, DROP exchange_appointment_id, DROP exchange_master_appointment_id, DROP google_appointment_id, DROP google_master_appointment_id');
		
        $this->addSql('DROP INDEX exchange_appointment_id_idx ON office_hours');
        $this->addSql('DROP INDEX google_appointment_id_idx ON office_hours');

        $this->addSql('ALTER TABLE office_hours DROP source, DROP exchange_appointment_id, DROP google_appointment_id, DROP last_synced');

        $this->addSql('DROP INDEX exchange_master_appointment_id_idx ON office_hours_series');
        $this->addSql('DROP INDEX google_master_appointment_id_idx ON office_hours_series');

        $this->addSql('ALTER TABLE office_hours_series DROP exchange_master_appointment_id, DROP google_master_appointment_id, DROP last_synced');	

    }
}
