<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211222161050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE admitad_coupon_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE admitad_coupon (id INT NOT NULL, name VARCHAR(255) NOT NULL, promocode VARCHAR(150) NOT NULL, promocode_id INT NOT NULL, description TEXT NOT NULL, discount VARCHAR(50) NOT NULL, image VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, date_start VARCHAR(150) NOT NULL, date_end VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE admitad_coupon_id_seq CASCADE');
        $this->addSql('DROP TABLE admitad_coupon');
    }
}
