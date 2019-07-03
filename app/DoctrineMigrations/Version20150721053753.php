<?php
namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150721053753 extends AbstractMigration
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
        
        $query = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '</div>  
   </body>
</html>'
WHERE `ebi_template_key` = 'Pdf_Course_Footer_Template';
CDATA;
        $this->addSql($query);
        
        $query1 = <<<CDATA
        UPDATE `ebi_template_lang` SET `body` = '</div>  
   </body>
</html>'
WHERE `ebi_template_key` = 'Pdf_CourseStudent_Footer_Template';
CDATA;
        $this->addSql($query1);
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

        
    }
}
