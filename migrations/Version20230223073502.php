<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223073502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //Default value of the table company
        $this->addSql('INSERT INTO `company` (`name`, `logo`, `address_fkid_id`) VALUES("Artd Webdesign GmbH", "artd-logo.svg", (SELECT `id` FROM `address` WHERE street="Waldegstrasse" AND house_number=41))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
