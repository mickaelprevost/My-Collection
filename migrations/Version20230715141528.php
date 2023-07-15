<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230715141528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, universe_id_id INT NOT NULL, user_id_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, poster VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_39986E43BE7ABE16 (universe_id_id), INDEX IDX_39986E439D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE album_like (id INT AUTO_INCREMENT NOT NULL, album_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_C868F6F31137ABCF (album_id), INDEX IDX_C868F6F3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collectible (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, release_date DATETIME DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, poster VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1D1F976E9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collectible_album (collectible_id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_FEFC601F9700322F (collectible_id), INDEX IDX_FEFC601F1137ABCF (album_id), PRIMARY KEY(collectible_id, album_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collectible_category (collectible_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_B7B6461D9700322F (collectible_id), INDEX IDX_B7B6461D12469DE2 (category_id), PRIMARY KEY(collectible_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, recipient_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, is_read TINYINT(1) NOT NULL, updated_at DATETIME DEFAULT NULL, active TINYINT(1) NOT NULL, active2 TINYINT(1) NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FE92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, album_id INT DEFAULT NULL, comment LONGTEXT DEFAULT NULL, rating INT DEFAULT NULL, published_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_794381C6A76ED395 (user_id), INDEX IDX_794381C61137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review_like (id INT AUTO_INCREMENT NOT NULL, review_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_4ED70DAB3E2E969B (review_id), INDEX IDX_4ED70DABA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE universe (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, summary LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43BE7ABE16 FOREIGN KEY (universe_id_id) REFERENCES universe (id)');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E439D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE album_like ADD CONSTRAINT FK_C868F6F31137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE album_like ADD CONSTRAINT FK_C868F6F3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE collectible ADD CONSTRAINT FK_1D1F976E9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE collectible_album ADD CONSTRAINT FK_FEFC601F9700322F FOREIGN KEY (collectible_id) REFERENCES collectible (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collectible_album ADD CONSTRAINT FK_FEFC601F1137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collectible_category ADD CONSTRAINT FK_B7B6461D9700322F FOREIGN KEY (collectible_id) REFERENCES collectible (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collectible_category ADD CONSTRAINT FK_B7B6461D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C61137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review_like ADD CONSTRAINT FK_4ED70DAB3E2E969B FOREIGN KEY (review_id) REFERENCES review (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review_like ADD CONSTRAINT FK_4ED70DABA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43BE7ABE16');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E439D86650F');
        $this->addSql('ALTER TABLE album_like DROP FOREIGN KEY FK_C868F6F31137ABCF');
        $this->addSql('ALTER TABLE album_like DROP FOREIGN KEY FK_C868F6F3A76ED395');
        $this->addSql('ALTER TABLE collectible DROP FOREIGN KEY FK_1D1F976E9D86650F');
        $this->addSql('ALTER TABLE collectible_album DROP FOREIGN KEY FK_FEFC601F9700322F');
        $this->addSql('ALTER TABLE collectible_album DROP FOREIGN KEY FK_FEFC601F1137ABCF');
        $this->addSql('ALTER TABLE collectible_category DROP FOREIGN KEY FK_B7B6461D9700322F');
        $this->addSql('ALTER TABLE collectible_category DROP FOREIGN KEY FK_B7B6461D12469DE2');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C61137ABCF');
        $this->addSql('ALTER TABLE review_like DROP FOREIGN KEY FK_4ED70DAB3E2E969B');
        $this->addSql('ALTER TABLE review_like DROP FOREIGN KEY FK_4ED70DABA76ED395');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE album_like');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE collectible');
        $this->addSql('DROP TABLE collectible_album');
        $this->addSql('DROP TABLE collectible_category');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE review_like');
        $this->addSql('DROP TABLE universe');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
