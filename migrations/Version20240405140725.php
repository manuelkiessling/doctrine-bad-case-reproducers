<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240405140725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tire DROP FOREIGN KEY FK_A2CE96DBC3C6F69F');
        $this->addSql('ALTER TABLE tire ADD CONSTRAINT FK_A2CE96DBC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tire DROP FOREIGN KEY FK_A2CE96DBC3C6F69F');
        $this->addSql('ALTER TABLE tire ADD CONSTRAINT FK_A2CE96DBC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
    }
}
