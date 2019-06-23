<?php


namespace App\WebSocket\Actions\User;


use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class UserUpdateAvatar extends ActionPayload
{
    protected $action = WebSocketAction::CUSTOMER_UPDATE_AVATAR;

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