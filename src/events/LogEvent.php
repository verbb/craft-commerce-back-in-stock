<?php
namespace verbb\backinstock\events;

use verbb\backinstock\models\Log;

use yii\base\Event;

class LogEvent extends Event
{
    // Properties
    // =========================================================================

    public Log $log;
    public bool $isNew = false;

}
