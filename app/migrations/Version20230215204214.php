<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230215204214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointment (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, service_id INT DEFAULT NULL, doctor_id INT DEFAULT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, INDEX IDX_6A41727A9395C3F3 (customer_id), INDEX IDX_6A41727AED5CA9E6 (service_id), INDEX IDX_6A41727A87F4FB17 (doctor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `building` (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(256) NOT NULL, city VARCHAR(256) NOT NULL, start_hour DATETIME DEFAULT NULL, end_hour DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_E16F61D4D4E6F81 (address), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `service` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(256) NOT NULL, price VARCHAR(256) NOT NULL, description VARCHAR(256) NOT NULL, duration INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE services_doctors (service_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5F8D4C49ED5CA9E6 (service_id), INDEX IDX_5F8D4C49A76ED395 (user_id), PRIMARY KEY(service_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, building_id INT DEFAULT NULL, first_name VARCHAR(256) NOT NULL, last_name VARCHAR(256) NOT NULL, email VARCHAR(256) NOT NULL, password VARCHAR(256) NOT NULL, telephone_nr VARCHAR(256) NOT NULL, cnp VARCHAR(256) NOT NULL, role VARCHAR(256) NOT NULL, specialization VARCHAR(256) NOT NULL, experience INT NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D64935C246D5 (password), UNIQUE INDEX UNIQ_8D93D64945F1B9BF (telephone_nr), UNIQUE INDEX UNIQ_8D93D6491EAB9B7E (cnp), INDEX IDX_8D93D6494D2A7E12 (building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_6A41727A9395C3F3 FOREIGN KEY (customer_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_6A41727AED5CA9E6 FOREIGN KEY (service_id) REFERENCES `service` (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_6A41727A87F4FB17 FOREIGN KEY (doctor_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE services_doctors ADD CONSTRAINT FK_5F8D4C49ED5CA9E6 FOREIGN KEY (service_id) REFERENCES `service` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE services_doctors ADD CONSTRAINT FK_5F8D4C49A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6494D2A7E12 FOREIGN KEY (building_id) REFERENCES `building` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_6A41727A9395C3F3');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_6A41727AED5CA9E6');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_6A41727A87F4FB17');
        $this->addSql('ALTER TABLE services_doctors DROP FOREIGN KEY FK_5F8D4C49ED5CA9E6');
        $this->addSql('ALTER TABLE services_doctors DROP FOREIGN KEY FK_5F8D4C49A76ED395');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6494D2A7E12');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE `building`');
        $this->addSql('DROP TABLE `service`');
        $this->addSql('DROP TABLE services_doctors');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
