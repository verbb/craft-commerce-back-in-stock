<?php
/**
 * Back In Stock plugin for Craft CMS 3.x
 *
 * Back in stock Craft Commerce 2 plugin
 *
 * @link      https://www.mylesthe.dev/
 * @copyright Copyright (c) 2019 Myles Beardsmore
 */

namespace mediabeastnz\backinstock\jobs;

use mediabeastnz\backinstock\BackInStock;
use mediabeastnz\backinstock\records\BackInStockRecord;

use Craft;
use craft\queue\BaseJob;

/**
 * @author    Myles Beardsmore
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
     * @var boolean
     */
    public $confirmation;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $template;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $record = BackInStockRecord::findOne($this->backInStockRecordId);
        if ($record) {
            if (BackInStock::$plugin->backInStockService->sendMail($record, $this->subject, $this->template)) {
                if (!$this->confirmation) {
                    if (BackInStock::$plugin->getSettings()->purgeRequests) {
                        $record->delete();
                    } else {
                        $record->isNotified = 1;
                        $record->save();
                    }
                }
            }
        } else {
            Craft::error("Couldn't find record for back in stock email. ID#" . $this->backInStockRecordId);
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('craft-commerce-back-in-stock', 'Sending email for back in stock plugin');
    }
}
