<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150805061044 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE org_riskval_calc_inputs ADD is_success_marker_calc_reqd ENUM(\'y\',\'n\'), ADD is_talking_point_calc_reqd ENUM(\'y\',\'n\'), ADD is_factor_calc_reqd ENUM(\'y\',\'n\')');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
        $this->addSql('ALTER TABLE org_riskval_calc_inputs DROP is_success_marker_calc_reqd, DROP is_talking_point_calc_reqd, DROP is_factor_calc_reqd');
    }
}
