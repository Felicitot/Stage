<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251009101023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demander_role ADD role_id INT NOT NULL, CHANGE status status VARCHAR(20) NOT NULL, CHANGE date_demande date_demande DATETIME NOT NULL');
        $this->addSql('ALTER TABLE demander_role ADD CONSTRAINT FK_47A06FB0D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_47A06FB0D60322AC ON demander_role (role_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demander_role DROP FOREIGN KEY FK_47A06FB0D60322AC');
        $this->addSql('DROP INDEX IDX_47A06FB0D60322AC ON demander_role');
        $this->addSql('ALTER TABLE demander_role DROP role_id, CHANGE status status VARCHAR(255) NOT NULL, CHANGE date_demande date_demande DATE NOT NULL');
    }
}
