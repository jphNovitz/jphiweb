<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261019024938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add price mention flags to work';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE work ADD show_starting_from TINYINT(1) DEFAULT 0 NOT NULL, ADD show_hosting_note TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE work DROP show_starting_from, DROP show_hosting_note');
    }
}
