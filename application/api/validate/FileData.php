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
    protected $rule = [
        'name'  =>  'require|max:25',
        'email' =>  'email',
    ];

    protected $message  =   [
        'name.require' => '名称必须',
        'name.max'     => '名称最多不能超过25个字符',
        'age.number'   => '年龄必须是数字',
        'age.between'  => '年龄只能在1-120之间',
        'email'        => '邮箱格式错误',
    ];

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