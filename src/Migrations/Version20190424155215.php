<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190424155215 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE news_post DROP submiter');
        $this->addSql('ALTER TABLE sys_position ADD news_post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sys_position ADD CONSTRAINT FK_3EF572BD1C6D1FCA FOREIGN KEY (news_post_id) REFERENCES news_post (id)');
        $this->addSql('CREATE INDEX IDX_3EF572BD1C6D1FCA ON sys_position (news_post_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE news_post ADD submiter VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE sys_position DROP FOREIGN KEY FK_3EF572BD1C6D1FCA');
        $this->addSql('DROP INDEX IDX_3EF572BD1C6D1FCA ON sys_position');
        $this->addSql('ALTER TABLE sys_position DROP news_post_id');
    }
}
