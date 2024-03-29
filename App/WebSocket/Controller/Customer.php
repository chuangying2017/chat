<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/10
 * Time: 9:52
 */

namespace App\WebSocket\Controller;




use App\Obtain\TempUserGet;
use App\Storage\SaveMessage;
use App\Task\CustomerTask;
use App\WebSocket\Actions\Broadcast\BroadcastMessage;
use App\WebSocket\Actions\User\UserDeleteSession;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;


class Customer extends Base
{
    //单对单 发送给客户端 需要区分 是客服 还是 客户 00 20196
    /**
     * @throws \Exception
     */
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

            $info = $this->currentUser();

            if (!empty($info))
            {
                $message->setNumber($info['number']);
                $message->setAvatar($info['avatar']);
                $message->setSend($info['number']);
                $message->setName($info['name']);
            }

            $message->setAccept($broadcastPayload['number']);

            $message->setMasterId($broadcastPayload['masterId']);


            TaskManager::async(new CustomerTask([
                'payload' => $message->__toString(),
                'fromFd' => $client->getFd(),
                'mode'  => isset($broadcastPayload['mode']) ? $broadcastPayload['mode'] : false,
                'toCustomer' => [
                    'customer_id' => $broadcastPayload['toUserFd'],
                    'number' => $broadcastPayload['number']
                ],
                'number' => $broadcastPayload['number']
            ]));
        }
        $this->response()->setStatus($this->response()::STATUS_OK);
    }

    /**
     * delete session record
     */
    public function deleteSessionRecord()
    {

        $broadcastPayload = $this->caller()->getArgs();

        if (!empty($broadcastPayload) && isset($broadcastPayload['client_number']))
        {
            $getInstance = SaveMessage::getInstance();

            $userDeleteSession = new UserDeleteSession();

            $userDeleteSession->setClientNumber($broadcastPayload['client_number']);

            $customerData = TempUserGet::getInstance()->GetTempClientList($broadcastPayload['customer_number']);

            if (!empty($customerData))
            {

                $user_client_number = 'user' . $broadcastPayload['client_number'];

                if (isset($customerData[$user_client_number])) unset($customerData[$user_client_number]);

                $getInstance->setCustomerRoomUser($broadcastPayload['customer_number'],$customerData);

            }

            $this->response()->setMessage($userDeleteSession);
        }

        $this->response()->setStatus($this->response()::STATUS_OK);
    }
}