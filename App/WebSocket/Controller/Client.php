<?php


namespace App\WebSocket\Controller;

use App\Storage\OnlineUser;
use App\Storage\SaveMessage;
use App\Task\CustomerTask;
use App\WebSocket\Actions\Broadcast\BroadcastClient;
use App\WebSocket\Actions\User\UserInfo;
use App\WebSocket\Actions\User\UserInRoom;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;
class Client extends Base
{

    /**
     * 初始化 客户端 第一次请求 用户上线啦
     *
     */
    public function info()
    {
        /** @var WebSocketClient $client */
        $args = $this->caller()->getArgs();
        $client = $this->caller()->getClient();
        $fd = $client->getFd();

        $server = ServerManager::getInstance()->getSwooleServer();

        $client = $args['client'];

        $customer = $args['customer'];

        OnlineUser::getInstance()->set($fd, $client['number'], $client['avatar']);

        if (isset($customer['name']) && !empty($customer['name']))
        {
            $name = $customer['name'];
        }else{
            $name = $customer['number'];
        }

        //第一次给客户端推送消息
        $BroadcastClient = new BroadcastClient();
        $BroadcastClient->setCustomerId($customer['customer_id']);
        $BroadcastClient->setNumber($customer['number']);
        $BroadcastClient->setName($name);
        $BroadcastClient->setContent("您好, 客服  {$name} 很高兴为您服务!");
        $server->push($fd,$BroadcastClient->__toString());

        $clientData = ['fd' => $fd, 'avatar' => $client['avatar'], 'number' => $client['number']];
        $userInRoomMessage = new UserInRoom();
        $userInRoomMessage->setInfo($clientData);
        SaveMessage::getInstance()->saveRedisCustomer($customer['number'], $clientData);

        //推送给客服 如果不在线 推送离线消息 第一次
        TaskManager::async(new CustomerTask([
            'payload' => $userInRoomMessage->__toString(),
            'fromFd' => $fd,
            'toCustomer' =>
                [
                    'number' => $customer['number'],
                    'customer_id' => $customer['customer_id']
                ]
        ]));
        $message = new UserInfo();
        $message->setIntro('欢迎使用 即时通讯');
        $message->setUserFd($fd);
        $message->setAvatar($client['avatar']);
        $message->setNumber($client['number']);
        $this->response()->setMessage($message);
    }
}