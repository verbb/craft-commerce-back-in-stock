<?php
namespace verbb\backinstock\migrations;

use verbb\backinstock\BackInStock;

use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

class Install extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->createTables();
        $this->createIndexes();
        $this->createForeignKeys();

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropForeignKeys();
        $this->dropTables();

        return true;
    }

    public function createTables(): void
    {
        $this->archiveTableIfExists('{{%backinstock_records}}');
        $this->createTable('{{%backinstock_records}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(255)->notNull()->defaultValue(''),
            'variantId' => $this->integer()->notNull(),
            'locale' => $this->string(255),
            'options' => $this->text(),
            'isNotified' => $this->boolean()->defaultValue(false),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);
    }

    public function createIndexes(): void
    {
        $this->createIndex(null, '{{%backinstock_records}}', ['variantId'], false);
    }

    public function createForeignKeys(): void
    {
        if ($this->db->tableExists('{{%commerce_variants}}')) {
            $this->addForeignKey(null, '{{%backinstock_records}}', ['variantId'], '{{%commerce_variants}}', ['id'], 'CASCADE', 'CASCADE');
        }
    }

    public function dropTables(): void
    {
        $this->dropTableIfExists('{{%backinstock_records}}');
    }

    public function dropForeignKeys(): void
    {
        if ($this->db->tableExists('{{%backinstock_records}}')) {
            MigrationHelper::dropAllForeignKeysOnTable('{{%backinstock_records}}', $this);
        }
    }
}
