<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170404102837 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $replacedCount = 0;
        $depositsItems =  $this->connection->fetchAll("select id, items from deposit d where d.status = 'completed';");
        foreach ($depositsItems as $itemsJson) {
            $items = json_decode($itemsJson['items'], true);
            foreach ($items as $key => $item) {
                $items[$key]['icon_url'] = str_replace(['https://steamcommunity-a.akamaihd.net/economy/image/https://steamcommunity-a.akamaihd.net/economy/image/', '100x100/100x100'], ['https://steamcommunity-a.akamaihd.net/economy/image/', '100x100'], $item['icon_url'], $replacedCount);
            }
            $itemsFixed = json_encode($items);
            $this->addSql('UPDATE deposit SET items=:items WHERE id = :id', [':items' => $itemsFixed, ':id' => $itemsJson['id']]);
        }

        $this->write("$replacedCount replaced patterns");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
