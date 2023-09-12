<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222152650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // add default administrator user jerico.lua@artd.ch
        $this->addSql('INSERT INTO `administrator` (`email`, `roles`, `password`) VALUES("jerico.lua@artd.ch", "[\"ROLE_ADMIN\"]", "$2y$13$hEB1iKxgWkqSrF9nXCxSJeKampMku6o8cknBALoeSKDsEyQaVpagy")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM `administrator` WHERE `email`="jerico.lua@artd.ch"');
    }
}
