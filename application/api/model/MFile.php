<?php
/**
 * Created by Uploads
 * User: bibo
 * Date: 2019-10-28
 * Time: 15:18
 */

namespace app\api\model;


use app\common\lib\OssUtils;
use app\common\lib\Utils;
use think\Db;
use think\facade\Config;
use think\Model;

class MFile extends Model
{
    protected $table = TABLE_FILE;
    public function saveFile($file, $uid, $scope)
    {
        $src = $file->getInfo(); // 获取 上传 信息
        $save_path = Config::get('upload.upload_save_path');
        $info = $file->move($save_path);
        if ($info) {
            $path = Config::get('upload.upload_domain') . str_replace('\\', '/', $info->getSaveName());
            list($_, $type) = explode('/', $src['type']);
            $save = $save_path . DIRECTORY_SEPARATOR . $info->getSaveName();
            $md5 = md5_file($save);
            $data = Db::table($this->table)
                ->where([
                    'md5' => $md5,
                    'type' => $type
                ])
                ->find();

            if (!empty($data)) {
                $result = Db::table($this->table)
                    ->where('id', $data['id'])
                    ->update(['update_at' => time()]);
                $tmp = DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'upload';
                $_new = $tmp.DIRECTORY_SEPARATOR.$type.'.'.time().'tmp' ;
                if(!IS_WIN) {
                    if(!file_exists($tmp)){
                        mkdir($tmp,0755,true);
                    }
                    rename($save,$_new);// 移动
                }else{
                    unlink($save);
                }
                return $data['access_url'];
            }

            $data = [
                'type' => $type,
                'row_name' => $src['name'],
                'access_url' => $path,
                'scope' => $scope,
                'save_url' => $save,
                'uid' => $uid,
                'create_at' => time(),
                'state' => 1,
                'md5' => md5_file($save),
                'update_at' => time()
            ];
            Db::table($this->table)->insert($data);
            return $path;
        }
    }

    public function saveOssFile($file, $uid, $scope, $oss_type, $type)
    {
        $src = $file->getInfo(); // 获取 上传 信息

        $md5 = md5_file($src['tmp_name']);

        $data = Db::table($this->table)
            ->where([
                'md5' => $md5,
            ])
            ->find();

        if (!empty($data)) {
            Db::table($this->table)
                ->where('id', $data['id'])
                ->update(['update_at' => time()]);

            return $data['access_url'];
        } else {
            $file_name = 'employment/pic/' . Utils::random(5) . time() . '.' . $type;
            $info = OssUtils::getInstance()->uploadFile($file_name, $src['tmp_name'], $oss_type);

            unlink($src['tmp_name']);
            if ($info) {
                $path = $info['info']['url'];
                list($_, $type) = explode('/', $src['type']);

                $data = [
                    'type' => $type,
                    'row_name' => $src['name'],
                    'access_url' => $path,
                    'scope' => $scope,
                    'save_url' => $path,
                    'uid' => $uid,
                    'create_at' => time(),
                    'state' => 1,
                    'md5' => $md5,
                    'update_at' => time()
                ];
                Db::table($this->table)->insert($data);
                return $path;
            }
        }

        return false;

    }
}