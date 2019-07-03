<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151018083942 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        
        $this->addSql("update activity_log as al
inner join(
SELECT distinct(a.appointments_id) ,a.person_id_faculty as al_faculty,a.person_id_student as al_student,ars.person_id_faculty,ars.person_id_student
from
    activity_log as a
LEFT JOIN 
appointment_recepient_and_status as ars
On a.appointments_id = ars.appointments_id
where
		activity_type = 'A'
        and a.person_id_faculty = a.person_id_student
) as alu On al.appointments_id = alu.appointments_id
set  al.person_id_faculty = alu.person_id_faculty");
        
    }
    
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    
    }
}
