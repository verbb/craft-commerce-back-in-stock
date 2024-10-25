# Configuration
Create a `craft-commerce-back-in-stock.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

The below shows the defaults already used by Back In Stock, so you don't need to add these options unless you want to modify the values.

```php
<?php

return [
    '*' => [
        'pluginName' => 'Back In Stock',
        'hasCpSection' => true,
        'sendConfirmation' => false,
        'confirmationEmailTemplate' => 'craft-commerce-back-in-stock/emails/confirmation',
        'confirmationEmailSubject' =>'Back in stock notification confirmation for {{ variant.title }}',
        'emailTemplate' => 'craft-commerce-back-in-stock/emails/notification',
        'emailSubject' => 'Order today, {{ variant.title }} is now in stock',
        'purgeRequests' => false,
    ]
];
```

## Configuration options
- `pluginName` - If you wish to customise the plugin name.
- `hasCpSection` - Whether to enable Back in Stock in the main sidebar navigation.
- `sendConfirmation` - Whether a confirmation email should be sent to the customer if they request to be notified.
- `confirmationEmailTemplate` - Use a custom template for the confirmation email.
- `confirmationEmailSubject` - The email subject for the confirmation email.
- `emailTemplate` - Use a custom template for the notification email.
- `emailSubject` - The email subject for the notification email.
- `purgeRequests` - Purge notification requests from the database when a notification is successfully sent.

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Back In Stock.
