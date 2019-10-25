<?php

namespace app\api\controller;

use app\api\validate\FileData;
use app\common\controller\BaseApi;
use app\common\lib\Utils;
use think\facade\Config;
use think\facade\Response;
use think\response\Json;

class Uploader extends BaseApi
{
    public function index()
    {
        // todo
    }

    /**
     * 获取上传token
     * @return bool|object|\think\response
     */
    public function token()
    {
        // token
        $access = $this->access();
        if ($access instanceof Json) {
            return $access;
        }

        if (!$access) {
            return $this->response(['code' => CODE_TOKEN_ERROR, 'msg' => '身份校验失败']);
        }

        $type = $this->request->post('type');
        $validate = new FileData();
        if (!$validate->hasType($access->scope, $type)) {
            return $this->response(['code' => CODE_TOKEN_ERROR, 'msg' => '类型受限']);
        }

        $token = Utils::uploadTokenSave($access, $type, Config::get('upload.expire_token_time'));

        return $this->response($token['token']);
    }

    /**
     * 用户token验证
     * @return bool|object|\think\response
     */
    public function access()
    {
        $app_name = $this->request->header(Config::get('upload.app_project_name'));
        $project_name = explode(',', Config::get('upload.app_project'));

        if (!in_array($app_name, $project_name)) {
            return $this->response(['code' => CODE_UNKNOWN_APP, 'msg' => MSG_UNKNOWN_APP]);
        }

        $token = $this->request->header(Config::get('upload.app_token_name'));

        if (empty($token)) {
            return $this->response(['code' => CODE_UNKNOWN_AUTH, 'msg' => MSG_UNKNOWN_AUTH]);
        }

        $data = Utils::isAliveToken($token, $app_name);

        return $data;
    }

}
