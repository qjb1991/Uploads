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
        'file_upload' => Env::get('upload.file_upload_key', ''),
        'job_api' => Env::get('upload.job_api_key', ''),
        'job_admin' => Env::get('upload.job_admin_key', ''),
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
    'expire_token_time' => 3600,

    'upload_domain' => 'http://file.upload.com/',
    'upload_save_path' => dirname(__DIR__).DIRECTORY_SEPARATOR.'upload',

    'oss' => [
        'access_key_id' => Env::get('oss.access_key_id', ''),
        'access_key_secret' => Env::get('oss.access_key_secret', ''),
        'endpoint_net' => Env::get('oss.endpoint_net', ''),

        'pub_bucket_host' => Env::get('oss.pub_bucket_host', ''),
        'pub_bucket_name' => Env::get('oss.pub_bucket_name', ''),
        'pub_endpoint_pir' => Env::get('oss.pub_endpoint_pir', ''),

        'pri_bucket_host' => Env::get('oss.pri_bucket_host', ''),
        'pri_bucket_name' => Env::get('oss.pri_bucket_name', ''),
        'pri_endpoint_pir' => Env::get('oss.pri_endpoint_pir', ''),

        'expire' => 3600
    ]

];