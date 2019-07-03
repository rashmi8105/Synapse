<?php

namespace Synapse\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * ESPRJ-17664;
 * data_definition_file needed new column names as we use "(Year Specific)" at the end of the column name to signify whether or not a column is year specific
 * added ClassLevel column into template, data_definition_file and data_dump.
 * First, move all records that to appear after ClassLevel column back one in the sort_order
 * Second, add the records into all downloads
 * Also archived the ebi_metadata "Participatingin201617"
 */                                                                                                                           
class Version20180409163630 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        // * data_definition_file needed new column names as we use "(Year Specific)" at the end of the column name to signify whether or not a column is year specific
        $this->addSql("INSERT INTO `synapse`.`upload_column_header` (`upload_column_name`, `upload_column_display_name`) VALUES ('IsActive', 'IsActive (Year Specific)');            ");
        $this->addSql("INSERT INTO `synapse`.`upload_column_header` (`upload_column_name`, `upload_column_display_name`) VALUES ('Participating', 'Participating (Year Specific)');  ");
        $this->addSql("INSERT INTO `synapse`.`upload_column_header` (`upload_column_name`, `upload_column_display_name`) VALUES ('RetentionTrack', 'RetentionTrack (Year Specific)');");

        // inserting the three new columns into the data_definition_file
        $this->addSql("
                Update upload_column_header_download_map
                  set upload_column_header_id = (select id from upload_column_header where upload_column_name = 'RetentionTrack' and upload_column_display_name = 'RetentionTrack (Year Specific)')
                where
                  upload_id = (select id from upload where upload_name = 'student')
                    and
                  ebi_download_type_id = (select id from ebi_download_type where download_type = 'data_definition_file')
                    and
                  upload_column_header_id = (select id from upload_column_header where upload_column_name = 'RetentionTrack' and upload_column_display_name = 'RetentionTrack'); ");

        $this->addSql("
                  Update upload_column_header_download_map
                      set upload_column_header_id = (select id from upload_column_header where upload_column_name = 'Participating' and upload_column_display_name = 'Participating (Year Specific)')
                  where
                    upload_id = (select id from upload where upload_name = 'student')
                      and
                    ebi_download_type_id = (select id from ebi_download_type where download_type = 'data_definition_file')
                      and
                    upload_column_header_id = (select id from upload_column_header where upload_column_name = 'Participating' and upload_column_display_name = 'Participating');  ");

        $this->addSql("
                Update upload_column_header_download_map
                    set upload_column_header_id = (select id from upload_column_header where upload_column_name = 'IsActive' and upload_column_display_name = 'IsActive (Year Specific)')
                where
                  upload_id = (select id from upload where upload_name = 'student')
                    and
                  ebi_download_type_id = (select id from ebi_download_type where download_type = 'data_definition_file')
                    and
                  upload_column_header_id = (select id from upload_column_header where upload_column_name = 'IsActive' and upload_column_display_name = 'IsActive');");


        // * added ClassLevel column into template, data_definition_file and data_dump.
        // * First, move all records that to appear after ClassLevel column back one in the sort_order
        $this->addSql("UPDATE upload_ebi_metadata_column_header_download_map
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


        $this->addSql("UPDATE upload_ebi_metadata_column_header_download_map
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


    $this->addSql("UPDATE upload_ebi_metadata_column_header_download_map
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


        // * Second, add the records into all downloads
        $this->addSql("Insert into upload_ebi_metadata_column_header_download_map set

            sort_order = (SELECT * FROM (SELECT
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
                    download_type = 'data_definition_file')) as a) + 1,

        upload_id = (SELECT
            id
        FROM
            upload
        WHERE
            upload_name = 'student'),
		ebi_download_type_id = (SELECT
            id
        FROM
            ebi_download_type
        WHERE
            download_type = 'data_definition_file'),
            ebi_metadata_id = (SELECT
                    id
                FROM
                    ebi_metadata
                WHERE
                    meta_key = 'ClassLevel')
            ;");

        $this->addSql("Insert into upload_ebi_metadata_column_header_download_map set

            sort_order = (SELECT * FROM (SELECT
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
                    download_type = 'data_dump')) as a) + 1,

        upload_id = (SELECT
            id
        FROM
            upload
        WHERE
            upload_name = 'student'),
		ebi_download_type_id = (SELECT
            id
        FROM
            ebi_download_type
        WHERE
            download_type = 'data_dump'),
            ebi_metadata_id = (SELECT
                    id
                FROM
                    ebi_metadata
                WHERE
                    meta_key = 'ClassLevel')
            ;");

        $this->addSql("Insert into upload_ebi_metadata_column_header_download_map set

            sort_order = (SELECT * FROM (SELECT
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
                    download_type = 'template')) as A) + 1,

        upload_id = (SELECT
            id
        FROM
            upload
        WHERE
            upload_name = 'student'),
		ebi_download_type_id = (SELECT
            id
        FROM
            ebi_download_type
        WHERE
            download_type = 'template'),
            ebi_metadata_id = (SELECT
                    id
                FROM
                    ebi_metadata
                WHERE
                    meta_key = 'ClassLevel');");


        //  * Also archived the ebi_metadata "Participatingin201617"
        $this->addSql("UPDATE `synapse`.`ebi_metadata` SET `status`='archived' WHERE `id`='90' AND meta_key = 'Participatingin201617';");


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
