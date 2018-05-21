<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170220093833 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE deposit ADD push_status INT DEFAULT NULL');
        $pushbacks = $this->connection->fetchAssoc('SELECT GROUP_CONCAT(id) as ids, is_pushback_accepted FROM deposit WHERE is_pushback_accepted = 1');
        $this->addSql('UPDATE deposit SET push_status = 1 WHERE id IN ('.$pushbacks['ids'].')');
        $this->addSql('ALTER TABLE deposit DROP is_pushback_accepted');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE deposit ADD is_pushback_accepted TINYINT(1) DEFAULT NULL, DROP push_status');
    }
}
