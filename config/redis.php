<?php
/**
 * Created by Uploads
 * User: bibo
 * Date: 2019-10-24
 * Time: 14:24
 */

use think\facade\Env;

return [
    'hostname' => Env::get('redis.hostname', '127.0.0.1'),
    'port' => Env::get('redis.port', 6379),
    'dbindex' => Env::get('redis.dbindex', 0),
];