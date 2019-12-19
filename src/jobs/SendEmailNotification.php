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

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {

        $template = BackInStock::$plugin->getSettings()->emailTemplate;
        $subject = BackInStock::$plugin->getSettings()->emailSubject;

        $record = BackInStockRecord::findOne($this->backInStockRecordId);
        if ($record) {
            if (BackInStock::$plugin->backInStockService->sendMail($record, $subject, $template)) {
                if (BackInStock::$plugin->getSettings()->purgeRequests) {
                    $record->delete();
                } else {
                    $record->isNotified = 1;
                    $record->save();
                }
            }
        }
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
