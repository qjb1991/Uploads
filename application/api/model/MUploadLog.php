<?php
/**
 * Created by Uploads
 * User: bibo
 * Date: 2019-10-28
 * Time: 17:34
 */

namespace app\api\model;


use think\Db;
use think\Model;

class MUploadLog extends Model
{
    protected $table = TABLE_UPLOAD_LOG;

    public function saveLog($data)
    {
        $result = Db::table($this->table)->insert($data);
        return $result;
    }
}