<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261118112323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE article CHANGE url_simplifiee url_simplifiee VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E667EF48136 ON article (url_simplifiee)');
        $this->addSql('ALTER TABLE article RENAME INDEX fk_23a0e66bcf5e72d TO IDX_23A0E66BCF5E72D');
        $this->addSql('ALTER TABLE categorie ADD couleur_hex VARCHAR(10) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_497DD6347EF48136 ON categorie (url_simplifiee)');
        $this->addSql('ALTER TABLE commande ADD date_creation DATETIME NOT NULL, ADD code_promo VARCHAR(50) DEFAULT NULL, ADD utilisateur_id INT DEFAULT NULL, DROP datetime_immutable, CHANGE reference reference VARCHAR(50) NOT NULL, CHANGE adresse_livraison adresse_livraison LONGTEXT NOT NULL, CHANGE status statut VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6EEAA67DAEA34913 ON commande (reference)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DFB88E14F ON commande (utilisateur_id)');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_3170B74B82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_3170B74B7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F7294869C');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66BCF5E72D');
        $this->addSql('DROP INDEX UNIQ_23A0E667EF48136 ON article');
        $this->addSql('ALTER TABLE article CHANGE url_simplifiee url_simplifiee VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE article RENAME INDEX idx_23a0e66bcf5e72d TO FK_23A0E66BCF5E72D');
        $this->addSql('DROP INDEX UNIQ_497DD6347EF48136 ON categorie');
        $this->addSql('ALTER TABLE categorie DROP couleur_hex');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFB88E14F');
        $this->addSql('DROP INDEX UNIQ_6EEAA67DAEA34913 ON commande');
        $this->addSql('DROP INDEX IDX_6EEAA67DFB88E14F ON commande');
        $this->addSql('ALTER TABLE commande ADD datetime_immutable VARCHAR(255) NOT NULL, DROP date_creation, DROP code_promo, DROP utilisateur_id, CHANGE reference reference VARCHAR(30) NOT NULL, CHANGE adresse_livraison adresse_livraison VARCHAR(255) NOT NULL, CHANGE statut status VARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_3170B74B82EA2E54');
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_3170B74B7294869C');
    }
}
