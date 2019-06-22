<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:49
 */

namespace App\WebSocket\Actions\User;

use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class UserOutRoom extends ActionPayload
{
    protected $action = WebSocketAction::USER_OUT_ROOM;
    protected $userFd;
    protected $number;//客户编号
    protected $customer_number;

    /**
     * @return mixed
     */
    public function getUserFd()
    {
        return $this->userFd;
    }

    /**
     * @param mixed $userFd
     */
    public function setUserFd($userFd): void
    {
        $this->userFd = $userFd;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function setCustomerNumber($customerNumber)
    {
        $this->customer_number = $customerNumber;
    }
}