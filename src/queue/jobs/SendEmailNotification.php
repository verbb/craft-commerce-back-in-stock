<?php
namespace verbb\backinstock\queue\jobs;

use verbb\backinstock\BackInStock;
use verbb\backinstock\records\Log as LogRecord;

use Craft;
use craft\queue\BaseJob;

class SendEmailNotification extends BaseJob
{
    // Properties
    // =========================================================================

    public int $logId;
    public bool $confirmation;
    public string $subject;
    public string $template;


    // Public Methods
    // =========================================================================

    public function execute($queue): void
    {
        $log = LogRecord::findOne($this->logId);
        
        if ($log) {
            if (BackInStock::$plugin->getService()->sendMail($log, $this->subject, $this->template)) {
                if (!$this->confirmation) {
                    if (BackInStock::$plugin->getSettings()->purgeRequests) {
                        $log->delete();
                    } else {
                        $log->isNotified = true;
                        $log->save(false);
                    }
                }
            }
        } else {
            Craft::error("Couldn't find record for back in stock email. ID#" . $this->logId);
        }
    }


    // Protected Methods
    // =========================================================================

    protected function defaultDescription(): ?string
    {
        return Craft::t('craft-commerce-back-in-stock', 'Sending email for back in stock plugin');
    }
}
