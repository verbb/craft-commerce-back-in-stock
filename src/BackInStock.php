<?php
namespace verbb\backinstock;

use verbb\backinstock\base\PluginTrait;
use verbb\backinstock\models\Settings;
use verbb\backinstock\variables\BackInStockVariable;

use Craft;
use craft\base\Plugin;
use craft\events\ModelEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;

use yii\base\Event;

use craft\commerce\elements\Variant;

class BackInStock extends Plugin
{
    // Properties
    // =========================================================================

    public bool $hasCpSection = true;
    public bool $hasCpSettings = true;
    public string $schemaVersion = '1.1.0';


    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $this->_registerVariables();
        $this->_registerCraftEventListeners();

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            $this->_registerCpRoutes();
        }

        $this->hasCpSection = $this->getSettings()->hasCpSection;
    }

    public function getPluginName(): string
    {
        return Craft::t('craft-commerce-back-in-stock', $this->getSettings()->pluginName);
    }

    public function getCpNavItem(): array
    {
        $nav = parent::getCpNavItem();

        $nav['label'] = $this->getPluginName();
        $nav['url'] = 'back-in-stock';

        if (Craft::$app->getUser()->checkPermission('accessPlugin-craft-commerce-back-in-stock')) {
            $nav['subnav']['logs'] = [
                'label' => Craft::t('craft-commerce-back-in-stock', 'Logs'),
                'url' => 'back-in-stock/logs',
            ];
        }

        if (Craft::$app->getUser()->getIsAdmin() && Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
            $nav['subnav']['settings'] = [
                'label' => Craft::t('craft-commerce-back-in-stock', 'Settings'),
                'url' => 'back-in-stock/settings',
            ];
        }

        return $nav;
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('back-in-stock/settings'));
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['back-in-stock'] = ['template' => 'craft-commerce-back-in-stock/index'];
            $event->rules['back-in-stock/logs'] = 'craft-commerce-back-in-stock/logs/index';
            $event->rules['back-in-stock/settings'] = 'craft-commerce-back-in-stock/plugin/settings';
        });
    }

    private function _registerVariables(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('backInStock', BackInStockVariable::class);
        });
    }

    private function _registerCraftEventListeners(): void
    {
        Event::on(Variant::class, Variant::EVENT_BEFORE_SAVE, function(ModelEvent $event) {
            $variant = $event->sender;
            
            if ($variant->id && ($variant->stock > 0 || $variant->hasUnlimitedStock)) {
                $this->getService()->isBackInStock($variant);
            }
        });
    }
}
