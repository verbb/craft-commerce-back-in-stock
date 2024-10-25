<?php
namespace verbb\backinstock\controllers;

use verbb\backinstock\BackInStock;
use verbb\backinstock\models\Log;
use verbb\backinstock\models\Settings;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\web\Controller;

use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class LogsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        $logs = BackInStock::$plugin->getLogs()->getAllLogs();

        return $this->renderTemplate('craft-commerce-back-in-stock/logs', [
            'logs' => $logs,
        ]);
    }
}
