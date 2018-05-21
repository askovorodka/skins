<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170530100211 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE integration_reports (id INT AUTO_INCREMENT NOT NULL, integration_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created DATETIME NOT NULL COMMENT \'date of create record\', file_type VARCHAR(255) DEFAULT NULL, file VARCHAR(255) NOT NULL COMMENT \'report file\', file_size INT DEFAULT NULL COMMENT \'size of file\', filter_params LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', INDEX user_id (user_id), INDEX integration_id (integration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE integration_reports ADD CONSTRAINT FK_9C331D1C9E82DDEA FOREIGN KEY (integration_id) REFERENCES integration (id)');
        $this->addSql('ALTER TABLE integration_reports ADD CONSTRAINT FK_9C331D1CA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
