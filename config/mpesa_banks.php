<?php

/*
 |--------------------------------------------------------------------------
 | Bank paybills for M-Pesa → bank account deposits
 |--------------------------------------------------------------------------
 | These are public Lipa na M-Pesa paybills operated by each bank that
 | accept deposits to any account number (passed as AccountReference).
 | Verify each shortcode before enabling for a client — banks occasionally
 | add/change paybill numbers.
 */
return [

    'equity' => [
        'name'    => 'Equity Bank',
        'paybill' => '247247',
    ],

    'kcb' => [
        'name'    => 'KCB Bank',
        'paybill' => '522522',
    ],

    'coop' => [
        'name'    => 'Co-operative Bank',
        'paybill' => '400200',
    ],

    'absa' => [
        'name'    => 'Absa Bank',
        'paybill' => '303030',
    ],

    'family' => [
        'name'    => 'Family Bank',
        'paybill' => '222111',
    ],

    'ncba' => [
        'name'    => 'NCBA Bank',
        'paybill' => '880100',
    ],

    'dtb' => [
        'name'    => 'Diamond Trust Bank',
        'paybill' => '516600',
    ],

    'stanbic' => [
        'name'    => 'Stanbic Bank',
        'paybill' => '600100',
    ],

    'scb' => [
        'name'    => 'Standard Chartered',
        'paybill' => '329329',
    ],

    'im' => [
        'name'    => 'I&M Bank',
        'paybill' => '542542',
    ],

];
