<?php
namespace verbb\backinstock\models;

use craft\base\Model;
use craft\helpers\App;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public string $pluginName = 'Back in Stock';
    public bool $hasCpSection = true;
    public bool $sendConfirmation = false;
    public ?string $confirmationEmailTemplate = null;
    public string $confirmationEmailSubject = 'Back in stock notification confirmation for {{ variant.title }}';
    public ?string $emailTemplate = null;
    public string $emailSubject = 'Order today, {{ variant.title }} is now in stock';
    public bool $purgeRequests = false;
    public ?string $fromEmail = null;
    public ?string $fromName = null;


    // Public Methods
    // =========================================================================

    public function getFromEmail(): ?string
    {
        $settings = Craft::$app->getProjectConfig()->get('email');

        return App::parseEnv($this->fromEmail) ?? App::parseEnv($settings['fromEmail'];
    }

    public function getFromName(): ?string
    {
        $settings = Craft::$app->getProjectConfig()->get('email');

        return App::parseEnv($this->fromName) ?? App::parseEnv($settings['fromName'];
    }

    public function getConfirmationEmailTemplate(): ?string
    {
        return App::parseEnv($this->confirmationEmailTemplate) ?? 'craft-commerce-back-in-stock/emails/confirmation';
    }

    public function getEmailTemplate(): ?string
    {
        return App::parseEnv($this->emailTemplate) ?? 'craft-commerce-back-in-stock/emails/notification';
    }
}
