<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223082056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE social_media_employee (id INT AUTO_INCREMENT NOT NULL, employee_fkid_id INT DEFAULT NULL, social_media_fkid_id INT DEFAULT NULL, username VARCHAR(150) NOT NULL, INDEX IDX_A52AB6C02E13667D (employee_fkid_id), INDEX IDX_A52AB6C0793238AF (social_media_fkid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE social_media_employee ADD CONSTRAINT FK_A52AB6C02E13667D FOREIGN KEY (employee_fkid_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE social_media_employee ADD CONSTRAINT FK_A52AB6C0793238AF FOREIGN KEY (social_media_fkid_id) REFERENCES social_media (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE social_media_employee DROP FOREIGN KEY FK_A52AB6C02E13667D');
        $this->addSql('ALTER TABLE social_media_employee DROP FOREIGN KEY FK_A52AB6C0793238AF');
        $this->addSql('DROP TABLE social_media_employee');
    }
}
