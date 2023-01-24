<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230124134645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('DROP INDEX IDX_D34A04AD12469DE2 ON product');
        $this->addSql('ALTER TABLE product ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD active TINYINT(1) NOT NULL, CHANGE price price DOUBLE PRECISION NOT NULL, CHANGE category_id id_category_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA545015 FOREIGN KEY (id_category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADA545015 ON product (id_category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP active');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA545015');
        $this->addSql('DROP INDEX IDX_D34A04ADA545015 ON product');
        $this->addSql('ALTER TABLE product DROP created_at, DROP active, CHANGE price price TINYINT(1) NOT NULL, CHANGE id_category_id category_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
    }
}
