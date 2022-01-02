<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211221192433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE admitad_client_auth_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admitad_client_auth (id INT NOT NULL, username VARCHAR(50) NOT NULL, access_token VARCHAR(255) NOT NULL, token_type VARCHAR(15) NOT NULL, expired_time INT NOT NULL, refresh_token VARCHAR(255) NOT NULL, scopes TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN admitad_client_auth.scopes IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE admitad_client_auth_id_seq CASCADE');
        $this->addSql('DROP TABLE admitad_client_auth');
    }
}
