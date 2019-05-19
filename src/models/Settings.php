<?php
/**
 * Back In Stock plugin for Craft CMS 3.x
 *
 * Back in stock Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\backinstock\models;

use mediabeastnz\backinstock\BackInStock;

use Craft;
use craft\base\Model;

/**
 * @author    Myles Derham
 * @package   BackInStock
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $emailTemplate = 'craft-commerce-back-in-stock/emails/notification';

    /**
     * @var string
     */
    public $emailSubject = 'Order today, {{variant.title}} is now in stock';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['emailTemplate', 'emailSubject'], 'string'],
            [['emailTemplate', 'emailSubject'], 'required']
        ];
    }
}
