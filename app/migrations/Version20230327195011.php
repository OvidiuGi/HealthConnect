<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230327195011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727A87F4FB17');
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727A9395C3F3');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A87F4FB17 FOREIGN KEY (doctor_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A9395C3F3 FOREIGN KEY (customer_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8E87F4FB17');
        $this->addSql('ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8E87F4FB17 FOREIGN KEY (doctor_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494D2A7E12');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494D2A7E12 FOREIGN KEY (building_id) REFERENCES `building` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727A9395C3F3');
        $this->addSql('ALTER TABLE appointments DROP FOREIGN KEY FK_6A41727A87F4FB17');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A9395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A87F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE schedules DROP FOREIGN KEY FK_313BDC8E87F4FB17');
        $this->addSql('ALTER TABLE schedules ADD CONSTRAINT FK_313BDC8E87F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6494D2A7E12');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6494D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
