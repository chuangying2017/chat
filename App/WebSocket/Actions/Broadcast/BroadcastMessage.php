<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:49
 */

namespace App\WebSocket\Actions\Broadcast;

use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

/**
 * 广播客户消息
 * Class BroadcastMessage
 * @package App\WebSocket\Actions\Broadcast
 */
class BroadcastMessage extends ActionPayload
{
    protected $action = WebSocketAction::BROADCAST_MESSAGE;
    protected $fromUserFd;
    protected $content;
    protected $type;
    protected $sendTime;
    protected $avatar; //头像链接
    protected $number; //用户编号
    protected $accept; //接收者的账号
    protected $send; //发送者的账号
    protected $masterId; //聊天组的id
    protected $name = null;//客服端昵称

    /**
     * @return mixed
     */
    public function getFromUserFd()
    {
        return $this->fromUserFd;
    }

    /**
     * @param mixed $fromUserFd
     */
    public function setFromUserFd($fromUserFd): void
    {
        $this->fromUserFd = $fromUserFd;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @param mixed $content
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @param mixed $content
     */
    public function setSendTime($sendTime): void
    {
        $this->sendTime = $sendTime;
    }

    public function setAvatar($avatar):void
    {
        $this->avatar = $avatar;
    }

    public function setNumber($number):void
    {
        $this->number = $number;
    }

    public function setAccept($number):void
    {
        $this->accept = $number;
    }

    public function setSend($number):void
    {
        $this->send = $number;
    }

    public function setMasterId($masterId):void
    {
        $this->masterId = $masterId;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}