<?php

/**
 * Back In Stock plugin for Craft CMS 3.x
 *
 * Back in stock Craft Commerce 2 plugin
 *
 * @link      https://www.mylesthe.dev/
 * @copyright Copyright (c) 2019 Myles Beardsmore
 */

namespace mediabeastnz\backinstock\controllers;

use mediabeastnz\backinstock\BackInStock;
use mediabeastnz\backinstock\records\BackInStockRecord;
use mediabeastnz\backinstock\models\BackInStockModel;

use Craft;
use craft\web\Controller;
use craft\commerce\elements\Variant;
use yii\web\Response;
use craft\db\Paginator;
use craft\db\Query;
use craft\web\twig\variables\Paginate;

/**
 * @author    Myles Beardsmore
 * @package   BackInStock
 */
class BaseController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected array|int|bool $allowAnonymous = ['register-interest'];

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionRegisterInterest()
    {

        $this->requirePostRequest();

        $session = Craft::$app->getSession();
        $request = Craft::$app->getRequest();

        $email = $request->getParam('email');
        $variantId = $request->getParam('variantId');
        $options = $request->getParam('options', json_encode([]));
        $locale = Craft::$app->language;

        if ($variantId == '' || !is_numeric($variantId)) {
            $error = Craft::t('craft-commerce-back-in-stock', 'Sorry you couldn\'t be added to the notifications list');

            if ($request->getAcceptsJson()) {
                return $this->asJson([
                    'success' => false,
                    'error' => $error,
                ]);
            }

            Craft::$app->getSession()->setError($error);

            return null;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = Craft::t('craft-commerce-back-in-stock', 'Please enter a valid email address');

            if ($request->getAcceptsJson()) {
                return $this->asJson([
                    'success' => false,
                    'error' => $error,
                ]);
            }

            Craft::$app->getSession()->setError($error);

            return null;
        }

        //check is product exists and is actually out of stock
        $variant = Variant::findOne($variantId);

        if (!$variant) {
            $error = Craft::t('craft-commerce-back-in-stock', 'Unable to find variant');

            if ($request->getAcceptsJson()) {
                return $this->asJson([
                    'success' => false,
                    'error' => $error,
                ]);
            }

            Craft::$app->getSession()->setError($error);

            return null;
        }

        if ($variant->hasStock()) {
            $error = Craft::t('craft-commerce-back-in-stock', 'Variant is in stock!');

            if ($request->getAcceptsJson()) {
                return $this->asJson([
                    'success' => false,
                    'error' => $error,
                ]);
            }

            Craft::$app->getSession()->setError($error);

            return null;
        }

        $model = new BackInStockModel();
        $model->variantId = $variantId;
        $model->email = $email;
        $model->options = $options;
        $model->locale = $locale;

        if (!BackInStock::$plugin->backInStockService->createBackInStockRecord($model)) {
            $error = Craft::t('craft-commerce-back-in-stock', 'Your email is already subscribed to receive updates for this product');

            if ($request->getAcceptsJson()) {
                return $this->asJson([
                    'success' => false,
                    'error' => $error,
                ]);
            }

            Craft::$app->getSession()->setError($error);

            return null;
        }

        $successMesaage = Craft::t('craft-commerce-back-in-stock', '{email} will be notified when {title} is available', ['email' => $email, 'title' => $variant->title]);

        if ($request->getAcceptsJson()) {
            return $this->asJson([
                'success' => true,
                'message' => $successMesaage,
            ]);
        }

        Craft::$app->getSession()->setFlash('notice', $successMesaage);

        return $this->redirectToPostedUrl();
    }

    public function actionLogs(): Response
    {
        $records = [];
        $c = new Query();
        $c->select('*')->from(['{{%backinstock_records}}'])->orderBy('dateCreated desc');
        $paginator = new Paginator($c, [
            'pageSize' => 30,
            'currentPage' => \Craft::$app->request->pageNum,
        ]);

        $pageResults = $paginator->getPageResults();
        if ($pageResults && count($pageResults)) {
            foreach ($pageResults as $pageResult) {
                $records[] = new BackInStockRecord($pageResult);
            }

            $pageOffset = $paginator->getPageOffset();
            $page = Paginate::create($paginator);

            return $this->renderTemplate('craft-commerce-back-in-stock/logs', [
                'logEntries' => $records,
                'pageInfo' => [
                    'first' => $pageOffset + 1,
                    'last' => $pageOffset + count($pageResults),
                    'total' => $paginator->getTotalResults(),
                    'currentPage' => $paginator->getCurrentPage(),
                    'totalPages' => $paginator->getTotalPages(),
                    'prevUrl' => $page->getPrevUrl(),
                    'nextUrl' => $page->getNextUrl(),
                ],
            ]);
        } else {
            return $this->renderTemplate('craft-commerce-back-in-stock/logs', [
                'logEntries' => $records,
            ]);
        }
    }

}
