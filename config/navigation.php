<?php

/*
|--------------------------------------------------------------------------
| Context-aware navigation map
|--------------------------------------------------------------------------
|
| Odoo-style module navigation. The app launcher (topbar waffle) switches
| between "apps"; when the current route belongs to an app, the left sidebar
| swaps to that app's sub-sections and the topbar shows an app breadcrumb.
|
| Each app:
|   label       Display name shown in the sidebar group + breadcrumb.
|   icon        Font Awesome class for the app.
|   match       Route-name patterns (request()->routeIs()) that mean the user
|               is currently "inside" this app.
|   items       Sub-sections: label, route (name), icon, optional active
|               pattern (defaults to route), optional permission (module key
|               checked with the same $__can() helper used elsewhere).
|
| Ordering of apps matters: the first app whose `match` matches wins.
|
*/

return [
    'apps' => [

        'sales' => [
            'label' => 'Sales',
            'icon'  => 'fa-shopping-cart',
            'match' => ['sales.*', 'returns.*'],
            'items' => [
                ['label' => 'Point of Sale', 'route' => 'sales.pos', 'icon' => 'fa-cash-register', 'active' => 'sales.pos', 'permission' => 'pos'],
                ['label' => 'Orders', 'route' => 'sales.orders', 'icon' => 'fa-shopping-cart', 'active' => 'sales.orders*|sales.order-details|sales.archived-orders', 'permission' => 'orders'],
                ['label' => 'Customers', 'route' => 'sales.customers', 'icon' => 'fa-users', 'active' => 'sales.customers*|sales.customer-details|sales.customers.*', 'permission' => 'customers'],
                ['label' => 'Customer Debts', 'route' => 'sales.customer-debts', 'icon' => 'fa-hand-holding-usd', 'active' => 'sales.customer-debts', 'permission' => 'customers'],
                ['label' => 'Supplier Debts', 'route' => 'sales.supplier-debts', 'icon' => 'fa-file-invoice', 'active' => 'sales.supplier-debts*', 'permission' => 'suppliers'],
                ['label' => 'Credit Notes', 'route' => 'sales.credit-notes.index', 'icon' => 'fa-receipt', 'active' => 'sales.credit-note*', 'permission' => 'orders'],
                ['label' => 'Returns', 'route' => 'returns.index', 'icon' => 'fa-undo', 'active' => 'returns.*', 'permission' => 'returns'],
            ],
        ],

        'products' => [
            'label' => 'Products',
            'icon'  => 'fa-box',
            'match' => ['products.*', 'product-conversions.*', 'ocr.*'],
            'items' => [
                ['label' => 'Products', 'route' => 'products.index', 'icon' => 'fa-box', 'active' => 'products.index|products.show|products.create|products.edit|products.quick-create|products.bulk-import*', 'permission' => 'products'],
                ['label' => 'Inventory', 'route' => 'products.inventory', 'icon' => 'fa-warehouse', 'active' => 'products.inventory*', 'permission' => 'products'],
                ['label' => 'Receive Stock', 'route' => 'products.receive', 'icon' => 'fa-dolly', 'active' => 'products.receive*', 'permission' => 'products'],
                ['label' => 'Conversions', 'route' => 'product-conversions.index', 'icon' => 'fa-exchange-alt', 'active' => 'product-conversions.*', 'permission' => 'products'],
                ['label' => 'OCR Scan', 'route' => 'ocr.index', 'icon' => 'fa-camera', 'active' => 'ocr.*', 'permission' => 'products'],
            ],
        ],

        'services' => [
            'label' => 'Services',
            'icon'  => 'fa-concierge-bell',
            'match' => ['services.*', 'service-bookings.*'],
            'items' => [
                ['label' => 'Services', 'route' => 'services.index', 'icon' => 'fa-concierge-bell', 'active' => 'services.*', 'permission' => 'services'],
                ['label' => 'Bookings', 'route' => 'service-bookings.index', 'icon' => 'fa-calendar-check', 'active' => 'service-bookings.*', 'permission' => 'services'],
            ],
        ],

        'staff' => [
            'label' => 'Staff',
            'icon'  => 'fa-user-tie',
            'match' => ['staff.*', 'salary-advances.*'],
            'items' => [
                ['label' => 'Staff', 'route' => 'staff.index', 'icon' => 'fa-user-tie', 'active' => 'staff.index|staff.show|staff.create|staff.edit|staff.salary-details', 'permission' => 'staff'],
                ['label' => 'Salary Advances', 'route' => 'salary-advances.index', 'icon' => 'fa-money-check-alt', 'active' => 'salary-advances.*', 'permission' => 'staff'],
                ['label' => 'Salary Calculations', 'route' => 'staff.salary-calculations', 'icon' => 'fa-calculator', 'active' => 'staff.salary-calculations', 'permission' => 'staff'],
            ],
        ],

        'suppliers' => [
            'label' => 'Suppliers',
            'icon'  => 'fa-truck',
            'match' => ['suppliers.*'],
            'items' => [
                ['label' => 'Suppliers', 'route' => 'suppliers.index', 'icon' => 'fa-truck', 'active' => 'suppliers.*', 'permission' => 'suppliers'],
            ],
        ],

        'finance' => [
            'label' => 'Finance',
            'icon'  => 'fa-receipt',
            'match' => ['costs.*', 'tax.*'],
            'items' => [
                ['label' => 'Expenses', 'route' => 'costs.index', 'icon' => 'fa-receipt', 'active' => 'costs.*', 'permission' => 'expenses'],
                ['label' => 'Tax Settings', 'route' => 'tax.settings', 'icon' => 'fa-file-invoice-dollar', 'active' => 'tax.settings', 'permission' => 'expenses'],
                ['label' => 'Tax Reports', 'route' => 'tax.reports', 'icon' => 'fa-chart-bar', 'active' => 'tax.reports|tax.dashboard', 'permission' => 'expenses'],
            ],
        ],

        'reports' => [
            'label' => 'Reports & Analytics',
            'icon'  => 'fa-chart-line',
            'match' => ['reports.*', 'business.analysis.*'],
            'items' => [
                ['label' => 'Analytics', 'route' => 'business.analysis.index', 'icon' => 'fa-chart-line', 'active' => 'business.analysis.index', 'permission' => 'reports'],
                ['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'fa-file-alt', 'active' => 'reports.*', 'permission' => 'reports'],
                ['label' => 'Financials', 'route' => 'business.analysis.financial', 'icon' => 'fa-chart-pie', 'active' => 'business.analysis.financial', 'permission' => 'reports'],
            ],
        ],

        'growth' => [
            'label' => 'Growth',
            'icon'  => 'fa-bullhorn',
            'match' => ['marketing.*', 'website.*', 'testimonials.*'],
            'items' => [
                ['label' => 'Marketing', 'route' => 'marketing.social-media', 'icon' => 'fa-bullhorn', 'active' => 'marketing.*', 'permission' => 'marketing'],
                ['label' => 'Website Builder', 'route' => 'website.builder.index', 'icon' => 'fa-globe', 'active' => 'website.*', 'permission' => 'website'],
                ['label' => 'Reviews', 'route' => 'testimonials.owner.index', 'icon' => 'fa-star', 'active' => 'testimonials.owner.*', 'permission' => 'website'],
            ],
        ],

        'ai' => [
            'label' => 'AI Tools',
            'icon'  => 'fa-robot',
            'match' => ['ai-comm.*', 'ai-content.*'],
            'items' => [
                ['label' => 'AI Assistant', 'route' => 'ai-comm.chat', 'icon' => 'fa-robot', 'active' => 'ai-comm.*', 'permission' => 'ai'],
                ['label' => 'AI Content', 'route' => 'ai-content.index', 'icon' => 'fa-magic', 'active' => 'ai-content.*', 'permission' => 'ai'],
            ],
        ],

    ],
];
