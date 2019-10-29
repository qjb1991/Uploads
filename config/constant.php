<?php
/**
 * Created by Uploads
 * User: bibo
 * Date: 2019-10-23
 * Time: 15:34
 */

define('JOB_SCOPE_API', 'api');     // 小程序API使用
define('JOB_SCOPE_ADMIN', 'admin');     // 后台使用
define('FILE_SCOPE_UPLOAD', 'file_upload');     // 后台使用

define('DEF_ALG', 'HS256');

define('TABLE_UPLOAD_LOG', 'upload_log');
define('TABLE_FILE', 'file');
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);
