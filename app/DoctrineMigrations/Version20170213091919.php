<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170213091919 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE integration_balance (id INT AUTO_INCREMENT NOT NULL, integration_id INT DEFAULT NULL, balance NUMERIC(10, 2) NOT NULL, currency VARCHAR(255) NOT NULL, INDEX IDX_C1D8A5A79E82DDEA (integration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE integration_debit (id INT AUTO_INCREMENT NOT NULL, integration_id INT DEFAULT NULL, currency VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, status VARCHAR(255) NOT NULL, INDEX created (created), INDEX integration_id (integration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE integration_balance ADD CONSTRAINT FK_C1D8A5A79E82DDEA FOREIGN KEY (integration_id) REFERENCES integration (id)');
        $this->addSql('ALTER TABLE integration_debit ADD CONSTRAINT FK_843C0CF59E82DDEA FOREIGN KEY (integration_id) REFERENCES integration (id)');
        $this->addSql('ALTER TABLE fos_user ADD integration_id INT DEFAULT NULL, DROP locked, DROP expired, DROP expires_at, DROP credentials_expired, DROP credentials_expire_at, CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A64799E82DDEA FOREIGN KEY (integration_id) REFERENCES integration (id)');
        $this->addSql('CREATE INDEX IDX_957A64799E82DDEA ON fos_user (integration_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE integration_balance');
        $this->addSql('DROP TABLE integration_debit');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A64799E82DDEA');
        $this->addSql('DROP INDEX IDX_957A64799E82DDEA ON fos_user');
        $this->addSql('ALTER TABLE fos_user ADD locked TINYINT(1) NOT NULL, ADD expired TINYINT(1) NOT NULL, ADD expires_at DATETIME DEFAULT NULL, ADD credentials_expired TINYINT(1) NOT NULL, ADD credentials_expire_at DATETIME DEFAULT NULL, DROP integration_id, CHANGE salt salt VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
