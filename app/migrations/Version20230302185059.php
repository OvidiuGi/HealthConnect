<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230302185059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE days (id INT AUTO_INCREMENT NOT NULL, schedule_id INT DEFAULT NULL, date DATETIME NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, INDEX IDX_EBE4FC66A40BC2D5 (schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedules (id INT AUTO_INCREMENT NOT NULL, doctor_id INT DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, INDEX IDX_313BDC8E87F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE days ADD CONSTRAINT FK_EBE4FC66A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedules (id)');
        $this->addSql('ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8E87F4FB17 FOREIGN KEY (doctor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE building ADD name VARCHAR(256) NOT NULL, ADD postal_code VARCHAR(256) NOT NULL, ADD phone VARCHAR(256) NOT NULL, ADD email VARCHAR(256) NOT NULL, ADD description VARCHAR(256) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E16F61D4E7927C74 ON building (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE days DROP FOREIGN KEY FK_EBE4FC66A40BC2D5');
        $this->addSql('ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8E87F4FB17');
        $this->addSql('DROP TABLE days');
        $this->addSql('DROP TABLE schedules');
        $this->addSql('DROP INDEX UNIQ_E16F61D4E7927C74 ON `building`');
        $this->addSql('ALTER TABLE `building` DROP name, DROP postal_code, DROP phone, DROP email, DROP description');
    }
}
