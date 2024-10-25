<?php
namespace verbb\backinstock\models;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public string $pluginName = 'Back in Stock';
    public bool $hasCpSection = true;
    public bool $sendConfirmation = false;
    public string $confirmationEmailTemplate = 'craft-commerce-back-in-stock/emails/confirmation';
    public string $confirmationEmailSubject = 'Back in stock notification confirmation for {{ variant.title }}';
    public string $emailTemplate = 'craft-commerce-back-in-stock/emails/notification';
    public string $emailSubject = 'Order today, {{ variant.title }} is now in stock';
    public bool $purgeRequests = false;
}
