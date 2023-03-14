<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309211605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointments CHANGE date date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE time_interval time_interval VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE building CHANGE start_hour start_hour DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE end_hour end_hour DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE days CHANGE date date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE start_time start_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE end_time end_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE schedules CHANGE start_date start_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE end_date end_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `building` CHANGE start_hour start_hour DATETIME DEFAULT NULL, CHANGE end_hour end_hour DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE days CHANGE date date DATETIME NOT NULL, CHANGE start_time start_time DATETIME NOT NULL, CHANGE end_time end_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE appointments CHANGE time_interval time_interval VARCHAR(255) NOT NULL, CHANGE date date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE schedules CHANGE start_date start_date DATETIME NOT NULL, CHANGE end_date end_date DATETIME NOT NULL');
    }
}
