<?php
/**
 * Back In Stock plugin for Craft CMS 3.x
 *
 * Back in stock Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\backinstock\jobs;

use mediabeastnz\backinstock\BackInStock;
use mediabeastnz\backinstock\records\BackInStockRecord;

use Craft;
use craft\queue\BaseJob;

/**
 * @author    Myles Derham
 * @package   BackInStock
 */
class SendEmailNotification extends BaseJob
{
    // Public Properties
    // =========================================================================

    /**
     * @var int
     */
    public $backInStockRecordId;
    
    /**
     * @var int
     */
    public $variantId;

    /**
     * @var string
     */
    public $email;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {

        $template = BackInStock::$plugin->getSettings()->emailTemplate;
        $subject = BackInStock::$plugin->getSettings()->emailSubject;
        $recipient = $this->email;
        $variant = $this->variantId;

        $record = BackInStockRecord::findOne($this->backInStockRecordId);
        if ($record) {
            $record->isNotified = 1;
            $record->save();
        }

        BackInStock::$plugin->backInStockService->sendMail($variant, $subject, $recipient, $template);
        
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('craft-commerce-back-in-stock', 'Send email notifications of stock increase');
    }
}
