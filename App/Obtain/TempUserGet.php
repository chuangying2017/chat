<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/10
 * Time: 20:39
 */

namespace App\Obtain;


use EasySwoole\Component\Singleton;
use EasySwoole\RedisPool\Redis;

class TempUserGet
{
    use Singleton;

    protected $cache;

    public function __construct()
    {
        $this->cache = Redis::defer('redis');
    }

    public function GetTempClientList($customerNumber)
    {
        $res = $this->cache->get($customerNumber);

        if ($res)
        {
            $res = unserialize($res);
        }else{
            $res = false;
        }

        return $res;
    }


}