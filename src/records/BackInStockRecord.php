<?php
/**
 * Back In Stock plugin for Craft CMS 3.x
 *
 * Back in stock Craft Commerce 2 plugin
 *
 * @link      https://www.mylesthe.dev/
 * @copyright Copyright (c) 2019 Myles Beardsmore
 */

namespace mediabeastnz\backinstock\records;

use mediabeastnz\backinstock\BackInStock;
use craft\commerce\elements\Variant;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    Myles Beardsmore
 * @package   BackInStock
 */
class BackInStockRecord extends ActiveRecord
{

    public function getVariant()
    {
        $query = Variant::find();
        $query->where(['commerce_variants.id' => $this->variantId]);
        return $query->one();
    }

    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%backinstock_records}}';
    }
}
