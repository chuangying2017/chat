<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:49
 */

namespace App\WebSocket\Actions\Broadcast;

use App\Model\CustomerModel;
use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class BroadcastAdmin extends ActionPayload
{
    //随机分配后台客服 统一用客服编号 通信
    protected $action = WebSocketAction::BROADCAST_ADMIN;
    protected $content;

    protected $username;//客服编号
    protected $customer_id;//客服id

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function setRandomCustomer(): void
    {
        $customer = new CustomerModel();

        $allActive = $customer->where('status','active')->select();

        if (!empty($allActive))
        {
            $int_rand = array_rand($allActive,1);

            $arr = $allActive[$int_rand];

            $this->username = $arr['number'];
            $this->customer_id = $arr['id'];//客服id
        }else{

            $this->username = 'KF000TEST';

        }


    }
}