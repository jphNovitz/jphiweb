<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261017120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align project, topic and work timestamps on Gedmo TimestampableEntity (created_at/updated_at)';
    }

    public function up(Schema $schema): void
    {
        foreach (['project', 'topic'] as $table) {
            $this->addSql(sprintf('ALTER TABLE %s ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL', $table));
            $this->addSql(sprintf('UPDATE %s SET created_at = NOW(), updated_at = NOW()', $table));
            $this->addSql(sprintf('ALTER TABLE %s MODIFY created_at DATETIME NOT NULL, MODIFY updated_at DATETIME NOT NULL', $table));
        }

        $this->addSql('ALTER TABLE work CHANGE created created_at DATETIME NOT NULL, CHANGE updated updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE work CHANGE created_at created DATE NOT NULL, CHANGE updated_at updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE project DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE topic DROP created_at, DROP updated_at');
    }
}
