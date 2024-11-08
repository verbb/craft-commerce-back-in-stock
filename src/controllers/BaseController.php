<?php
namespace verbb\backinstock\controllers;

use verbb\backinstock\BackInStock;
use verbb\backinstock\models\Log;
use verbb\backinstock\models\Settings;
use verbb\backinstock\queue\jobs\SendEmailNotification;

use Craft;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use craft\web\Controller;

use yii\web\Response;

use craft\commerce\elements\Variant;

class BaseController extends Controller
{
    // Properties
    // =========================================================================

    protected array|int|bool $allowAnonymous = ['register-interest'];


    // Public Methods
    // =========================================================================

    public function actionRegisterInterest(): ?Response
    {
        $this->requirePostRequest();

        $session = Craft::$app->getSession();

        $log = new Log();
        $log->variantId = $this->request->getParam('variantId');
        $log->email = $this->request->getParam('email');
        $log->options = Json::decode($this->request->getParam('options')) ?? [];
        $log->locale = Craft::$app->language;

        if (!BackInStock::$plugin->getLogs()->saveLog($log)) {
            $error = array_values($log->getErrors())[0][0] ?? Craft::t('craft-commerce-back-in-stock', 'Sorry you couldn‘t be added to the notifications list.');

            BackInStock::error(Json::encode($log->getErrors()));

            if ($this->request->getAcceptsJson()) {
                return $this->asJson([
                    'success' => false,
                    'error' => $error,
                ]);
            }

            $session->setError($error);

            return null;
        }

        // Check settings to see if confirmation is required
        $template = BackInStock::$plugin->getSettings()->confirmationEmailTemplate;
        $subject = BackInStock::$plugin->getSettings()->confirmationEmailSubject;
        $sendConfirmation = BackInStock::$plugin->getSettings()->sendConfirmation;

        if ($sendConfirmation) {
            Craft::$app->getQueue()->push(new SendEmailNotification([
                'confirmation' => true,
                'logId' => $log->id,
                'subject' => $subject,
                'template' => $template,
            ]));
        }

        $successMesaage = Craft::t('craft-commerce-back-in-stock', '{email} will be notified when {title} is available.', ['email' => $log->email, 'title' => $log->getVariant()->title]);

        if ($this->request->getAcceptsJson()) {
            return $this->asJson([
                'success' => true,
                'message' => $successMesaage,
            ]);
        }

        $session->setFlash('notice', $successMesaage);

        return $this->redirectToPostedUrl();
    }
}
