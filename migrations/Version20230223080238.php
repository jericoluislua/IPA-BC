<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223080238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //Default value of table social_media
        $this->addSql('INSERT INTO `social_media` (`platform`, `link`) VALUES("Facebook", "https://www.facebook.com/"), ("LinkedIn","https://www.linkedin.com/"), ("Instagram","https://www.instagram.com/"), ("Twitter","https://twitter.com/")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
