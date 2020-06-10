<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190419173812 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE payment_for_jobs (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price INT NOT NULL, aux VARCHAR(255) DEFAULT NULL, anouncements_number_max INT NOT NULL, visible_days INT NOT NULL, days_importants INT NOT NULL, cv_number_max INT NOT NULL, evaluations_psicological TINYINT(1) NOT NULL, selection TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment DROP cv_number_max, DROP evaluations_psicological, DROP selection, DROP admin_payment');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE payment_for_jobs');
        $this->addSql('ALTER TABLE payment ADD cv_number_max INT NOT NULL, ADD evaluations_psicological TINYINT(1) NOT NULL, ADD selection TINYINT(1) NOT NULL, ADD admin_payment TINYINT(1) NOT NULL');
    }
}
