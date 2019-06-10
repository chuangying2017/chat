<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/11
 * Time: 1:02
 */

namespace App\Obtain;


use EasySwoole\Component\Singleton;

class FilterMethod
{
    use Singleton;

    public function only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }
}