<?php
namespace verbb\backinstock\variables;

use verbb\backinstock\BackInStock;

class BackInStockVariable
{
    // Public Methods
    // =========================================================================

    public function getPlugin(): BackInStock
    {
        return BackInStock::$plugin;
    }

    public function getPluginName(): string
    {
        return BackInStock::$plugin->getPluginName();
    }
    
}