<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Moves system data into a more logical place for the Our Students Report.
 * Factors are already using the range_min and range_max columns of the report_element_buckets table,
 * and there's no reason to have to join on an extra table (report_bucket_range) for questions.
 */
class Version20160918111928 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 6, range_max = 7
                        WHERE element_id = 160 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 6, range_max = 7
                        WHERE element_id = 161 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 6, range_max = 7
                        WHERE element_id = 162 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 2, range_max = 6
                        WHERE element_id = 163 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 1, range_max = 1
                        WHERE element_id = 164 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 9, range_max = 9
                        WHERE element_id = 165 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 5, range_max = 8
                        WHERE element_id = 166 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 3, range_max = 9
                        WHERE element_id = 167 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 2, range_max = 4
                        WHERE element_id = 168 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 0
                        WHERE element_id = 169 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 3, range_max = 3
                        WHERE element_id = 170 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 4, range_max = 5
                        WHERE element_id = 171 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 0
                        WHERE element_id = 172 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 6, range_max = 7
                        WHERE element_id = 173 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 6, range_max = 7
                        WHERE element_id = 174 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 6, range_max = 7
                        WHERE element_id = 175 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 6, range_max = 7
                        WHERE element_id = 176 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 6, range_max = 7
                        WHERE element_id = 177 AND bucket_name = 'Numerator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 1, range_max = 1
                        WHERE element_id = 178 AND bucket_name = 'Numerator';");


        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 1, range_max = 7
                        WHERE element_id = 160 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 1, range_max = 7
                        WHERE element_id = 161 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 1, range_max = 7
                        WHERE element_id = 162 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 6
                        WHERE element_id = 163 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 6
                        WHERE element_id = 164 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 9
                        WHERE element_id = 165 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 9
                        WHERE element_id = 166 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 9
                        WHERE element_id = 167 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 4
                        WHERE element_id = 168 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 4
                        WHERE element_id = 169 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 5
                        WHERE element_id = 170 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 5
                        WHERE element_id = 171 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 3
                        WHERE element_id = 172 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 1, range_max = 7
                        WHERE element_id = 173 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 1, range_max = 7
                        WHERE element_id = 174 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 1, range_max = 7
                        WHERE element_id = 175 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 1, range_max = 7
                        WHERE element_id = 176 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 1, range_max = 7
                        WHERE element_id = 177 AND bucket_name = 'Denominator';");

        $this->addSql("UPDATE report_element_buckets
                        SET range_min = 0, range_max = 1
                        WHERE element_id = 178 AND bucket_name = 'Denominator';");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on "mysql".');
    }
}
