<?php
namespace verbb\backinstock\services;

use verbb\backinstock\BackInStock;
use verbb\backinstock\models\Log;
use verbb\backinstock\models\Settings;
use verbb\backinstock\queue\jobs\SendEmailNotification;
use verbb\backinstock\records\Log as LogRecord;

use Craft;
use craft\base\Component;
use craft\helpers\App;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use craft\mail\Message;

use Throwable;

use craft\commerce\elements\Variant;

class Service extends Component
{
    // Public Methods
    // =========================================================================

    public function isBackInStock(Variant $variant): bool
    {
        // Check that before save variant had 0 stock and was not unlimited
        $originalObject = Variant::findOne($variant->id);

        if ($originalObject) {
            if ($originalObject->stock == 0 && !$originalObject->hasUnlimitedStock) {
                $this->findInterestedEmails($variant->id);
                
                return true;
            }
        }

        return false;
    }

    public function findInterestedEmails(int $variantId): void
    {
        $logs = LogRecord::find()->where([
            'variantId' => $variantId,
            'isNotified' => false,
        ])->all();

        if ($logs) {
            $template = BackInStock::$plugin->getSettings()->emailTemplate;
            $subject = BackInStock::$plugin->getSettings()->emailSubject;

            // Add all emails to send to the queue
            foreach ($logs as $log) {
                Craft::$app->getQueue()->push(new SendEmailNotification([
                    'confirmation' => false,
                    'logId' => $log->id,
                    'subject' => $subject,
                    'template' => $template,
                ]));
            }
        }
    }

    public function sendMail(LogRecord $log, string $subject, ?string $templatePath = null): bool
    {
        $view = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();
        $originalLanguage = $log->locale ? $log->locale : Craft::$app->language;
        Craft::$app->language = $originalLanguage;

        if (strpos($templatePath, 'craft-commerce-back-in-stock/emails') !== false) {
            $view->setTemplateMode($view::TEMPLATE_MODE_CP);
        } else {
            $view->setTemplateMode($view::TEMPLATE_MODE_SITE);
        }

        $variant = Variant::findOne($log->variantId);

        if (!$variant) {
            $error = Craft::t('craft-commerce-back-in-stock', 'Could not find Variant for Back In Stock Notification email.');
            
            BackInStock::error($error);
            
            $view->setTemplateMode($oldTemplateMode);
            
            return false;
        }

        // making sure that the subject is correct for the preheader text
        $subject = Craft::t('craft-commerce-back-in-stock', $subject, [
            'variant' => $variant,
        ]);

        $subject = $view->renderString($subject, [
            'variant' => $variant,
        ]);

        // template variables
        $renderVariables = [
            'subject' => $subject,
            'variant' => $variant,
        ];

        // Add the log options, if available
        if ($log && is_string($log->options)) {
            $renderVariables['options'] = Json::decode($log->options);
        }

        $templatePath = $view->renderString($templatePath, $renderVariables);

        // validate that the email template exists
        if (!$view->doesTemplateExist($templatePath)) {
            $error = Craft::t('craft-commerce-back-in-stock', 'Email template does not exist at “{templatePath}”.', [
                'templatePath' => $templatePath,
            ]);
            
            BackInStock::error($error);
            
            $view->setTemplateMode($oldTemplateMode);
            
            return false;
        }

        // Get from address from site settings
        $settings = Craft::$app->projectConfig->get('email');

        // build the email
        $newEmail = new Message();
        $newEmail->setFrom([App::parseEnv($settings['fromEmail']) => App::parseEnv($settings['fromName'])]);
        $newEmail->setTo($log->email);
        $newEmail->setSubject($subject);
        $newEmail->setHtmlBody($view->renderTemplate($templatePath, $renderVariables));

        try {
            if (!Craft::$app->getMailer()->send($newEmail)) {
                $error = Craft::t('craft-commerce-back-in-stock', 'Back In Stock email “{email}” could not be sent');
                
                BackInStock::error($error);
                
                $view->setTemplateMode($oldTemplateMode);
                
                return false;
            }
        } catch (Throwable $e) {
            $error = Craft::t('craft-commerce-back-in-stock', 'Back In Stock email could not be sent', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'order' => $order->id,
            ]);

            BackInStock::error($error);

            $view->setTemplateMode($oldTemplateMode);

            return false;
        }

        return true;
    }
}
