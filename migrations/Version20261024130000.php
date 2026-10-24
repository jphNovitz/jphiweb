<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261024130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add published flag to project, existing projects stay published';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project ADD published TINYINT(1) DEFAULT 1 NOT NULL');
        // New projects are unpublished by default, like blog posts
        $this->addSql('ALTER TABLE project ALTER published DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project DROP published');
    }
}
