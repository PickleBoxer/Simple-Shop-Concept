<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230126123449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Existing entries in the conference database will get a null value when the migration is ran
        // $this->addSql('ALTER TABLE category ADD slug VARCHAR(255) NOT NULL');
        // add the column and allow it to be null
        $this->addSql('ALTER TABLE category ADD slug VARCHAR(255)');
        // set the slug to a not null value
        $this->addSql("UPDATE category SET slug=CONCAT(id , '-', LOWER(name))");
        // change the slug column to not allow null
        $this->addSql('ALTER TABLE category MODIFY slug VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP slug');
    }
}
