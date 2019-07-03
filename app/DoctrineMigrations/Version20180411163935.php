<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-17688; update for upload_column_header_download_map that was missed in migration script Version20180409163630
 */
class Version20180411163935 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE upload_column_header_download_map
SET
    sort_order = sort_order + 1
WHERE
    sort_order > (select * From (SELECT
            sort_order
        FROM
            upload_ebi_metadata_column_header_download_map
        WHERE
            ebi_metadata_id = (SELECT
                    id
                FROM
                    ebi_metadata
                WHERE
                    meta_key = 'StartYearClassLevel')
                AND upload_id = (SELECT
                    id
                FROM
                    upload
                WHERE
                    upload_name = 'student')
                AND ebi_download_type_id = (SELECT
                    id
                FROM
                    ebi_download_type
                WHERE
                    download_type = 'data_definition_file')) as a)
        AND upload_id = (SELECT
            id
        FROM
            upload
        WHERE
            upload_name = 'student')
        AND ebi_download_type_id = (SELECT
            id
        FROM
            ebi_download_type
        WHERE
            download_type = 'data_definition_file');");

        $this->addSql("UPDATE upload_column_header_download_map
SET
    sort_order = sort_order + 1
WHERE
    sort_order > (SELECT * From (SELECT
            sort_order
        FROM
            upload_ebi_metadata_column_header_download_map
        WHERE
            ebi_metadata_id = (SELECT
                    id
                FROM
                    ebi_metadata
                WHERE
                    meta_key = 'StartYearClassLevel')
                AND upload_id = (SELECT
                    id
                FROM
                    upload
                WHERE
                    upload_name = 'student')
                AND ebi_download_type_id = (SELECT
                    id
                FROM
                    ebi_download_type
                WHERE
                    download_type = 'data_dump')) As a)
        AND upload_id = (SELECT
            id
        FROM
            upload
        WHERE
            upload_name = 'student')
        AND ebi_download_type_id = (SELECT
            id
        FROM
            ebi_download_type
        WHERE
            download_type = 'data_dump');");


        $this->addSql("UPDATE upload_column_header_download_map
SET
    sort_order = sort_order + 1
WHERE
    sort_order > (SELECT * FROM (SELECT
            sort_order
        FROM
            upload_ebi_metadata_column_header_download_map
        WHERE
            ebi_metadata_id = (SELECT
                    id
                FROM
                    ebi_metadata
                WHERE
                    meta_key = 'StartYearClassLevel')
                AND upload_id = (SELECT
                    id
                FROM
                    upload
                WHERE
                    upload_name = 'student')
                AND ebi_download_type_id = (SELECT
                    id
                FROM
                    ebi_download_type
                WHERE
                    download_type = 'template')) as a)
        AND upload_id = (SELECT
            id
        FROM
            upload
        WHERE
            upload_name = 'student')
        AND ebi_download_type_id = (SELECT
            id
        FROM
            ebi_download_type
        WHERE
            download_type = 'template');");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
