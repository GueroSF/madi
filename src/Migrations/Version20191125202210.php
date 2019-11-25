<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191125202210 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, INDEX IDX_5A8A6C8DF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_info (id INT AUTO_INCREMENT NOT NULL, reader_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_info_user (post_info_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_75967A30606376CB (post_info_id), INDEX IDX_75967A30A76ED395 (user_id), PRIMARY KEY(post_info_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_info_post (post_info_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_A28FC0F4606376CB (post_info_id), INDEX IDX_A28FC0F44B89032C (post_id), PRIMARY KEY(post_info_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post_info_user ADD CONSTRAINT FK_75967A30606376CB FOREIGN KEY (post_info_id) REFERENCES post_info (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_info_user ADD CONSTRAINT FK_75967A30A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_info_post ADD CONSTRAINT FK_A28FC0F4606376CB FOREIGN KEY (post_info_id) REFERENCES post_info (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_info_post ADD CONSTRAINT FK_A28FC0F44B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE post_info_post DROP FOREIGN KEY FK_A28FC0F44B89032C');
        $this->addSql('ALTER TABLE post_info_user DROP FOREIGN KEY FK_75967A30606376CB');
        $this->addSql('ALTER TABLE post_info_post DROP FOREIGN KEY FK_A28FC0F4606376CB');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE post_info_user DROP FOREIGN KEY FK_75967A30A76ED395');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_info');
        $this->addSql('DROP TABLE post_info_user');
        $this->addSql('DROP TABLE post_info_post');
        $this->addSql('DROP TABLE user');
    }
}
