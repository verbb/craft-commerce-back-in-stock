# Events
Back In Stock provides a collection of events for extending its functionality. Modules and plugins can register event listeners, typically in their `init()` methods, to modify Back In Stock’s behavior.

## Log Events

### The `beforeSaveLog` event
The event that is triggered before a log is saved.

```php
use verbb\backinstock\events\LogEvent;
use verbb\backinstock\services\Logs;
use yii\base\Event;

Event::on(Logs::class, Logs::EVENT_BEFORE_SAVE_LOG, function(LogEvent $event) {
    $log = $event->log;
    $isNew = $event->isNew;
    // ...
});
```

### The `afterSaveLog` event
The event that is triggered after a log is saved.

```php
use verbb\backinstock\events\LogEvent;
use verbb\backinstock\services\Logs;
use yii\base\Event;

Event::on(Logs::class, Logs::EVENT_AFTER_SAVE_LOG, function(LogEvent $event) {
    $log = $event->log;
    $isNew = $event->isNew;
    // ...
});
```
