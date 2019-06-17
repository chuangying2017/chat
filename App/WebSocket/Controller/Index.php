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

        OnlineUser::getInstance()->set($fd, $number, $avatar);

        //如果是客服 就不需要发送信息过来
        if (!isset($args['number']) || empty($args['number']))
        {
            //有客服来咨询
            $clientData = ['fd' => $fd, 'avatar' => $avatar, 'number' => $number];
            $userInRoomMessage = new UserInRoom();
            $userInRoomMessage->setInfo($clientData);
            //没有获取到连接 自动去获取随机 客服

                // 发送欢迎消息给用户 抽取随机客服给用户发信息
                $broadcastAdminMessage = new BroadcastAdmin();
                // $broadcastAdminMessage->setContent("{$number}，欢迎到来 你好 请问有什么可以帮助到你！");
                $broadcastAdminMessage->setRandomCustomer();
                //  $server->push($fd, $broadcastAdminMessage->__toString());

                $broadcastAdminMessage->setContent("编号{$broadcastAdminMessage->getNumber()},很高兴为您服务");
                $server->push($fd, $broadcastAdminMessage->__toString());
                //临时用户保存在redis 最多20个
                SaveMessage::getInstance()->saveRedisCustomer($broadcastAdminMessage->getNumber(), $clientData);

                //推送给客服 如果不在线 推送离线消息 第一次
                TaskManager::async(new CustomerTask([
                    'payload' => $userInRoomMessage->__toString(),
                    'fromFd' => $fd,
                    'toCustomer' =>
                        [
                            'number' => $broadcastAdminMessage->getnumber(),
                            'customer_id' => $broadcastAdminMessage->getCustomerId()
                        ]
                ]));
                //$olineUser = OnlineUser::getInstance()->table();

                //var_dump($olineUser->get(1));
                // 提取最后10条消息发送给用户
        /*        $lastMessages = ChatMessage::getInstance()->readMessage();
                $lastMessages = array_reverse($lastMessages);
                if (!empty($lastMessages)) {
                    foreach ($lastMessages as $message) {
                        $server->push($fd, $message);
                    }
                }*/


        }else{
           $tempData = TempUserGet::getInstance()->GetTempClientList($number);

           if (is_array($tempData))
           {
               foreach ($tempData as $k => $v)
               {
                   $server->push($fd,json_encode(['action' => 203,'info' => $v],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
               }
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
}