<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Business Module Permissions
    |--------------------------------------------------------------------------
    | 
    | These are the modules that can be granted to team members.
    | The key is the slug used in permission checks, the value is the label.
    |
    */

    'modules' => [
        'pos'       => ['label' => 'Point of Sale',        'icon' => 'fas fa-cash-register',  'color' => '#10b981'],
        'products'  => ['label' => 'Products & Inventory', 'icon' => 'fas fa-boxes',          'color' => '#3b82f6'],
        'orders'    => ['label' => 'Orders',               'icon' => 'fas fa-shopping-cart',  'color' => '#f59e0b'],
        'customers' => ['label' => 'Customers',            'icon' => 'fas fa-users',          'color' => '#06b6d4'],
        'suppliers' => ['label' => 'Suppliers',            'icon' => 'fas fa-truck',          'color' => '#64748b'],
        'returns'   => ['label' => 'Returns & Refunds',    'icon' => 'fas fa-undo',           'color' => '#ef4444'],
        'services'  => ['label' => 'Services & Bookings',  'icon' => 'fas fa-concierge-bell', 'color' => '#34d399'],
        'staff'     => ['label' => 'Staff & HR',           'icon' => 'fas fa-user-tie',       'color' => '#8b5cf6'],
        'expenses'  => ['label' => 'Expenses & Costs',     'icon' => 'fas fa-wallet',         'color' => '#f97316'],
        'reports'   => ['label' => 'Reports & Analytics',  'icon' => 'fas fa-chart-bar',      'color' => '#020258'],
        'marketing' => ['label' => 'Marketing',            'icon' => 'fas fa-bullhorn',       'color' => '#ec4899'],
        'website'   => ['label' => 'Website Builder',      'icon' => 'fas fa-globe',          'color' => '#6366f1'],
        'ai'        => ['label' => 'AI Tools',             'icon' => 'fas fa-robot',          'color' => '#13e8e9'],
        'settings'  => ['label' => 'Business Settings',    'icon' => 'fas fa-cog',            'color' => '#6b7280'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Permissions per Role
    |--------------------------------------------------------------------------
    */
    'role_defaults' => [
        'admin'   => ['pos','products','orders','customers','suppliers','returns','services','staff','expenses','reports','marketing','website','ai','settings'],
        'manager' => ['pos','products','orders','customers','suppliers','returns','services','staff','expenses','reports'],
        'cashier' => ['pos','orders','customers'],
        'staff'   => ['pos','orders'],
        'viewer'  => ['reports'],
    ],

    'role_labels' => [
        'admin'   => 'Administrator',
        'manager' => 'Manager',
        'cashier' => 'Cashier',
        'staff'   => 'Staff',
        'viewer'  => 'Viewer (Read-only)',
    ],
];
