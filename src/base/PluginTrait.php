<?php
namespace verbb\backinstock\base;

use verbb\backinstock\BackInStock;
use verbb\backinstock\services\Logs;
use verbb\backinstock\services\Service;

use Craft;

use verbb\base\LogTrait;
use verbb\base\helpers\Plugin;

trait PluginTrait
{
    // Static Properties
    // =========================================================================

    public static ?BackInStock $plugin = null;
    

    // Traits
    // =========================================================================

    use LogTrait;

    // Static Methods
    // =========================================================================

    public static function config(): array
    {
        Plugin::bootstrapPlugin('craft-commerce-back-in-stock');

        return [
            'components' => [
                'logs' => Logs::class,
                'service' => Service::class,
            ],
        ];
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

}