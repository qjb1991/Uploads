<?php
/**
 * Created by Uploads
 * User: bibo
 * Date: 2019-10-24
 * Time: 11:57
 */

namespace app\common\lib;


use Firebase\JWT\JWT;
use think\facade\Config;
use think\facade\Log;

class Utils
{
    const API_PASSWORD_SALT = '(*&*&%$#)!';
    const PER_SALT = '*@)_@!';

    public static function exportError(\Exception $e = null, $type = 'error')
    {
        if (empty($e) || !($e instanceof \Exception)) {
            Log::error('unknown error');
        }

        Log::error('error code [ ' . $e->getCode() . '] file: ' . $e->getFile() . ' at line: ' . $e->getLine() . '  error msg : ' . $e->getMessage());
    }

    /**
     * 密码加密 | 验证密码
     * @param $data
     * @param null $password
     * @return bool|string
     */
    public static function password($data, $password = null, $sale = self::API_PASSWORD_SALT)
    {
        if (empty($data)) {
            return false;
        }

        if ($password === null) {
            return md5(md5($data . $sale) . $sale);
        }

        $encode = self::password($data, null, $sale);
        if ($encode && $encode === $password) {
            return true;
        }

        return false;
    }

    /**
     * 获取token并保存
     * @param $data
     * @param $type
     * @param string $scope
     * @param float|int $expire
     * @return mixed
     * @throws \Exception
     */
    public static function tokenSave($data, $type, $scope = SCOPE_API, $expire = DEFAULT_LOGIN_TIME_OUT)
    {
        if (empty($data)) {
            throw new \Exception('用户信息为空');
        }

        if (!is_numeric($expire) || $expire < 1 ) {
            throw new \Exception('有效期设置错误');
        }

        $data['time'] = time() + $expire;
        $data['scope'] = $scope;
        $data['type'] = $type;

        $token = self::token($data, $scope);

        $key = self::getTokenSaveKey($data, $scope);
        $result = RedisUtils::getInstance()->set($key, $token, $expire);
        if (!$result) {
            throw new \Exception('SYSTEM_BUSY');
        }

        $data['token'] = $token;
        return $data;
    }

    /**
     * token 生成
     * @param $data
     * @param $scope
     * @param string $alg
     * @return string
     * @throws \Exception
     */
    public static function token($data, $app_name, $alg = DEF_ALG)
    {
        if (empty($data)) {
            throw new \Exception('空数据');
        }

        $key = Config::get('upload.encode_token_key.' . $app_name);
        Log::info('upload token data ========> ' . var_export($data, true));

        $token = JWT::encode($data, $key, $alg);
        if (!$token) {
            Log::error(var_export(['data' => $data, 'key' => $key, 'alg' => $alg], true));
            throw new \Exception('encode token error');
        }

        return $token;
    }

    /**
     * 解码token
     * @param null $token
     * @param null $key
     * @param array $alg
     * @return bool|object
     */
    public static function verifyToken($token = null, $app_name = null, $alg = array(DEF_ALG))
    {
        if (empty($token) || empty($app_name)) {
            return false;
        }

        $key = Config::get('upload.encode_token_key.' . $app_name);


        try{
            $decode = JWT::decode($token, $key, $alg);

            return $decode;
        } catch (\Exception $e) {
            Log::error('token_error----->' . $e->getMessage());
        }

        return false;
    }

    /**
     * token是否存活
     * @param $token
     * @param $scope
     * @return bool|object
     */
    public static function isAliveToken($token, $app_name)
    {
        $scope = Utils::getAliveTokenScope($app_name);

        $decode = self::verifyToken($token, $app_name);     // 解码token
        if (!$decode) {
            return false;
        }

        if (empty($decode->time) || empty($decode->scope) || empty($decode->type) || empty($decode->id)) {
            return false;
        }

        if ($decode->time < time()) {
            return false;
        }

        $key = self::getTokenSaveKey(['id' => $decode->id, 'type' => $decode->type], $scope);
        $_token = RedisUtils::getInstance()->get($key);

        $_token = unserialize($_token);
        if (!$_token || $_token !== $token) {
            return false;
        }

        return $decode;
    }

    public static function getAliveTokenScope($app_name)
    {
        return Scope::$app_name();
    }

    /**
     * redis 存token的key
     * @param $data
     * @param $scope
     * @return string
     */
    public static function getTokenSaveKey($data, $scope)
    {
        return $scope . '_' . $data['type'] . '_' . md5($data['id'] . $data['type']);
    }

    /**
     * 后台管理员权限缓存key
     * @param $id
     * @return string
     */
    public static function managerPermissionSaveKey($id)
    {
        return 'permission_' . md5(md5($id . self::PER_SALT) . self::PER_SALT);
    }

    /**
     * 文件上传token
     * @param $user
     * @param $type
     * @param $expire
     * @return array|\Exception
     */
    public static function uploadTokenSave($user, $type, $expire)
    {
        try{
            if (empty($user)) {
                throw new \Exception('用户信息为空');
            }

            if (!is_numeric($expire) || $expire < 1 ) {
                throw new \Exception('有效期设置错误');
            }

            $data = [
                'id' => $user->id,
                'time' => time() + $expire,
                'scope' => $user->scope,
                'type' => $type     // 此type不是用户token中的type， 这里的type是文件上传的类型，例如：image， video， document
            ];

            $token = self::token($data, 'file_upload');
            $key = self::getTokenSaveKey($data, 'file_upload');
            $result = RedisUtils::getInstance()->set($key, serialize($token), $expire);
            if (!$result) {
                throw new \Exception('SYSTEM_BUSY');
            }

            $data['token'] = $token;
            return $data;
        } catch (\Exception $e) {
            self::exportError($e);

            return $e;
        }
    }
}