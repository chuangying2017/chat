<?php


namespace App\WebSocket\Actions\User;


use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class UserDeleteSession extends ActionPayload
{
    protected $action = WebSocketAction::DELETE_CUSTOMER_SESSION;

    protected $ClientNumber;

    public function setClientNumber($ClientNumber)
    {
        $this->ClientNumber = $ClientNumber;
    }
}