<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180507064903 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE karpeta (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE karpeta_taldea (karpeta_id INT NOT NULL, taldea_id INT NOT NULL, INDEX IDX_8CC51397CD791072 (karpeta_id), INDEX IDX_8CC51397C33C39F6 (taldea_id), PRIMARY KEY(karpeta_id, taldea_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taldea (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE karpeta_taldea ADD CONSTRAINT FK_8CC51397CD791072 FOREIGN KEY (karpeta_id) REFERENCES karpeta (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE karpeta_taldea ADD CONSTRAINT FK_8CC51397C33C39F6 FOREIGN KEY (taldea_id) REFERENCES taldea (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE karpeta_taldea DROP FOREIGN KEY FK_8CC51397CD791072');
        $this->addSql('ALTER TABLE karpeta_taldea DROP FOREIGN KEY FK_8CC51397C33C39F6');
        $this->addSql('DROP TABLE karpeta');
        $this->addSql('DROP TABLE karpeta_taldea');
        $this->addSql('DROP TABLE taldea');
    }
}
