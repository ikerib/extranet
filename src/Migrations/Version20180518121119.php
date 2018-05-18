<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180518121119 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE karpeta (id INT AUTO_INCREMENT NOT NULL, foldername VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, taldea_id INT NOT NULL, karpeta_id INT NOT NULL, can_write TINYINT(1) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_E04992AAC33C39F6 (taldea_id), INDEX IDX_E04992AACD791072 (karpeta_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taldea (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAC33C39F6 FOREIGN KEY (taldea_id) REFERENCES taldea (id)');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AACD791072 FOREIGN KEY (karpeta_id) REFERENCES karpeta (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AACD791072');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAC33C39F6');
        $this->addSql('DROP TABLE karpeta');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE taldea');
    }
}
