<?php
/**
 * Created by PhpStorm.
 * User: sunzhenghua
 * Date: 16/8/3
 * Time: 下午6:08
 */

namespace Live\Controller;

use Live\Response;
use Live\Third\Qiniu;

class Upload extends Basic
{
    public function cover($request)
    {
        $this->_upload($request, 'static', 'cover', function ($uid, $img) {
            (new \Live\Database\Live())->updateLive($uid, [
                'cover' => $img
            ]);
        });
    }

    public function avatar($request)
    {
        $this->_upload($request, 'static', 'avatar', function ($uid, $img) {
            (new \Live\Database\User())->updateUser($uid, [
                'avatar' => $img
            ]);
        });
    }

    private function _upload($request, $bucket, $prefix, $cb)
    {
        $data = parent::getValidator()->required('token')->getResult();
        if (!$data)
            return $data;

        if (!isset($request->files))
            return Response::msg('参数错误', 10035);

        $file = current($request->files);
        if (!$file || !$tmp_name = &$file['tmp_name'])
            return Response::msg('参数错误', 10036);

        $token_uid = $data['token_uid'];

        $arr = explode('.', $file['name']);
        $ext = end($arr);

        $name = "{$prefix}/{$token_uid}_" . \Swoolet\App::$ts . ".{$ext}";

        $img = (new Qiniu())->upload($bucket, $tmp_name, $name);
        if (!$img)
            return $img;

        $cb($token_uid, $img);

        return Response::data([
            'img' => $img
        ]);
    }
}