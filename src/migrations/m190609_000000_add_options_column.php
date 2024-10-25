<?php
namespace verbb\backinstock\migrations;

use Craft;
use craft\db\Migration;

class m190609_000000_add_options_column extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        if (!$this->db->columnExists('{{%backinstock_records}}', 'options')) {
            $this->addColumn('{{%backinstock_records}}', 'options', $this->text()->after('variantId'));
        }

        return true;
    }

    public function safeDown(): bool
    {
        echo "m190609_000000_add_options_column cannot be reverted.\n";
        return false;
    }
}