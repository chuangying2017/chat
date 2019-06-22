<?php

namespace App\Storage;

use EasySwoole\Component\Singleton;
use EasySwoole\Component\TableManager;
use Swoole\Table;

/**
 * 在线用户
 * Class OnlineUser
 * @package App\Storage
 */
class OnlineUser
{
    use Singleton;
    protected $table;  // 储存用户信息的Table

    const INDEX_TYPE_ROOM_ID = 1;
    const INDEX_TYPE_ACTOR_ID = 2;

    /**
     * OnlineUser constructor.
     */
    function __construct()
    {
        TableManager::getInstance()->add('onlineUsers', [
            'fd' => ['type' => Table::TYPE_INT, 'size' => 8],
            'avatar' => ['type' => Table::TYPE_STRING, 'size' => 128],
            'number' => ['type' => Table::TYPE_STRING, 'size' => 128],
            'last_heartbeat' => ['type' => Table::TYPE_INT, 'size' => 4],
            'name' => ['type' => Table::TYPE_STRING, 'size' => 50],
            'customer_number' => ['type' => Table::TYPE_STRING, 'size' => 50]
        ]);

        $this->table = TableManager::getInstance()->get('onlineUsers');
    }

    /**
     * 设置一条用户信息
     * @param $fd
     * @param $number
     * @param $avatar
     * @param string $name
     * @param null $customer_number
     * @return mixed
     */
    function set($fd, $number, $avatar,$name=null,$customer_number = null)
    {
        return $this->table->set($number, [
            'fd' => $fd,
            'avatar' => $avatar,
            'number' => $number,
            'last_heartbeat' => time(),
            'name' => $name,
            'customer_number' => $customer_number
        ]);
    }

    /**
     * 获取一条用户信息
     * @param $number
     * @return array|mixed|null
     */
    function get($number)
    {
        $info = $this->table->get($number);
        return is_array($info) ? $info : null;
    }

    /**
     * 更新一条用户信息
     * @param $number
     * @param $data
     */
    function update($number, $data)
    {
        $info = $this->get($number);
        if ($info) {
            $fd = $info['fd'];
            $info = $data + $info;
            $this->table->set($fd, $info);
        }
    }

    /**
     * 删除一条用户信息
     * @param $number
     */
    function delete($number)
    {
        $info = $this->get($number);
        if ($info) {
            $this->table->del($info['fd']);
        }
    }

    /**
     * 心跳检查
     * @param int $ttl
     */
    function heartbeatCheck($ttl = 60)
    {
        foreach ($this->table as $item) {
            $time = $item['time'];
            if (($time + $ttl) < $time) {
                $this->delete($item['fd']);
            }
        }
    }

    /**
     * 心跳更新
     * @param $number
     */
    function updateHeartbeat($number)
    {
        $this->update($number, [
            'last_heartbeat' => time()
        ]);
    }

    /**
     * 直接获取当前的表
     * @return Table|null
     */
    function table()
    {
        return $this->table;
    }
}