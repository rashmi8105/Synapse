<?php

    namespace Synapse\Migrations;

    use Doctrine\DBAL\Migrations\AbstractMigration;
    use Doctrine\DBAL\Schema\Schema;

    /**
     * Migration script to add column "short_name" in table "factor_lang". The text in this new column will be used by the compare report to display factor description
     *
     * Ticket: https://jira-mnv.atlassian.net/browse/ESPRJ-14324
     */
    class Version20170413101001 extends AbstractMigration
    {
        /**
         * @param Schema $schema
         */
        public function up(Schema $schema)
        {
            $table = $schema->getTable('factor_lang');
            $columnExists = $table->hasColumn('short_name');

            if (!$columnExists) {
                $this->addSql('ALTER TABLE factor_lang ADD COLUMN short_name varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL AFTER name');
            }

            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Commitment to the Institution' WHERE name ='Commitment to the Institution'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Communication Skills' WHERE name ='Self-Assessment: Communication Skills'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Analytical Skills' WHERE name ='Self-Assessment: Analytical Skills'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Self-Discipline' WHERE name ='Self-Assessment: Self-Discipline'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Time Management' WHERE name ='Self-Assessment: Time Management'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Financial Means' WHERE name ='Financial Means'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Basic Academic Behaviors' WHERE name ='Basic Academic Behaviors'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Adv Academic Behaviors' WHERE name ='Advanced Academic Behaviors'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Academic Self-Efficacy' WHERE name ='Academic Self-Efficacy'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Academic Resiliency' WHERE name ='Academic Resiliency'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Peer Connections' WHERE name ='Peer Connections'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Homesick: Separation' WHERE name ='Homesickness: Separation'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Homesick: Distressed' WHERE name ='Homesickness: Distressed'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Academic Integration' WHERE name ='Academic Integration'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Social Integration' WHERE name ='Social Integration'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Satisfaction with Institution' WHERE name ='Satisfaction with Institution'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='On-Campus: Social Aspects' WHERE name ='On-Campus Living: Social Aspects'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='On-Campus: Environment' WHERE name ='On-Campus Living: Environment'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='On-Campus: Roommate' WHERE name ='On-Campus Living: Roommate Relationship'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Off-Campus: Environment' WHERE name ='Off-Campus Living: Environment'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Test Anxiety' WHERE name ='Test Anxiety'");
            $this->addSQL("UPDATE synapse.factor_lang SET short_name ='Adv Study Skills' WHERE name ='Advanced Study Skills'");
        }

        /**
         * @param Schema $schema
         */
        public function down(Schema $schema)
        {
            $table = $schema->getTable('factor_lang');
            $columnExists = $table->hasColumn('short_name');

            if ($columnExists) {
                $this->addSql('ALTER TABLE factor_lang DROP COLUMN short_name');
            }

        }
    }
