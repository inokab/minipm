<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261018172340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Replace the user roles JSON array with a single role column';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD role VARCHAR(20) NOT NULL');
        $this->addSql("UPDATE user SET role = IF(JSON_CONTAINS(roles, '\"ROLE_ADMIN\"'), 'ROLE_ADMIN', 'ROLE_MEMBER')");
        $this->addSql('ALTER TABLE user DROP roles');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL');
        $this->addSql('UPDATE user SET roles = JSON_ARRAY(role)');
        $this->addSql('ALTER TABLE user DROP role');
    }
}
