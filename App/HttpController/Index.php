<?php

namespace App\HttpController;

use App\Model\SessionRecord;
use App\Utility\ReverseProxyTools;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;

/**
 * Class Index
 * @package App\HttpController
 */
class Index extends Base
{
    function index()
    {

        if (empty($_SESSION['user']))
        {
            $src = 'http://127.0.0.1:9501/login/show';
        }else{
            $src = null;
        }

        $hostName = $this->cfgValue('WEBSOCKET_HOST', 'ws://127.0.0.1:9501');
        $this->render('index', [
            'server' => $hostName,
            'src' => $src,
            'ke' => true
        ]);
    }

    function randIndex()
    {
        $hostName = $this->cfgValue('WEBSOCKET_HOST', 'ws://127.0.0.1:9501');
        $this->render('index', [
            'server' => $hostName,
            'src' => false,
            'ke' => false
        ]);
    }


    function test()
    {
        try {
            $date = date('Y-m-d H:i:s');
            $data = [
                'client_number' => uniqid('KF') . random_int(000, 999),
                'content' => '测试数据',
                'customer_id' => 3,
                'created_at' => $date,
                'updated_at' => $date
            ];

            $db = SessionRecord::getInstance();

            $res = $db->insert($data);

            var_dump($res);

        } catch (\Exception $e) {
            var_dump($e->getMessage());
        } catch (\Throwable $e) {
            var_dump($e->getMessage());
        }
    }

    function testRedirect()
    {
        $this->response()->redirect('/login/show');
    }
}
