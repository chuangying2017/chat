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

class SaveMessage
{
    use Singleton;

    public function saveMessage($message)
    {

        $arr = [
            'customer_id' => $message['customer_id'],
            'client_number' => $message['client_number'],
            'content' => $message['content'],
        ];

        SessionRecord::getInstance()->add($arr);

    }
}