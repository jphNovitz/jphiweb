<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261019030900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename topic_related_blog indexes to Doctrine generated names when they still use the legacy names';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('topic_related_blog');

        if ($table->hasIndex('idx_topic_related_blog_topic')) {
            $this->addSql('ALTER TABLE topic_related_blog RENAME INDEX idx_topic_related_blog_topic TO IDX_750272D21F55203D');
        }
        if ($table->hasIndex('idx_topic_related_blog_blog')) {
            $this->addSql('ALTER TABLE topic_related_blog RENAME INDEX idx_topic_related_blog_blog TO IDX_750272D2DAE07E97');
        }
    }

    public function down(Schema $schema): void
    {
        // The legacy index names are not restored: the Doctrine generated names are the expected ones.
    }
}
