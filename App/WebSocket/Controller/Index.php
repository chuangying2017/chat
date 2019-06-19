<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:19
 */

namespace App\WebSocket\Controller;

use App\Obtain\TempUserGet;
use App\Storage\OnlineUser;
use App\Storage\SaveMessage;
use App\Task\CustomerTask;
use App\Utility\Gravatar;
use App\WebSocket\Actions\Broadcast\BroadcastAdmin;
use App\WebSocket\Actions\User\UserInfo;
use App\WebSocket\Actions\User\UserInRoom;
use App\WebSocket\Actions\User\UserOnline;
use App\WebSocket\Actions\User\UserUpdate;
use App\WebSocket\WebSocketAction;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
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
        if (isset($args['number']) && !empty($args['number'])) {
            $number = $args['number'];
            $avatar = Gravatar::makeGravatar($number . '@swoole.com');
        } else {
            $random = Random::character(8);
            $avatar = Gravatar::makeGravatar($random . '@swoole.com');
            $number = 'KF_' . $random;
        }

        OnlineUser::getInstance()->set($fd, $number, $avatar,$args['name']??null);

        $tempData = TempUserGet::getInstance()->GetTempClientList($number);

        if (is_array($tempData))
        {
            foreach ($tempData as $k => $v)
            {
                $server->push($fd,json_encode(['action' => WebSocketAction::USER_IN_ROOM,'info' => $v],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            }
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
        $info = $OnlineUser->get($fd);

        if ($args['name'] && $info)
        {
            $info['name'] = $args['name'];

            $OnlineUser->table()->set($fd,$info);


            $userUpdate = new UserUpdate();

            $userUpdate->setFd($fd);

            $userUpdate->setInfo($info);

            $this->response()->setMessage($userUpdate);
        }

    }
}