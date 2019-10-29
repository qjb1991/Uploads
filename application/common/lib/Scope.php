<?php
/**
 * Created by Uploads
 * User: bibo
 * Date: 2019-10-24
 * Time: 15:41
 */

namespace app\common\lib;


class Scope
{
    public static function job_api()
    {
        return JOB_SCOPE_API;
    }

    public static function job_admin()
    {
        return JOB_SCOPE_ADMIN;
    }

    public static function file_upload()
    {
        return FILE_SCOPE_UPLOAD;
    }
}