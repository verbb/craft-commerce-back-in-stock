<?php
namespace verbb\backinstock\records;

use craft\db\ActiveRecord;

class Log extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%backinstock_records}}';
    }
}
