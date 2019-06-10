<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;

use App\Storage\ChatMessage;
use App\Storage\OnlineUser;
use App\WebSocket\WebSocketEvents;
use App\WebSocket\WebSocketParser;
use EasySwoole\Component\Pool\Exception\PoolObjectNumError;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\MysqliPool\RedisPoolException;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Socket\Dispatcher;
use swoole_server;
use swoole_websocket_frame;
use \Exception;

class EasySwooleEvent implements Event
{
    /**
     *
     */
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        $redis_config = Config::getInstance()->getConf('REDIS');
        $redisConfig =new \EasySwoole\RedisPool\Config($redis_config);
        try {
            $redisPoolConfig = Redis::getInstance()->register('redis', $redisConfig);
        } catch (RedisPoolException $e) {

        }
        try {
            $redisPoolConfig->setMaxObjectNum($redis_config['maxObjectNum']);

            $mysql1Config = new \EasySwoole\Mysqli\Config(Config::getInstance()->getConf('MYSQL'));
            $pool1Config = \EasySwoole\MysqliPool\Mysql::getInstance()->register('mysql',$mysql1Config);
            //根据返回的poolConfig对象进行配置连接池配置项
            $pool1Config->setMaxObjectNum(Config::getInstance()->getConf('MYSQL.maxObjectNum'));

        } catch (PoolObjectNumError $e) {

        }
    }

    /**
     * 服务启动前
     * @param EventRegister $register
     * @throws Exception
     */
    public static function mainServerCreate(EventRegister $register)
    {
        $server = ServerManager::getInstance()->getSwooleServer();

        OnlineUser::getInstance();
        ChatMessage::getInstance();
        Cache::getInstance()->setTempDir(EASYSWOOLE_ROOT . '/Temp')->attachToServer($server);

        // 注册服务事件
        $register->add(EventRegister::onOpen, [WebSocketEvents::class, 'onOpen']);
        $register->add(EventRegister::onClose, [WebSocketEvents::class, 'onClose']);

        // 收到用户消息时处理
        $conf = new \EasySwoole\Socket\Config;
        $conf->setType($conf::WEB_SOCKET);
        $conf->setParser(new WebSocketParser);
        $dispatch = new Dispatcher($conf);
        $register->set(EventRegister::onMessage, function (swoole_server $server, swoole_websocket_frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame->data, $frame);
        });

        $register->add($register::onWorkerStart, function (\swoole_server $server, int $workerId) {
            if ($server->taskworker == false) {
                PoolManager::getInstance()->getPool(Redis::class)->preLoad(1);
                //PoolManager::getInstance()->getPool(RedisPool::class)->preLoad(预创建数量,必须小于连接池最大数量);
            }

            // var_dump('worker:' . $workerId . 'start');
        });

        $register->add($register::onWorkerStart, function (\swoole_server $server, int $workerId) {
            if ($server->taskworker == false) {
                PoolManager::getInstance()->getPool(Mysql::class)->preLoad(1);
                //PoolManager::getInstance()->getPool(RedisPool::class)->preLoad(预创建数量,必须小于连接池最大数量);
            }

            // var_dump('worker:' . $workerId . 'start');
        });

    }

    public static function onRequest(Request $request, Response $response): bool
    {
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {

    }
}