<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170522154756 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        //created
        $this->addSql('UPDATE `deposit` set `created` = DATE_ADD(created, INTERVAL + 3 HOUR ) WHERE created IS NOT NULL');
        //updated
        $this->addSql('UPDATE `deposit` set `updated` = DATE_ADD(updated, INTERVAL + 3 HOUR ) WHERE updated IS NOT NULL and updated <> 0');
        //created_pushback
        $this->addSql('UPDATE `deposit` set `pushback_created` = DATE_ADD(pushback_created, INTERVAL + 3 HOUR ) WHERE pushback_created IS NOT NULL and pushback_created <> 0');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        //created
        $this->addSql('UPDATE `deposit` set `created` = DATE_ADD(created, INTERVAL - 3 HOUR ) WHERE created IS NOT NULL');
        //updated
        $this->addSql('UPDATE `deposit` set `updated` = DATE_ADD(updated, INTERVAL - 3 HOUR ) WHERE updated IS NOT NULL and updated <> 0');
        //created_pushback
        $this->addSql('UPDATE `deposit` set `pushback_created` = DATE_ADD(pushback_created, INTERVAL - 3 HOUR ) WHERE pushback_created IS NOT NULL and pushback_created <> 0');
    }
}
