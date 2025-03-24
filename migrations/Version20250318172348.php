<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250318172348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Инициализация таблиц offer и geo.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE geo (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(20) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, approval_time INT NOT NULL, site_url VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, rating DOUBLE PRECISION NOT NULL, offer_currency_name VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offer_geo (offer_id INT NOT NULL, geo_id INT NOT NULL, INDEX IDX_5B6B860453C674EE (offer_id), INDEX IDX_5B6B8604FA49D0B (geo_id), PRIMARY KEY(offer_id, geo_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offer_geo ADD CONSTRAINT FK_5B6B860453C674EE FOREIGN KEY (offer_id) REFERENCES offer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offer_geo ADD CONSTRAINT FK_5B6B8604FA49D0B FOREIGN KEY (geo_id) REFERENCES geo (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX offer_name_idx ON offer (name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE offer_geo DROP FOREIGN KEY FK_5B6B860453C674EE');
        $this->addSql('ALTER TABLE offer_geo DROP FOREIGN KEY FK_5B6B8604FA49D0B');
        $this->addSql('DROP TABLE geo');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE offer_geo');
    }
}
