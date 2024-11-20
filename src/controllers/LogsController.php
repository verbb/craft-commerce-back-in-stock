<?php
namespace verbb\backinstock\controllers;

use verbb\backinstock\BackInStock;
use verbb\backinstock\models\Log;
use verbb\backinstock\models\Settings;

use Craft;
use craft\db\Query;
use craft\helpers\AdminTable;
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use craft\helpers\Json;
use craft\i18n\Locale;
use craft\web\Controller;

use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use craft\commerce\Plugin as Commerce;

class LogsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        return $this->renderTemplate('craft-commerce-back-in-stock/logs');
    }

    public function actionGetLogs(): Response
    {
        $this->requireAcceptsJson();

        $page = $this->request->getParam('page', 1);
        $sort = $this->request->getParam('sort');
        $limit = $this->request->getParam('per_page', 10);
        $search = $this->request->getParam('search');
        $offset = ($page - 1) * $limit;

        $query = (new Query())
            ->from(['logs' => '{{%backinstock_records}}'])
            ->select([
                'logs.*',
                'products.typeId AS productTypeId',
            ])
            ->leftJoin('{{%commerce_variants}} variants', '[[logs.variantId]] = [[variants.id]]')
            ->leftJoin('{{%commerce_products}} products', '[[variants.productId]] = [[products.id]]')
            ->orderBy(['id' => SORT_DESC]);

        if ($search) {
            $likeOperator = Craft::$app->getDb()->getIsPgsql() ? 'ILIKE' : 'LIKE';

            $query->andWhere([
                'or',
                [$likeOperator, 'logs.email', '%' . str_replace(' ', '%', $search) . '%', false],
                [$likeOperator, 'logs.locale', '%' . str_replace(' ', '%', $search) . '%', false],
            ]);
        }

        $total = $query->count();

        $query->limit($limit);
        $query->offset($offset);

        if ($sort) {
            $sortField = $sort[0]['sortField'] ?? null;
            $direction = $sort[0]['direction'] ?? null;

            if ($sortField && $direction) {
                $query->orderBy($sortField . ' ' . $direction);
            }
        }

        $logs = $query->all();

        $tableData = [];
        $productTypeMap = [];

        $dateFormat = Craft::$app->getFormattingLocale()->getDateTimeFormat('short', Locale::FORMAT_PHP);

        foreach (Commerce::getInstance()->getProductTypes()->getAllProductTypes() as $productType) {
            $productTypeMap[$productType->id] = [
                'title' => $productType->name,
                'cpEditUrl' => $productType->cpEditUrl,
            ];
        }

        foreach ($logs as $log) {
            $productLink = $productTypeMap[$log['productTypeId']] ?? [];
            $dateCreated = $log['dateCreated'] ? DateTimeHelper::toDateTime($log['dateCreated']) : null;

            $tableData[] = [
                'title' => $log['email'],
                'email' => $log['email'],
                'variantId' => $productLink,
                'locale' => $log['locale'],
                'isNotified' => $log['isNotified'],
                'dateCreated' => $dateCreated?->format($dateFormat) ?? null,
            ];
        }

        return $this->asJson([
            'pagination' => AdminTable::paginationLinks($page, $total, $limit),
            'data' => $tableData,
        ]);
    }
}
