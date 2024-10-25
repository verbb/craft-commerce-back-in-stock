# Example Form
The below HTML template gives you a head-start (using [Tailwind](https://tailwindcss.com/)) to creating a form for users to register their interest with an out of stock product.

```html
<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Back In Stock Form Example</title>
</head>
<body class="flex min-h-full flex-col">

{% set product = craft.products.one() %}
{% set error = craft.app.session.getFlash('error') %}
{% set success = craft.app.session.getFlash('notice') %}

{% if product %}
    <div class="grow bg-gray-100">
        <div class="mx-auto max-w-lg py-12 sm:px-6 lg:px-8">
            <div class="mb-4 flex flex-wrap items-center justify-center">
                <h2 class="flex-1 font-bold text-xl text-slate-700">{{ product.title }}</h2>

                {% if product.defaultVariant.hasStock() %}
                    <span class="flex-none bg-green-200 rounded-full px-3 py-1 text-xs font-semibold text-green-700">IN STOCK</span>
                {% else %}
                    <span class="flex-none bg-red-200 rounded-full px-3 py-1 text-xs font-semibold text-red-700">OUT OF STOCK</span>
                {% endif %}
            </div>

            {% if error %}
                <div class="sm:rounded-lg bg-red-200 p-4 mb-4">
                    <h3 class="text-sm font-medium text-red-800">{{ error }}</h3>
                </div>
            {% endif %}

            {% if success %}
                <div class="sm:rounded-lg bg-green-100 p-4 mb-4">
                    <h3 class="text-sm font-medium text-green-800">{{ success }}</h3>
                </div>
            {% endif %}

            <form method="post" accept-charset="UTF-8"class="bg-white mb-4 shadow sm:rounded-lg p-6">
                {{ actionInput('craft-commerce-back-in-stock/base/register-interest') }}
                {{ csrfInput() }}

                <input type="hidden" name="variantId" value="{{ product.defaultVariant.id }}">

                <div class="mb-4">
                    <label class="block text-slate-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input name="email" id="email" type="email" placeholder="Enter your email address" class="appearance-none border rounded w-full py-3 px-3 text-slate-700 leading-tight focus:outline-none focus:shadow-outline text-sm">
                </div>

                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">Notify Me</button>
            </form>

            <p class="text-center text-slate-400 text-xs">Your email address wont be shared or used for anything other than being notified of this item being re-stocked.</p>
        </div>
    </div>
{% endif %}

</body>
</html>
```