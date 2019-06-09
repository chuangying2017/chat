<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/9
 * Time: 18:02
 */

namespace App\Storage;


use EasySwoole\Component\Singleton;
use EasySwoole\Component\TableManager;
use Swoole\Table;

class OnlineCustomer
{
    use Singleton;

    protected $table;

    public function __construct()
    {
        TableManager::getInstance()->add('onlineCustomer', [
            'fd' => ['type' => Table::TYPE_INT, 'size' => 8],
            'avatar' => ['type' => Table::TYPE_STRING, 'size' => 128],
            'username' => ['type' => Table::TYPE_STRING, 'size' => 128],
            'last_heartbeat' => ['type' => Table::TYPE_INT, 'size' => 4],
        ]);

        $this->table = TableManager::getInstance()->get('onlineCustomer');
    }
}