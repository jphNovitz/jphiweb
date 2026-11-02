<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261103130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add schema.org type to project, existing projects become CreativeWork';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE project ADD schema_type VARCHAR(30) DEFAULT 'CreativeWork' NOT NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project DROP schema_type');
    }
}
