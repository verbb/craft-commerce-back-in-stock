<?php
/**
 * Back In Stock plugin for Craft CMS 3.x
 *
 * Back in stock Craft Commerce 2 plugin
 *
 * @link      https://www.mylesthe.dev/
 * @copyright Copyright (c) 2019 Myles Beardsmore
 */

namespace mediabeastnz\backinstock\migrations;

use mediabeastnz\backinstock\BackInStock;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * @author    Myles Beardsmore
 * @package   BackInStock
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%backinstock_records}}')) {
            $this->createTables();
            $this->createIndexes();
            $this->addForeignKeys();
        }
    }

   /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->removeTables();
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%backinstock_records}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%backinstock_records}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'email' => $this->string(255)->notNull()->defaultValue(''),
                    'variantId' => $this->integer()->notNull(),
                    'locale' => $this->string(255),
                    'options' => $this->text(),
                    'isNotified' => $this->boolean()->defaultValue(false),
                ]
            );
        }

        return $tablesCreated;
    }


    /**
     * @return void
     */
    public function createIndexes()
    {
        $this->createIndex(null, '{{%backinstock_records}}', ['variantId'], false);
    }

    
    /**
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%backinstock_records}}', 'variantId'),
            '{{%backinstock_records}}',
            'variantId',
            '{{%commerce_variants}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }


    /**
     * @return void
     */
    protected function removeTables()
    {
        $this->dropTableIfExists('{{%backinstock_records}}');
    }
}
