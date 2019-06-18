<?php


namespace App\WebSocket\Actions\Broadcast;


use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class BroadcastClient extends ActionPayload
{
    //第一次给客户端发送信息
    protected $action = WebSocketAction::BROADCAST_ADMIN;

    protected $content;

    protected $number;//客服编号
    protected $customer_id;//客服id
    protected $name = null;//客服昵称

    public function setContent($content):void
    {
        $this->content = $content;
    }

    public function setNumber($number):void
    {
        $this->number = $number;
    }

    public function setCustomerId($customerId):void
    {
        $this->customer_id = $customerId;
    }

    public function setName($name):void
    {
        $this->name = $name;
    }

    public function getContent()
    {
        return $this->content;
    }
}