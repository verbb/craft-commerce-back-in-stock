<?php
/**
 * Back In Stock plugin for Craft CMS 3.x
 *
 * Back in stock Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\backinstock\migrations;

use mediabeastnz\backinstock\BackInStock;

use craft\db\Migration;
use craft\db\Query;

use yii\db\Expression;

class m190609_000000_add_options_column extends Migration
{
    public function safeUp()
    {   
        if (!$this->db->columnExists('{{%backinstock_records}}', 'options')) {
            $this->addColumn('{{%backinstock_records}}', 'options', $this->text()->after('variantId'));
        }

        return true;
    }

    public function safeDown()
    {
        echo "m190609_000000_add_options_column cannot be reverted.\n";

        return false;
    }
}
