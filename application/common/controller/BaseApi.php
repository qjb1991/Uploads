<?php
/**
 * Created by Uploads
 * User: bibo
 * Date: 2019-10-23
 * Time: 16:10
 */

namespace app\common\controller;


use think\App;
use think\Controller;
use think\facade\Response;

class BaseApi extends Controller
{
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->initialize();
    }

    public function __initialise()
    {
        // todo
    }

    public function response($data, $code = CODE_UNKNOWN_ERROR)
    {
        if ($data instanceof \Exception) {
            return $this->responseJson(null, $code, $data->getMessage());
//            if (Core::getInstance()->isDev()) {
//                return $this->responseJson(null, $code, $data->getMessage());
//            } else {
//                return $this->responseJson(null, $code, SYSTEM_BUSY);
//            }
        } else {
            if (isset($data['code'])) {
                return $this->responseJson(null, $data['code'], $data['msg']);
            } else {
                if ($data === false) {
                    return $this->responseJson(null, $code, MSG_UNKNOWN_ERROR);
                } else {
                    return $this->responseJson($data);
                }
            }
        }
    }

    protected function responseJson($result = null, $statusCode = 200, $msg = 'success')
    {
        $data = Array(
            "code" => $statusCode,
            "msg" => $msg,
            "data" => $result
        );

        return Response::create($data, 'json');
    }
}