<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190218132306 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, localtion VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, experience VARCHAR(255) NOT NULL, salary_max INT NOT NULL, salary_min INT NOT NULL, qualification VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, date DATETIME NOT NULL, description VARCHAR(255) NOT NULL, responsabilities VARCHAR(255) NOT NULL, education VARCHAR(255) NOT NULL, others VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, your_localtion VARCHAR(255) NOT NULL, company_name VARCHAR(255) NOT NULL, web_address VARCHAR(255) NOT NULL, campany_profile VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE feed_url CHANGE enabled enabled TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE job');
        $this->addSql('ALTER TABLE feed_url CHANGE enabled enabled TINYINT(1) DEFAULT NULL');
    }
}
