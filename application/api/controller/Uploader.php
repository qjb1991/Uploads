<?php

namespace app\api\controller;

use app\api\model\MFile;
use app\api\model\MUploadLog;
use app\api\validate\FileData;
use app\common\controller\BaseApi;
use app\common\lib\Utils;
use think\App;
use think\facade\Config;
use think\response\Json;

class Uploader extends BaseApi
{
    const FILE_UPLOAD = 'file_upload';
    protected $model = null;
    protected $log_model = null;

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        if ($this->model === null) {
            $this->model = new MFile();
        }
        if ($this->log_model === null) {
            $this->log_model = new MUploadLog();
        }

    }

    public function index()
    {
        $access = $this->access();
        if ($access instanceof Json) {
            return $access;
        }

        if (!$access) {
            return $this->response(['code' => CODE_TOKEN_ERROR, 'msg' => '身份校验失败']);
        }

        $info = $this->uploadInit();
        if ($info instanceof Json) {
            return $info;
        }

        if (empty($info->id) || empty($info->time) || empty($info->scope) || empty($info->type)) {
            return $this->response(['code' => CODE_TOKEN_ERROR, 'msg' =>'token异常']);
        }

        if ($info->id !== $access->id) {
            return $this->response(['code' => CODE_TOKEN_ERROR, 'msg' => '身份校验失败2']);
        }

        $file = $this->request->file('file');
        if (empty($file)) {
            return $this->response(['code' => CODE_FILE_ERROR, 'msg' => '请选择上传文件']);
        }

        if (!is_array($file)) {
            $file = [$file];
        }

        try{
            $files = [];
            $types = Config::get('upload.type');
            if (!isset($types[$info->scope][$info->type])) {
                return $this->response(['code' => CODE_FILE_ERROR, 'msg' => '文件类型受限']);
            }

            foreach ($file as $item) {
                list($_, $type) = explode('/', $item->getMime());
                unset($_);
                if (!in_array($type, $types[$info->scope][$info->type])) {
                    continue;
                }

                $data = [
                    'token' => $this->request->header('lc-token'),
                    'app_name' => $this->request->header('lc-app'),
                    'file_info' => json_encode($item->getInfo()),
                    'uid' => $info->id,
                    'create_at' => time(),
                    'type' => $info->type
                ];
                $this->log_model->saveLog($data);

                $file = $this->save($item, $info);
                if (!$file) {
                    return $this->response(['code' => CODE_UPLOAD_FILE, 'msg' => '上传失败']);
                } else {
                    array_push($files, $file);
                }
            }

            if (count($files) === 0) {
                return $this->response(['code' => CODE_UPLOAD_FILE, 'msg' => '上传失败，无处理操作']);
            }

            return $this->response($files);
        } catch (\Exception $e) {
            Utils::exportError($e);

            return $this->response(['code' => CODE_UPLOAD_FILE, 'msg' => '上传失败']);
        }
    }

    public function save($file, $info)
    {
        if (!$file->isValid()) {
            return $this->response(CODE_FILE_ERROR, '非法上传');
        }
        $file = $this->model->saveFile($file, $info->id, $info->scope);

        return $file;
    }

    /**
     * 获取上传令牌
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

    public function uploadInit()
    {
        $token = $this->request->post('token');

        if (empty($token)) {
            return $this->response(['code' => CODE_TOKEN_ERROR, 'msg' => '上传令牌为空']);
        }

        $data = Utils::isAliveToken($token, self::FILE_UPLOAD);
        if (!$data) {
            return $this->response(['code' => CODE_TOKEN_ERROR, 'msg' => '令牌验证失败']);
        }

        return $data;
    }
}
