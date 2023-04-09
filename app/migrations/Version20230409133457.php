<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230409133457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE services_doctors DROP FOREIGN KEY FK_5F8D4C49A76ED395');
        $this->addSql('ALTER TABLE services_doctors DROP FOREIGN KEY FK_5F8D4C49ED5CA9E6');
        $this->addSql('DROP TABLE services_doctors');
        $this->addSql('ALTER TABLE service ADD doctor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD287F4FB17 FOREIGN KEY (doctor_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E19D9AD287F4FB17 ON service (doctor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE services_doctors (service_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5F8D4C49A76ED395 (user_id), INDEX IDX_5F8D4C49ED5CA9E6 (service_id), PRIMARY KEY(service_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE services_doctors ADD CONSTRAINT FK_5F8D4C49A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE services_doctors ADD CONSTRAINT FK_5F8D4C49ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `service` DROP FOREIGN KEY FK_E19D9AD287F4FB17');
        $this->addSql('DROP INDEX IDX_E19D9AD287F4FB17 ON `service`');
        $this->addSql('ALTER TABLE `service` DROP doctor_id');
    }
}
