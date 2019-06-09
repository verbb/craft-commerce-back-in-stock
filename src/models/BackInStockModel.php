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
use craft\helpers\Json;

/**
 * @author    Myles Derham
 * @package   BackInStock
 */
class BackInStockModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $id;

    public $email;

    public $variantId;

    public $options;

    public $isNotified;

    public $uid;

    public $dateCreated;

    public $dateUpdated;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (is_string($this->options)) {
            $this->options = Json::decode($this->options);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'variantId'], 'number', 'integerOnly' => true],
            [['email', 'variantId'], 'required'],
            [['email'], 'string', 'max' => 255],
        ];
    }

}
