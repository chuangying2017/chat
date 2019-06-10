<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/10
 * Time: 6:57
 */

namespace App\Task;


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
            $connection = $server->connection_info($userFd);
            if ($connection['websocket_status'] == 3 && $userInfo['username'] == $customer['username']) {  // 客服正常在线时可以进行消息推送
                $server->push($userInfo['fd'], $taskData['payload']);
            }
        }

        // 添加到离线消息
      //  if ($payload['action'] == 103) {

            $userinfo = OnlineUser::getInstance()->get($taskData['fromFd']);

            $customer['client_number'] = $userinfo['username'];

            $customer['content'] = $payload['content'] ?? '客户咨询';

            SaveMessage::getInstance()->saveMessage($customer);

      //  }

        return true;
    }

    protected function finish($result, $task_id)
    {
        // TODO: Implement finish() method.
    }
}