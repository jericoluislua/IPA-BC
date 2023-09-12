<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223081542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE employee_role (id INT AUTO_INCREMENT NOT NULL, employee_fkid_id INT NOT NULL, company_fkid_id INT NOT NULL, role VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_E2B0C02D2E13667D (employee_fkid_id), INDEX IDX_E2B0C02D8E0D2AA5 (company_fkid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employee_role ADD CONSTRAINT FK_E2B0C02D2E13667D FOREIGN KEY (employee_fkid_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE employee_role ADD CONSTRAINT FK_E2B0C02D8E0D2AA5 FOREIGN KEY (company_fkid_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employee_role DROP FOREIGN KEY FK_E2B0C02D2E13667D');
        $this->addSql('ALTER TABLE employee_role DROP FOREIGN KEY FK_E2B0C02D8E0D2AA5');
        $this->addSql('DROP TABLE employee_role');
    }
}
