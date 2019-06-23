<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:19
 */

namespace App\WebSocket\Controller;

use App\Config\CustomerConfig;
use App\Model\CustomerModel;
use App\Obtain\TempUserGet;
use App\Storage\OnlineUser;
use App\Utility\Gravatar;
use App\WebSocket\Actions\User\UserInfo;
use App\WebSocket\Actions\User\UserOnline;
use App\WebSocket\Actions\User\UserUpdate;
use App\WebSocket\Actions\User\UserUpdateAvatar;
use App\WebSocket\WebSocketAction;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;
use EasySwoole\Utility\Random;
use Exception;


class Index extends Base
{
    /**
     * 当前用户信息
     * 生成用户信息的同时 给用户随机选择 一位客服
     * 用户第一次进来注册 信息到内存

     */
    function info()
    {
        /** @var WebSocketClient $client */
            $args = $this->caller()->getArgs();
            $client = $this->caller()->getClient();
            $fd = $client->getFd();

            $server = ServerManager::getInstance()->getSwooleServer();

            $number = $args['number'];//客服编号

            if (!empty($args['avatar']))
            {
                $avatar = $args['avatar'];
            }else{
                $avatar = Gravatar::makeGravatar($number . '@swoole.com');
            }

            OnlineUser::getInstance()->set($fd, $number, $avatar,$args['name']??null);


            $tempData = TempUserGet::getInstance()->GetTempClientList($number);


            if (is_array($tempData))
            {

                $server->push($fd,json_encode(['action' => WebSocketAction::USER_IN_ROOM_LIST,'info' => $tempData],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));

            }


            $message = new UserInfo;

            $message->setIntro('欢迎使用 即时通讯');

            $message->setUserFd($fd);

            $message->setAvatar($avatar);

            $message->setNumber($number);

            $this->response()->setMessage($message);
    }

    /**
     * 在线用户列表
     * @throws Exception
     */
    function online()
    {
        $table = OnlineUser::getInstance()->table();
        $users = array();

        foreach ($table as $user) {
            $users['user' . $user['fd']] = $user;
        }

        if (!empty($users)) {
            $message = new UserOnline;
            $message->setList($users);
            $this->response()->setMessage($message);
        }
    }

    function heartbeat()
    {
        $this->response()->setMessage('PONG');
    }

    function updateCustomerName()
    {
        /** @var WebSocketClient $client */
        $args = $this->caller()->getArgs();
        $client = $this->caller()->getClient();
        $fd = $client->getFd();
        $OnlineUser = OnlineUser::getInstance();


        $info = $OnlineUser->get($args['number']);

        if ($args['name'] && $info)
        {
            $info['name'] = $args['name'];

            $OnlineUser->table()->set($args['number'],$info);


            $userUpdate = new UserUpdate();

            $userUpdate->setFd($fd);

            $userUpdate->setInfo($info);

            $this->response()->setMessage($userUpdate);
        }

    }

    function updateAvatar()
    {
        /** @var WebSocketClient $client */
        $args = $this->caller()->getArgs();
        $client = $this->caller()->getClient();
        $fd = $client->getFd();
        $OnlineUser = OnlineUser::getInstance();

        $info  = $OnlineUser->get($args['number']);

        if (isset($args['avatar']) && !empty($args['avatar']))
        {
            $info['avatar'] = $args['avatar'];

            $OnlineUser->table()->set($args['number'],$info);

            $avatarUpdate = new UserUpdateAvatar();

            $avatarUpdate->setFd($fd);

            $avatarUpdate->setInfo($info);

            $this->response()->setMessage($avatarUpdate);

            CustomerModel::getInstance()->updateData($args['number'], ['avatar' => $args['avatar']]);

        }
    }
}