<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231018063042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE canciones ADD autor_id INT NOT NULL');
        $this->addSql('ALTER TABLE canciones ADD CONSTRAINT FK_AEE7E88114D45BBE FOREIGN KEY (autor_id) REFERENCES autor (id)');
        $this->addSql('CREATE INDEX IDX_AEE7E88114D45BBE ON canciones (autor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE canciones DROP FOREIGN KEY FK_AEE7E88114D45BBE');
        $this->addSql('DROP INDEX IDX_AEE7E88114D45BBE ON canciones');
        $this->addSql('ALTER TABLE canciones DROP autor_id');
    }
}
