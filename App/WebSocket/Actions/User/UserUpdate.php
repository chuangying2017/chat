<?php


namespace App\WebSocket\Actions\User;


use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class UserUpdate extends ActionPayload
{
    protected $action = WebSocketAction::UPDATE_CUSTOMER_NAME;

    protected $fd;

    protected $info;


    public function setInfo($info)
    {
        $this->info = $info;
    }

    public function setFd($fd)
    {
        $this->fd = $fd;
    }
}