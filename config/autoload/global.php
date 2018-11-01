<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
//    'db' => array(
//        'driver'         => 'Pdo',
//        'dsn'            => 'mysql:dbname=etao_v4;host=127.0.0.1',
//        'driver_options' => array(
//            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
//        ),
//     ),
/*
 * 考虑到信息安全，将数据库连接的帐户信息独立在文件local.php中，
 * 并将local.php设置为gitignor，确保其不会被上传到GITHUB中
 */
    'db' => [
        'adapters' => [
            'Application\Db\WriteAdapter' => [
                'driver' => 'Pdo',
                'dsn' => 'mysql:dbname=etao_v4;host=127.0.0.1;charset=utf8',
            ],
            'Application\Db\ReadOnlyAdapter' => [
                'driver' => 'Pdo',
                'dsn' => 'mysql:dbname=etao_v4;host=127.0.0.1;charset=utf8',
            ],
        ],
    ],
];
