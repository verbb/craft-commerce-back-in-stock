<?php
/**
 * Back In Stock plugin for Craft CMS 3.x
 *
 * Back in stock Craft Commerce 2 plugin
 *
 * @link      https://www.mylesderham.dev/
 * @copyright Copyright (c) 2019 Myles Derham
 */

namespace mediabeastnz\backinstock\services;

use mediabeastnz\backinstock\BackInStock;
use mediabeastnz\backinstock\records\BackInStockRecord;
use mediabeastnz\backinstock\models\BackInStockModel;
use mediabeastnz\backinstock\jobs\SendEmailNotification;

use Craft;
use craft\base\Component;
use craft\helpers\Json;
use craft\mail\Message;
use craft\commerce\elements\Variant;


/**
 * @author    Myles Derham
 * @package   BackInStock
 */
class BackInStockService extends Component
{
    // Public Methods
    // =========================================================================

    public function findVariantsInStock($variant)
    {
        // product type has variants to check
        $variantsToCheck = array();
        if (isset($variant->product->defaultVariantId)) {
            foreach ($variant->product->variants as $v) {
                if ($v->stock > 0 || $v->hasUnlimitedStock) {
                    $variantsToCheck[] = $v->id;
                }
            }
        } else {
            // no variants - just check the product (which is still a variant...)
            $variantsToCheck[] = $variant->productId;
        }

        // find any variants that had 0 stock but now have more
        if (!empty($variantsToCheck)) {
            $variantsNowInStock = array();
            foreach ($variantsToCheck as $variantToCheck) {
                $originalObject = Variant::findOne($variantToCheck);
                if ($originalObject) {
                    if ($originalObject->stock == 0 || $originalObject->hasUnlimitedStock) {
                        $variantsNowInStock[] = $originalObject->id;
                    }
                }
            }

            if (!empty($variantsNowInStock)) {
                $this->findInterestedEmails($variantsNowInStock);
            }
        }

        return false;
    }


    public function findInterestedEmails($variantsNowInStock)
    {
        $records = BackInStockRecord::find()->where([
            'variantId' => $variantsNowInStock,
            'isNotified' => 0
        ])->all();

        if ($records) {
            // add all emails to send to the queue
            foreach ($records as $record) {
                Craft::$app->queue->push(new SendEmailNotification([
                    'backInStockRecordId' => $record->id,
                    'variantId' => $record->variantId,
                    'email' => $record->email
                ]));
            }
        }
    }


    public function createBackInStockRecord(BackInStockModel $model)
    {
        if ($model->variantId && $model->email) {
            
            // prevent duplicate notification requests
            $record = BackInStockRecord::findOne(array(
                'variantId' => $model->variantId, 
                'email' => $model->email,
                'options' => $model->options,
                'isNotified' => 0
            ));

            if (!$record) {
                // record deosn't exist so create it
                $newRecord = new BackInStockRecord();
                $newRecord->variantId = $model->variantId;
                $newRecord->email = $model->email;
                $newRecord->options = $model->options;
                $newRecord->save();

                return true;
            }

        }

        return false;
    }


    /**
     * Send the abandoned cart reminder email.
     *
     * @param AbandonedCart $cart
     * @return bool $result
     */
    public function sendMail($variantId, $subject, $record = null, $recipient = null, $templatePath = null): bool
    {        
        // settings/defaults
        $view = Craft::$app->getView();
        $oldTemplateMode = $view->getTemplateMode();
        $originalLanguage = Craft::$app->language;

        if (strpos($templatePath, "craft-commerce-back-in-stock/emails") !== false) { 
            $view->setTemplateMode($view::TEMPLATE_MODE_CP);
        } else {
            $view->setTemplateMode($view::TEMPLATE_MODE_SITE);
        }

        // get the order from the cart
        $variant = Variant::findOne($variantId);

        if (!$variant) {
            $error = Craft::t('craft-commerce-back-in-stock', 'Could not find Variant for Back In Stock Notification email.');
            Craft::error($error, __METHOD__);
            Craft::$app->language = $originalLanguage;
            $view->setTemplateMode($oldTemplateMode);
            return false;
        }

        // template variables
        $renderVariables = [
            'subject' => $subject,
            'variant' => $variant,
        ];

        // Add the record options, if available
        if ($record && is_string($record->options)) {
            $renderVariables['options'] = Json::decode($record->options);
        }

        $templatePath = $view->renderString($templatePath, $renderVariables);

        // validate that the email template exists
        if (!$view->doesTemplateExist($templatePath)) {
            $error = Craft::t('craft-commerce-back-in-stock', 'Email template does not exist at “{templatePath}”.', [
                'templatePath' => $templatePath,
            ]);
            Craft::error($error, __METHOD__);
            Craft::$app->language = $originalLanguage;
            $view->setTemplateMode($oldTemplateMode);
            return false;
        }

        // Get from address from site settings
        $settings = Craft::$app->systemSettings->getSettings('email');
        
        // build the email
        $newEmail = new Message();
        $newEmail->setFrom([Craft::parseEnv($settings['fromEmail']) => Craft::parseEnv($settings['fromName'])]);
        $newEmail->setTo($recipient);
        $newEmail->setSubject($view->renderString($subject, $renderVariables));
        $newEmail->setHtmlBody($view->renderTemplate($templatePath, $renderVariables));

        // attempt to send
        try {
            if (!Craft::$app->getMailer()->send($newEmail)) {
                $error = Craft::t('craft-commerce-back-in-stock', 'Back In Stock email “{email}” could not be sent');
                Craft::error($error, __METHOD__);
                Craft::$app->language = $originalLanguage;
                $view->setTemplateMode($oldTemplateMode);
                return false;
            }
        } catch (\Exception $e) {
            $error = Craft::t('craft-commerce-back-in-stock', 'Back In Stock email could not be sent', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'order' => $order->id
            ]);
            Craft::error($error, __METHOD__);
            Craft::$app->language = $originalLanguage;
            $view->setTemplateMode($oldTemplateMode);
            return false;
        }

        return true;
    }

}
