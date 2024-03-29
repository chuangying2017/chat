<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/10
 * Time: 6:57
 */

namespace App\Task;


use App\FilterLogic\Filter;
use App\Storage\OnlineUser;
use App\Storage\SaveMessage;
use App\WebSocket\WebSocketAction;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

class CustomerTask extends AbstractAsyncTask
{

    protected function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        // TODO: Implement run() method.
        $server = ServerManager::getInstance()->getSwooleServer();

        $payload = json_decode($taskData['payload'], true);

        $customer = $taskData['toCustomer'];

        foreach (OnlineUser::getInstance()->table() as $userFd => $userInfo) {
            $connection = $server->connection_info($userInfo['fd']);
            if ($connection['websocket_status'] == 3) {  // 客服正常在线时可以进行消息推送
                if ($userInfo['number'] == $customer['number'])
                {
                    $server->push($userInfo['fd'], $taskData['payload']);
                }elseif($userInfo['fd'] == $taskData['fromFd'])
                {
                    $server->push($userInfo['fd'], $taskData['payload']);
                }

            }
        }

            if (isset($taskData['mode']) && $taskData['mode'] == 'customer') $customer['mode'] = 'accept';


            if (isset($payload['masterId']))
            {
                $customer['client_number'] = $payload['masterId'];

                $customer['type'] = isset($payload['type']) && $payload['type'] == 'image' ? 'image' : 'msg';

                $customer['content'] = $payload['content'] ?? '客户咨询';

                $clientInfo = OnlineUser::getInstance()->get($payload['masterId']);

                $customer['client_name'] = $clientInfo['name'];

                SaveMessage::getInstance()->saveMessage(Filter::getInstance()->saveChatSession($customer));
            }

            return true;
    }

    protected function finish($result, $task_id)
    {
        // TODO: Implement finish() method.
    }
}