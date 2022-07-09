<?php

namespace mediabeastnz\backinstock\migrations;

use Craft;
use craft\db\Migration;

/**
 * m220709_040334_add_locale migration.
 */
class m220709_040334_add_locale extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!$this->db->columnExists('{{%backinstock_records}}', 'locale')) {
            $this->addColumn('{{%backinstock_records}}', 'locale', $this->string()->after('variantId'));
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220709_040334_add_locale cannot be reverted.\n";
        return false;
    }
}
