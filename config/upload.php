<?php
/**
 * Created by Uploads
 * User: bibo
 * Date: 2019-10-23
 * Time: 15:05
 */

use think\facade\Env;

return [
    'app_project_name' => Env::get('upload.app_project_name', 'x-app'),
    'app_project' => Env::get('upload.app_project', ''),

    'app_token_name' => Env::get('upload.app_token_name', 'x-token'),

    // 解码token的key
    'encode_token_key' => [
        'file_upload' => 'bTsxS2NeMFNwKzhKZ0A2Tg==',    // 上传的key
        'job_api' => 'TXpFQV5Ua0VDOCpKcUkqTQ==',
        'job_admin' => 'Y2xnRSpSaXNDRU1RYiQkMQ=='
    ],

    // 上传类型限制
    'type' => [
        'api' => [
            'image' => [
                'png',
                'jpg',
                'jpeg',
                'bmp',
            ]
        ],
    ],

    // 上传token有效时间
    'expire_token_time' => 3600.

//    'app_token_key' => Env::get('upload.token_key', 'this_is_token_key'),
//    'upload_domain' => Env::get('upload.upload_domain', 'http://file.eaimin.com/'),
//    'upload_save_path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'upload',
//    'domain' => Env::get('upload.upload_domain', 'http://file.4cgame.com'),
//
//    'allow_hosts' => [
//        'http://sdk.4cgame.com', 'http://api.4cgame.com',
//        'http://admin.4cgame.com', 'http://oa.4cgame.com',
//        'http://www.tp5.com', 'http://eaimin.com'
//    ]
];