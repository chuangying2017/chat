<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2019/6/8
 * Time: 22:36
 */

namespace App\Model;


class CustomerModel extends Model
{

    protected $dbTable = 'customer';


  public function updateData($number,$data)
  {
      $res = static::where('number',$number)->update($data);

      return $res;
  }
}