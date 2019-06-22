<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/8
 * Time: 22:36
 */

namespace App\Model;


use EasySwoole\Component\Singleton;

class SessionRecord extends Model
{
    use Singleton;

    protected $dbTable = 'session_record';

}