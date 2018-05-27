<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180527173711 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, lat DOUBLE PRECISION NOT NULL, lon DOUBLE PRECISION NOT NULL, location_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `match` (id INT AUTO_INCREMENT NOT NULL, sharing_user_id INT NOT NULL, renting_user_id INT NOT NULL, UNIQUE INDEX UNIQ_7A5BC505ED3A30F (sharing_user_id), UNIQUE INDEX UNIQ_7A5BC505C89F7869 (renting_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, day VARCHAR(255) NOT NULL, time INT NOT NULL, INDEX IDX_5A3811FBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, origin_id INT DEFAULT NULL, vehicle_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, time_flex INT NOT NULL, location_flex INT NOT NULL, UNIQUE INDEX UNIQ_8D93D64956A273CC (origin_id), UNIQUE INDEX UNIQ_8D93D649545317D1 (vehicle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_location (user_id INT NOT NULL, location_id INT NOT NULL, INDEX IDX_BE136DCBA76ED395 (user_id), INDEX IDX_BE136DCB64D218E (location_id), PRIMARY KEY(user_id, location_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicle (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, seats INT NOT NULL, model VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `match` ADD CONSTRAINT FK_7A5BC505ED3A30F FOREIGN KEY (sharing_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `match` ADD CONSTRAINT FK_7A5BC505C89F7869 FOREIGN KEY (renting_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64956A273CC FOREIGN KEY (origin_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE user_location ADD CONSTRAINT FK_BE136DCBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_location ADD CONSTRAINT FK_BE136DCB64D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64956A273CC');
        $this->addSql('ALTER TABLE user_location DROP FOREIGN KEY FK_BE136DCB64D218E');
        $this->addSql('ALTER TABLE `match` DROP FOREIGN KEY FK_7A5BC505ED3A30F');
        $this->addSql('ALTER TABLE `match` DROP FOREIGN KEY FK_7A5BC505C89F7869');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FBA76ED395');
        $this->addSql('ALTER TABLE user_location DROP FOREIGN KEY FK_BE136DCBA76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649545317D1');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE `match`');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_location');
        $this->addSql('DROP TABLE vehicle');
    }
}
