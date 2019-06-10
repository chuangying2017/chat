<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/10
 * Time: 7:06
 */

namespace App\Storage;


use App\Model\SessionRecord;
use EasySwoole\Component\Singleton;
use EasySwoole\RedisPool\Redis;

class SaveMessage
{
    use Singleton;

    public function saveMessage($message)
    {
        var_dump($message);

        $res = SessionRecord::getInstance()->add($message);

        var_dump($res);
    }

    /**
     * 将临时用户保存到指定的客户
     * @param $customerNumber
     * @param $tempUser
     */
    public function saveRedisCustomer($customerNumber,$tempUser):void
    {
        $redis = Redis::defer('redis');

        $cus = $redis->get($customerNumber);

        if ($cus)
        {
            $arr = unserialize($cus);

            if (count($arr) >= 20)
            {
                array_shift($arr );
            }

            $arr['user' . $tempUser['fd']] = $tempUser;
            $redis->delete($customerNumber);
        }else{
            $arr['user' . $tempUser['fd']] = $tempUser;
        }
        $redis->set($customerNumber, serialize($arr));
        $redis->setTimeout($customerNumber,3600);
    }
}