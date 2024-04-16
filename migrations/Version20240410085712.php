<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240410085712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bad_house (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bad_window (id INT UNSIGNED AUTO_INCREMENT NOT NULL, bad_house_id INT UNSIGNED NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_C69A46BD5594CDD1 (bad_house_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bad_window ADD CONSTRAINT FK_C69A46BD5594CDD1 FOREIGN KEY (bad_house_id) REFERENCES bad_house (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bad_window DROP FOREIGN KEY FK_C69A46BD5594CDD1');
        $this->addSql('DROP TABLE bad_house');
        $this->addSql('DROP TABLE bad_window');
    }
}
