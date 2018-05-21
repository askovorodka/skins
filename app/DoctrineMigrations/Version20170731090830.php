<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170731090830 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE deposit ADD value_dota NUMERIC(10, 3) NOT NULL COMMENT \'deposit value of dota part\', ADD value_csgo NUMERIC(10, 3) NOT NULL COMMENT \'deposit value of csgo part\', ADD no_tax_value_dota NUMERIC(10, 3) NOT NULL COMMENT \'deposit np_tax_value of dota part\', ADD no_tax_value_csgo NUMERIC(10, 3) NOT NULL COMMENT \'deposit no_tax_value of csgo part\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
