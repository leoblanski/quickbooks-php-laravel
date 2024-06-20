<?php

defined('SYSPATH') || die('No direct access allowed.');

/**
 * THIS FILE IS NOT THE REAL DATABASE.PHP
 *
 * This is a small snippet to demonstrate adding the
 * qb_data database to your kohana configuration file
 *
 * @author Jayson Lindsley <jay.lindsley@gmail.com>
 *
 * @package QuickBooks
 * @subpackage Documentation
 */

return
[
    'qb_data' =>
    [
        'type'       => 'mysql',
        'connection' => [
            'hostname'   => 'localhost',
            'username'   => 'your_qbapi_username',
            'password'   => 'your_qbapi_password',
            'persistent' => false,
            'database'   => 'qb_data',
        ],
        'table_prefix' => '',
        'charset'      => 'utf8',
        'caching'      => false,
        'profiling'    => false,
    ],
];
