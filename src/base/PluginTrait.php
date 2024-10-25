<?php
namespace verbb\backinstock\base;

use verbb\backinstock\BackInStock;
use verbb\backinstock\services\Logs;
use verbb\backinstock\services\Service;

use Craft;

use yii\log\Logger;

use verbb\base\BaseHelper;

trait PluginTrait
{
    // Static Properties
    // =========================================================================

    public static BackInStock $plugin;


    // Public Methods
    // =========================================================================

    public static function log(string $message, array $attributes = []): void
    {
        if ($attributes) {
            $message = Craft::t('craft-commerce-back-in-stock', $message, $attributes);
        }

        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'craft-commerce-back-in-stock');
    }

    public static function error(string $message, array $attributes = []): void
    {
        if ($attributes) {
            $message = Craft::t('craft-commerce-back-in-stock', $message, $attributes);
        }

        Craft::getLogger()->log($message, Logger::LEVEL_ERROR, 'craft-commerce-back-in-stock');
    }


    // Public Methods
    // =========================================================================

    public function getLogs(): Logs
    {
        return $this->get('logs');
    }

    public function getService(): Service
    {
        return $this->get('service');
    }


    // Private Methods
    // =========================================================================

    private function _setPluginComponents(): void
    {
        $this->setComponents([
            'logs' => Logs::class,
            'service' => Service::class,
        ]);

        BaseHelper::registerModule();
    }

    private function _setLogging(): void
    {
        BaseHelper::setFileLogging('craft-commerce-back-in-stock');
    }

}