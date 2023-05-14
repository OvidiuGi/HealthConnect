<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230514205344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494D2A7E12');
        $this->addSql('CREATE TABLE `hospital` (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(256) NOT NULL, city VARCHAR(256) NOT NULL, start_hour DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_hour DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(256) NOT NULL, postal_code VARCHAR(256) NOT NULL, phone VARCHAR(256) NOT NULL, email VARCHAR(256) NOT NULL, description VARCHAR(256) NOT NULL, UNIQUE INDEX UNIQ_4282C85BD4E6F81 (address), UNIQUE INDEX UNIQ_4282C85BE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE building');
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727A87F4FB17');
        $this->addSql('DROP INDEX IDX_6A41727A87F4FB17 ON appointments');
        $this->addSql('ALTER TABLE appointments CHANGE doctor_id medic_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A409615FE FOREIGN KEY (medic_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_6A41727A409615FE ON appointments (medic_id)');
        $this->addSql('ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8E87F4FB17');
        $this->addSql('DROP INDEX IDX_313BDC8E87F4FB17 ON schedules');
        $this->addSql('ALTER TABLE schedules CHANGE doctor_id medic_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8E409615FE FOREIGN KEY (medic_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_313BDC8E409615FE ON schedules (medic_id)');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD287F4FB17');
        $this->addSql('DROP INDEX IDX_E19D9AD287F4FB17 ON service');
        $this->addSql('ALTER TABLE service CHANGE doctor_id medic_id INT NOT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2409615FE FOREIGN KEY (medic_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E19D9AD2409615FE ON service (medic_id)');
        $this->addSql('DROP INDEX IDX_8D93D6494D2A7E12 ON user');
        $this->addSql('ALTER TABLE user CHANGE building_id hospital_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64963DBB69 FOREIGN KEY (hospital_id) REFERENCES `hospital` (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8D93D64963DBB69 ON user (hospital_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64963DBB69');
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(256) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, city VARCHAR(256) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, start_hour DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_hour DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(256) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, postal_code VARCHAR(256) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phone VARCHAR(256) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(256) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(256) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_E16F61D4E7927C74 (email), UNIQUE INDEX UNIQ_E16F61D4D4E6F81 (address), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE `hospital`');
        $this->addSql('ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8E409615FE');
        $this->addSql('DROP INDEX IDX_313BDC8E409615FE ON schedules');
        $this->addSql('ALTER TABLE schedules CHANGE medic_id doctor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8E87F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_313BDC8E87F4FB17 ON schedules (doctor_id)');
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727A409615FE');
        $this->addSql('DROP INDEX IDX_6A41727A409615FE ON appointments');
        $this->addSql('ALTER TABLE appointments CHANGE medic_id doctor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A87F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_6A41727A87F4FB17 ON appointments (doctor_id)');
        $this->addSql('DROP INDEX IDX_8D93D64963DBB69 ON `user`');
        $this->addSql('ALTER TABLE `user` CHANGE hospital_id building_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6494D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8D93D6494D2A7E12 ON `user` (building_id)');
        $this->addSql('ALTER TABLE `service` DROP FOREIGN KEY FK_E19D9AD2409615FE');
        $this->addSql('DROP INDEX IDX_E19D9AD2409615FE ON `service`');
        $this->addSql('ALTER TABLE `service` CHANGE medic_id doctor_id INT NOT NULL');
        $this->addSql('ALTER TABLE `service` ADD CONSTRAINT FK_E19D9AD287F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E19D9AD287F4FB17 ON `service` (doctor_id)');
    }
}
