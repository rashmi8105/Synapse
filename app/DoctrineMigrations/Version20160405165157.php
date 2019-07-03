<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160405165157 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        //
        /**
         * This query will take all JSONS with a valid cohort_ids in org_search
         * and then replace them with the new updated JSON. This will ignore
         * JSON without "cohort_ids" in them and JSON that has "cohort_ids":""
         * as those queries will not change.
         */
        $this->addSQL(<<<MYSQLUPDATE
UPDATE org_search
        INNER JOIN
    (SELECT
        org_search_id AS id,
        CONCAT(`JSON_start`, `cohort_filter_beginning`, `org_academic_year_id`, `cohort_filter_middle`, `cohorts`, `cohort_filter_end`, `JSON_end`) AS `returnJSON`
    FROM
        (SELECT
            org_search_id,
            `JSON_start`,
            '"cohort_filter":{"org_academic_year":' AS 'cohort_filter_beginning',
            ', "cohorts":[' AS 'cohort_filter_middle',
            SUBSTRING(`JSON_end`, 1, LOCATE('"', `JSON_end`) - 1) AS 'cohorts',
            ']}' AS 'cohort_filter_end',
            SUBSTRING(`JSON_end`, LOCATE('"', `JSON_end`) + 1) AS 'JSON_end',
            org_academic_year_id
    FROM
        (SELECT
            os.id AS org_search_id,
            SUBSTRING(`json`, 1, (LOCATE('"cohort_ids"', `json`) - 1)) AS `JSON_start`,
            SUBSTRING(`json`, (LOCATE('"cohort_ids"', `json`)), 14) AS `cohort_ids`,
            SUBSTRING(`json`, (LOCATE('"cohort_ids"', `json`) + 14)) AS `JSON_end`,
            oay.id AS org_academic_year_id,
            `json`
    FROM
        org_search os
    INNER JOIN org_academic_year oay ON oay.organization_id = os.organization_id
    WHERE
        `json` LIKE '%"cohort_ids":%'
            AND `json` NOT LIKE '%"cohort_ids":""%'
            AND `json` NOT LIKE '%cohort_filter%'
            AND oay.start_date < DATE(NOW())
            AND oay.end_date > DATE(NOW())) AS search_academic_year) AS returnJson) AS returnJson ON returnJson.id = org_search.id
SET
    org_search.json = returnJson.returnJson
WHERE
    org_search.id = returnJson.id
MYSQLUPDATE
    );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
