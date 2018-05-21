<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170608134920 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE integration_debit ADD payment_system VARCHAR(255) DEFAULT NULL COMMENT \'Тип кошелька\', ADD payment_destination VARCHAR(255) DEFAULT NULL COMMENT \'Номер кошелька\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE integration_debit DROP payment_system, DROP payment_destination');
    }
}
