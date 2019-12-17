<?php
/**
 * Created by Uploads
 * User: bibo
 * Date: 2019-10-25
 * Time: 14:14
 */

namespace app\api\validate;



use think\facade\Config;
use think\Validate;

class FileData extends Validate
{
//    public $rule = [
//        'ext' => 'fileExt:jpg,png',
//        'fileSize' => 'max:1M'
//    ];
//
//    public $scene = [
//        'image' => [
//            'ext',
//            'fileSize'
//        ],
//    ];
//
//    public $msg = [
//        'ext' => '上传文件类型不符合要求',
//        'fileSize' => '上传文件过大',
//    ];

    /**
     * 类型判断
     * @param $scope
     * @param $type
     * @return bool
     */
    public function hasType($scope, $type)
    {
        $types = Config::get('upload.type.' . $scope);

        return in_array($type, array_keys($types));
    }
}