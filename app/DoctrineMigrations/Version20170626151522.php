<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170626151522 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE integration ADD is_whitelabel TINYINT(1) DEFAULT 1, ADD logo_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE integration ADD home_url VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE integration DROP is_whitelabel, DROP logo_url');
        $this->addSql('ALTER TABLE integration DROP home_url');
    }
}
