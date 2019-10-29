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

];