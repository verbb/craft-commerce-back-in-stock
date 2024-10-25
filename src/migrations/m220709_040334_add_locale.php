<?php
namespace verbb\backinstock\migrations;

use Craft;
use craft\db\Migration;

class m220709_040334_add_locale extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        if (!$this->db->columnExists('{{%backinstock_records}}', 'locale')) {
            $this->addColumn('{{%backinstock_records}}', 'locale', $this->string()->after('variantId'));
        }

        return true;
    }

    public function safeDown(): bool
    {
        echo "m220709_040334_add_locale cannot be reverted.\n";
        return false;
    }
}