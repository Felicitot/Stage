<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251009130611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE application_role (application_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_A085E2E23E030ACD (application_id), INDEX IDX_A085E2E2D60322AC (role_id), PRIMARY KEY(application_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application_role ADD CONSTRAINT FK_A085E2E23E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_role ADD CONSTRAINT FK_A085E2E2D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application_role DROP FOREIGN KEY FK_A085E2E23E030ACD');
        $this->addSql('ALTER TABLE application_role DROP FOREIGN KEY FK_A085E2E2D60322AC');
        $this->addSql('DROP TABLE application_role');
    }
}
