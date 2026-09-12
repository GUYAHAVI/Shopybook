<?php

/*
|--------------------------------------------------------------------------
| Guided page tours ("the advisor")
|--------------------------------------------------------------------------
|
| Each key is a tour id stored in users.completed_tours. `routes` lists the
| route-name patterns (request()->routeIs()) the tour applies to. Steps target
| CSS selectors — prefer data-tour="..." attributes so markup changes don't
| silently break a tour. Steps whose element is missing are skipped at runtime.
|
| Copy rules: sentence case, plain language, one clear action per step.
|
*/

return [

    'dashboard' => [
        'routes' => ['dashboard'],
        'title'  => 'Your home page',
        'intro'  => 'This is where you land every day. Want a 30-second walk-through?',
        'steps'  => [
            [
                'element' => '[data-tour="dash-kpis"]',
                'title'   => "Today's numbers",
                'text'    => 'Money in, orders, pending work and new customers — all for today only. When a card is empty it tells you what to do next.',
            ],
            [
                'element' => '[data-tour="dash-actions"]',
                'title'   => 'Start here',
                'text'    => 'These four buttons cover the most common jobs. New business? Add a product first, then make your first sale.',
            ],
            [
                'element' => '#sidebar',
                'title'   => 'Everything else lives here',
                'text'    => 'Sell, Stock, Book, Team, Money, Reports, Grow. Click a heading to open it. You only need a couple of these to begin.',
                'side'    => 'right',
            ],
            [
                'element' => '#sidebarSearch',
                'title'   => "Can't find something?",
                'text'    => 'Type a word like "tax" or "customer" and the menu filters instantly.',
                'side'    => 'right',
            ],
            [
                'element' => '#appLauncherBtn',
                'title'   => 'Shortcuts',
                'text'    => 'A grid of every tool in one place, for when you know exactly where you want to go.',
                'side'    => 'bottom',
            ],
        ],
    ],

    'products' => [
        'routes' => ['products.index'],
        'title'  => 'Your products',
        'intro'  => 'This is your catalog. Shall I show you how to add and manage stock?',
        'steps'  => [
            [
                'element' => '[data-tour="products-add"]',
                'title'   => 'Add a product',
                'text'    => 'Click Quick Add — name, price, quantity and you are done in under a minute. Use the arrow for the detailed form.',
            ],
            [
                'element' => '[data-tour="products-import"]',
                'title'   => 'Already have a list?',
                'text'    => 'Upload a spreadsheet and add many products at once.',
            ],
            [
                'element' => '[data-tour="products-receive"]',
                'title'   => 'New stock arrived?',
                'text'    => 'Record deliveries here so your quantities stay correct.',
            ],
            [
                'element' => '[data-tour="products-search"]',
                'title'   => 'Find a product fast',
                'text'    => 'Search by name, or filter by category and stock level.',
            ],
            [
                'element' => '[data-tour="products-grid"]',
                'title'   => 'Your catalog',
                'text'    => 'Each card shows price and stock. Use the buttons on a card to edit or remove it.',
            ],
        ],
    ],

    'products-quick-create' => [
        'routes' => ['products.quick-create'],
        'title'  => 'Add a product',
        'intro'  => 'Three fields are all you need. Want a quick pointer?',
        'steps'  => [
            [
                'element' => '#name',
                'title'   => 'What is it called?',
                'text'    => 'Use the name your customers would recognise, e.g. "Sugar 1kg".',
            ],
            [
                'element' => '#price',
                'title'   => 'Selling price',
                'text'    => 'What the customer pays. You can change it later.',
            ],
            [
                'element' => '#stock_quantity',
                'title'   => 'How many do you have?',
                'text'    => 'Shopybook counts down as you sell and warns you when stock is low.',
            ],
            [
                'element' => '[data-tour="quick-submit"]',
                'title'   => 'Save it',
                'text'    => 'That is it. You can add a photo or category any time from the product page.',
            ],
        ],
    ],

    'pos' => [
        'routes' => ['sales.pos'],
        'title'  => 'The till',
        'intro'  => 'This is where you record sales. Want to see how a sale works?',
        'steps'  => [
            [
                'element' => '#product_search',
                'title'   => '1. Find the product',
                'text'    => 'Type a name or scan a barcode. Click a product below to add it to the cart.',
            ],
            [
                'element' => '#cart_items',
                'title'   => '2. Check the cart',
                'text'    => 'Change quantities or remove items here. Totals update automatically.',
                'side'    => 'left',
            ],
            [
                'element' => '#customer_search',
                'title'   => '3. Who is buying? (optional)',
                'text'    => 'Pick a customer to track their purchases, or leave it as Walk-in.',
            ],
            [
                'element' => '#payment_method',
                'title'   => '4. How are they paying?',
                'text'    => 'Cash, M-Pesa, card or bank. M-Pesa can send a payment prompt to their phone.',
            ],
            [
                'element' => '#payment_status',
                'title'   => '5. Paid in full?',
                'text'    => 'Choose Partial if they paid a deposit, or Unpaid to record a debt. An invoice is created automatically.',
            ],
            [
                'element' => '#checkout_btn',
                'title'   => '6. Complete the sale',
                'text'    => 'Stock is reduced, the sale is recorded and you can print a receipt.',
                'side'    => 'left',
            ],
        ],
    ],

    'customers' => [
        'routes' => ['sales.customers'],
        'title'  => 'Your customers',
        'intro'  => 'Keep track of who buys from you. Quick tour?',
        'steps'  => [
            [
                'element' => '[data-tour="customers-add"]',
                'title'   => 'Add a customer',
                'text'    => 'Individuals for walk-in shoppers, Organisations for companies you invoice.',
            ],
            [
                'element' => '[data-tour="customers-list"]',
                'title'   => 'Customer list',
                'text'    => 'See order counts at a glance. Click a name to view their history and any money they owe.',
            ],
            [
                'element' => '[data-tour="sales-tabs"]',
                'title'   => 'Jump between Sales pages',
                'text'    => 'Customers, Orders and the till are one click apart.',
            ],
        ],
    ],

    'orders' => [
        'routes' => ['sales.orders'],
        'title'  => 'Your orders',
        'intro'  => 'Every sale you have made is listed here. Want a quick look around?',
        'steps'  => [
            [
                'element' => '[data-tour="orders-filters"]',
                'title'   => 'Filter by status',
                'text'    => 'Pending orders need attention. Completed ones are done and paid.',
            ],
            [
                'element' => '[data-tour="orders-list"]',
                'title'   => 'Order details',
                'text'    => 'Click an order to see items, payment status and to print a receipt or invoice.',
            ],
            [
                'element' => '[data-tour="sales-tabs"]',
                'title'   => 'Owed money?',
                'text'    => 'Customer Debts shows anyone who has not finished paying, and lets you record what they pay later.',
            ],
        ],
    ],

];
