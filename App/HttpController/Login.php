<?php

namespace App\HttpController;
use Composer\{
    Config
};

/**
 * 登录系统
 * Class Register
 * @package App\HttpController
 */
class Login extends Base
{
    function index()
    {
        $this->render('login');
    }
}