<?php
/**
 * config.local
 * 开发分支-develop
 *
 * @author : Cyw
 * @email  : rose2099.c@gmail.com
 * @created: 16/6/9 下午4:20
 * @logs   :
 *
 */

if (!defined('RPC_IP')) {
    define('RPC_IP', '192.168.18.240');
}

if (!defined('RPC_PORT')) {
    define('RPC_PORT', '7702');
}

return new \Phalcon\Config([
    'debug'            => true,
    'databases'        => [
        'db_config'    => [
            'host'     => '192.168.18.240',
            'username' => 'root',
            'password' => '123456',
            'port'     => '3309',
            'dbname'   => 'db_config',
            'options'  => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]
        ],
        'db_live'      => [
            'host'     => '192.168.18.240',
            'username' => 'root',
            'password' => '123456',
            'port'     => '3309',
            'dbname'   => 'db_live',
            'options'  => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]
        ],
        'db_user'      => [
            'host'     => '192.168.18.240',
            'username' => 'root',
            'password' => '123456',
            'port'     => '3309',
            'dbname'   => 'db_user',
            'options'  => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]
        ],
        'db_user_temp' => [
            'host'     => '192.168.18.240',
            'username' => 'root',
            'password' => '123456',
            'port'     => '3309',
            'dbname'   => 'db_user',
            'options'  => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]
        ],
        'db_log'       => [
            'host'     => '192.168.18.240',
            'username' => 'root',
            'password' => '123456',
            'port'     => '3309',
            'dbname'   => 'db_log',
            'options'  => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]
        ],
        'db_log_read'  => [
            'host'     => '192.168.18.240',
            'username' => 'root',
            'password' => '123456',
            'port'     => '3309',
            'dbname'   => 'db_log',
            'options'  => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]
        ],
        'db_navy'      => [
            'host'     => '192.168.18.240',
            'username' => 'root',
            'password' => '123456',
            'port'     => '3309',
            'dbname'   => 'db_navy',
            'options'  => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]
        ],
        'db_rich'      => [
            'host'     => '192.168.18.240',
            'username' => 'root',
            'password' => '123456',
            'port'     => '3309',
            'dbname'   => 'db_rich',
            'options'  => [
                PDO::ATTR_EMULATE_PREPARES  => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        ],
        'db_rich_read' => [
            'host'     => '192.168.18.240',
            'username' => 'root',
            'password' => '123456',
            'port'     => '3309',
            'dbname'   => 'db_rich',
            'options'  => [
                PDO::ATTR_EMULATE_PREPARES  => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        ],
        'db_active'    => [
            'host'     => '192.168.18.240',
            'username' => 'root',
            'password' => '123456',
            'port'     => '3309',
            'dbname'   => 'db_active',
            'options'  => [
                PDO::ATTR_EMULATE_PREPARES  => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        ]
    ],
    //redis
    'redis'            => [
        'User'    => [
            'host'     => '192.168.18.240',
            'port'     => 6382,
            'readHost' => '192.168.18.240',
            'readPort' => 6382
        ],
        'Cache'   => [
            'host' => '192.168.18.240',
            'port' => 6382,
        ],
        'Session' => [
            'host' => '192.168.18.240',
            'port' => 6382,
        ],
        'Storage' => [
            'host' => '192.168.18.240',
            'port' => 6382,
        ],
        'Task'    => [
            'host' => '192.168.18.240',
            'port' => 6382,
        ]
    ],
    // 表结构缓存设置
    'DATA_CACHE_META'  => [
        'adapter'    => 'Redis',
        'host'       => '192.168.18.240',
        'port'       => 6382,
        'persistent' => false,
        'prefix'     => 'SYMETA',
        'lifetime'   => 86400
    ],
    // 数据库查询缓存设置
    'DATA_CACHE_MODEL' => [
        'host'       => '192.168.18.240',
        'port'       => 6382,
        'timeout'    => 1,
        'persistent' => false
    ],
    'go-gateway'       => [
        '192.168.18.240:8888',
        '192.168.18.240:8888'
    ],
    'go-push'          => [
        '192.168.18.240:8800',
        '192.168.18.240:8800'
    ],
    // 七牛账号信息
    'Qiniu'            => [
        'ACCESS_KEY' => 'x',
        'SECRET_KEY' => 'y',
    ],
    'logPath'          => BASE_PATH . '/log/'
]);
