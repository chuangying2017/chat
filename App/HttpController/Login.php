<?php

namespace App\HttpController;
use App\Model\CustomerModel;
use Composer\{
    Config
};
use EasySwoole\Http\Message\Status;


/**
 * 登录系统
 * Class Register
 * @package App\HttpController
 */
class Login extends Base
{

    function show()
    {
        $this->render('login');
    }

    function verifyLogin()
    {
        $data = $this->request()->getRequestParam();

        $cus = new CustomerModel();

        $res =$cus->where('username',$data['username'],'=')
            ->where('password',base64_encode($data['password']))
            ->getOne();

        if (is_null($res))
        {
           $this->writeJson(Status::CODE_OK,null,'fail');
        }else{
            $result = ['src' => 'http://127.0.0.1:9501/'];
            $_SESSION['user'] = $res;
            $this->writeJson(Status::CODE_OK,$result,'success');
        }
    }

    function logout()
    {
        $src = ['src' => 'http://127.0.0.1:9501/login/show'];

        $_SESSION['user'] = null;

        $this->writeJson(Status::CODE_OK,$src,'success');
    }
}