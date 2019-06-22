<?php

namespace App\WebSocket;

use App\Config\CustomerConfig;
use App\Obtain\TempUserGet;
use App\Storage\ChatMessage;
use App\Storage\OnlineUser;
use App\Storage\SaveMessage;
use App\Task\BroadcastTask;
use App\Task\CustomerTask;
use App\Task\OutTask;
use App\Utility\Gravatar;
use App\WebSocket\Actions\Broadcast\BroadcastAdmin;
use App\WebSocket\Actions\User\UserInRoom;

use App\WebSocket\Actions\User\UserOutRoom;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Utility\Random;

use \swoole_server;
use \swoole_websocket_server;
use \swoole_http_request;
use \Exception;

/**
 * WebSocket Events
 * Class WebSocketEvents
 * @package App\WebSocket
 */
class WebSocketEvents
{
    /**
     * 打开了一个链接
     * @param swoole_websocket_server $server
     * @param swoole_http_request $request
     */
    static function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {

/*        // 为用户分配身份并插入到用户表
        $fd = $request->fd;

        if (isset($request->get['username']) && !empty($request->get['username'])) {
            $username = $request->get['username'];
            $avatar = Gravatar::makeGravatar($username . '@swoole.com');
        } else {
            $random = Random::character(8);
            $avatar = Gravatar::makeGravatar($random . '@swoole.com');
            $username = 'KF_' . $random;
        }


        var_dump('here get method!');

        // 插入在线用户表
        OnlineUser::getInstance()->set($fd, $username, $avatar);

        var_dump('已经插入到了在线用户表');
        //有客服来咨询
        $userInRoomMessage = new UserInRoom;
        $userInRoomMessage->setInfo(['fd' => $fd, 'avatar' => $avatar, 'username' => $username]);

        //没有获取到连接 自动去获取随机 客服
        if (empty($request->get['is_reconnection']) || $request->get['is_reconnection'] == '0') {

            // 发送欢迎消息给用户 抽取随机客服给用户发信息
            $broadcastAdminMessage = new BroadcastAdmin;
           // $broadcastAdminMessage->setContent("{$username}，欢迎到来 你好 请问有什么可以帮助到你！");
            $broadcastAdminMessage->setRandomCustomer();
          //  $server->push($fd, $broadcastAdminMessage->__toString());

            $broadcastAdminMessage->setContent("编号{$broadcastAdminMessage->getUsername()},很高兴为您服务");
            $server->push($fd, $broadcastAdminMessage->__toString());
            //推送给客服 如果不在线 推送离线消息 第一次
            TaskManager::async(new CustomerTask([
                'payload' => $userInRoomMessage->__toString(),
                'fromFd' => $fd,
                'toCustomer' =>
                    [
                        'username' => $broadcastAdminMessage->getUsername(),
                        'customer_id' => $broadcastAdminMessage->getCustomerId()
                    ]
                ]));
            //$olineUser = OnlineUser::getInstance()->table();

            //var_dump($olineUser->get(1));
            // 提取最后10条消息发送给用户
            $lastMessages = ChatMessage::getInstance()->readMessage();
            $lastMessages = array_reverse($lastMessages);
            if (!empty($lastMessages)) {
                foreach ($lastMessages as $message) {
                    $server->push($fd, $message);
                }
            }

        }else{
            // 发送广播告诉频道里的用户 有新用户上线

            TaskManager::async(new CustomerTask(['payload' => $userInRoomMessage->__toString(), 'fromFd' => $fd]));
        }

        */
    }

    /**
     * 链接被关闭时
     * @param swoole_server $server
     * @param int $fd
     * @param int $reactorId
     * @throws Exception
     */
    static function onClose(\swoole_server $server, int $fd, int $reactorId)
    {
        $info = $server->connection_info($fd);
        if (isset($info['websocket_status']) && $info['websocket_status'] !== 0) {
            $OnlineUser = OnlineUser::getInstance();
            //获取用户信息

            $arr = TempUserGet::getInstance()->GetTempClientList(CustomerConfig::ONLINE_CLIENT);

            if (is_array($arr))
            {
                $res = array_search($fd,array_keys($arr));

                if (!is_bool($res))
                {
                    $number = $arr[$fd];

                    unset($arr[$fd]);

                    SaveMessage::getInstance()->setOnlineClient(CustomerConfig::ONLINE_CLIENT,$arr);

                    $userInfo = $OnlineUser->get($number);

                    $OnlineUser->delete($number);

                    $message = new UserOutRoom;

                    $message->setUserFd($fd);

                    $message->setNumber($number);

                    $message->setCustomerNumber($userInfo['customer_number']);

                    TaskManager::async(new OutTask(['payload' => $message->__toString(), 'fromFd' => $fd]));
                }

            }

        }
    }
}
