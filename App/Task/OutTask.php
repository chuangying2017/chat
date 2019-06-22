<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-11-28
 * Time: 20:23
 */

namespace App\Task;

use App\Obtain\TempUserGet;
use App\Storage\ChatMessage;
use App\Storage\OnlineUser;
use App\Storage\SaveMessage;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;
use App\WebSocket\WebSocketAction;
use EasySwoole\EasySwoole\Config;

/**
 * 客户端 退出通知 客服
 * Class BroadcastTask
 * @package App\Task
 */
class OutTask extends AbstractAsyncTask
{

    /**
     * 执行投递
     * @param $taskData
     * @param $taskId
     * @param $fromWorkerId
     * @param $flags
     * @return bool
     */
    protected function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        /** @var \swoole_websocket_server $server */
        $server = ServerManager::getInstance()->getSwooleServer();

        $payload = json_decode($taskData['payload'], true);

        foreach (OnlineUser::getInstance()->table() as $number => $userInfo) {
            $connection = $server->connection_info($userInfo['fd']);
            if ($connection['websocket_status'] == 3) {  // 用户正常在线时可以进行消息推送

                if (!$payload['customer_number']) break;

                if ($userInfo['number'] == $payload['customer_number'])
                {
                    $server->push($userInfo['fd'], $taskData['payload']);

                    $arr = TempUserGet::getInstance()->GetTempClientList($payload['customer_number']);

                    if (is_array($arr))
                    {
                        $array = $arr['user' . $arr[$payload['number']]];

                        $array['status'] = 'inactive';//离线

                        $arr['user' . $arr[$payload['number']]] = $array;

                        SaveMessage::getInstance()->setCustomerRoomUser($payload['customer_number'],$arr);
                    }
                }

            }
        }

        return true;
    }

    function finish($result, $task_id)
    {
        // TODO: Implement finish() method.
    }
}