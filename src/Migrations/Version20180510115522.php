<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180510115522 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE taldea_karpeta (taldea_id INT NOT NULL, karpeta_id INT NOT NULL, INDEX IDX_51560AECC33C39F6 (taldea_id), INDEX IDX_51560AECCD791072 (karpeta_id), PRIMARY KEY(taldea_id, karpeta_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE taldea_karpeta ADD CONSTRAINT FK_51560AECC33C39F6 FOREIGN KEY (taldea_id) REFERENCES taldea (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE taldea_karpeta ADD CONSTRAINT FK_51560AECCD791072 FOREIGN KEY (karpeta_id) REFERENCES karpeta (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE karpeta_taldea');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE karpeta_taldea (karpeta_id INT NOT NULL, taldea_id INT NOT NULL, INDEX IDX_8CC51397CD791072 (karpeta_id), INDEX IDX_8CC51397C33C39F6 (taldea_id), PRIMARY KEY(karpeta_id, taldea_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE karpeta_taldea ADD CONSTRAINT FK_8CC51397C33C39F6 FOREIGN KEY (taldea_id) REFERENCES taldea (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE karpeta_taldea ADD CONSTRAINT FK_8CC51397CD791072 FOREIGN KEY (karpeta_id) REFERENCES karpeta (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE taldea_karpeta');
    }
}
