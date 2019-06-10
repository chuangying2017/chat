<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/10
 * Time: 9:52
 */

namespace App\WebSocket\Controller;




use App\Task\CustomerTask;
use App\WebSocket\Actions\Broadcast\BroadcastMessage;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;

class Customer extends Controller
{
    //单对单 发送给客户端
    public function sendPersonal()
    {
        /** @var WebSocketClient $client */
        $client = $this->caller()->getClient();
        $broadcastPayload = $this->caller()->getArgs();
        if (!empty($broadcastPayload) && isset($broadcastPayload['content'])) {
            $message = new BroadcastMessage();
            $message->setFromUserFd($client->getFd());
            $message->setContent($broadcastPayload['content']);
            $message->setType($broadcastPayload['type']);
            $message->setSendTime(date('Y-m-d H:i:s'));
            TaskManager::async(new CustomerTask([
                'payload' => $message->__toString(),
                'fromFd' => $client->getFd(),
                'toCustomer' => [
                    'customer_id' => $broadcastPayload['toUserFd'],
                    'username' => $broadcastPayload['username']
                ]
            ]));
        }
        $this->response()->setStatus($this->response()::STATUS_OK);
    }
}