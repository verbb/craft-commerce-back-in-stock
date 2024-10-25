# Usage
To allow your users to register interest in a product that's out of stock, you'll need to provide them a form to enter their email. This will subscribe them to an email notification, so that they can be notified when the product is back in stock.

:::tip
Check out our ready-to-go [Tailwind template](docs:template-guides/example-form).
:::

The form will need 3 fields. 2 hidden fields and 1 visible field where the user can enter their email.

```twig
<input type="hidden" name="action" value="craft-commerce-back-in-stock/base/register-interest">
<input type="hidden" name="variantId" value="{{ product.defaultVariant.id }}">
<input type="text" name="email" value="{{ currentUser.email }}">
```

You can also include an `options` value to save additional information with the form submission. This should be in the form of a JSON object. This input is entirely optional.

```twig
{% set options = { title: 'Some Title', productAttribute: 'Some Value' } %}

<input type="hidden" name="options" value="{{ options | json_encode }}">
```

Once the user fills in their email, they'll be notified when the product comes back into stock.

## Confirmation Email
You can also set an email to be sent as soon as the user registers their interest, in the form of a confirmation email.

## Automatically Purge Notifications
If privacy is a requirement you'll want to enable "Automatically Purge Notification Requests". This will delete the users information once they have been notified rather than being kept in the database.
