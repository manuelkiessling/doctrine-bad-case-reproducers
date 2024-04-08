<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240408203719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bad_car (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bad_tire (id INT UNSIGNED AUTO_INCREMENT NOT NULL, bad_car_id INT UNSIGNED NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_7081636F8EB849FF (bad_car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bad_tire ADD CONSTRAINT FK_7081636F8EB849FF FOREIGN KEY (bad_car_id) REFERENCES bad_car (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bad_tire DROP FOREIGN KEY FK_7081636F8EB849FF');
        $this->addSql('DROP TABLE bad_car');
        $this->addSql('DROP TABLE bad_tire');
    }
}
