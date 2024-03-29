<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/11
 * Time: 1:04
 */

namespace App\FilterLogic;


use App\Obtain\FilterMethod;
use EasySwoole\Component\Singleton;

class Filter
{
    use Singleton;

    public function saveChatSession(array $arr): array
    {
        $filterKey = ['customer_id','client_number','content','mode','type','client_name'];

        return FilterMethod::getInstance()->only($arr, $filterKey);
    }

    public function saveCustomerData(array $arr)
    {
        $filterKey = ['name','number','username','id'];

        return FilterMethod::getInstance()->only($arr, $filterKey);
    }
}