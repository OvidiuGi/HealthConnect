<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230409210853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment ADD start_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD end_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP time_interval');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD25E237E06 ON service (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_E19D9AD25E237E06 ON `service`');
        $this->addSql('ALTER TABLE appointment ADD time_interval VARCHAR(255) DEFAULT NULL, DROP start_time, DROP end_time');
    }
}
