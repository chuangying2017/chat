<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:44
 */

namespace App\WebSocket;

class WebSocketAction
{
    // 1xx BROADCAST 广播类消息
    const BROADCAST_ADMIN = 101;   // 管理消息
    const BROADCAST_SYSTEM = 102;  // 系统消息
    const BROADCAST_MESSAGE = 103; // 用户消息
    const BROADCAST_LAST_MESSAGE = 104; // 最后消息

    // 2xx USER 用户类消息
    const USER_INFO = 201;         // 用户信息
    const USER_ONLINE = 202;       // 在线列表
    const USER_IN_ROOM = 203;      // 进入房间 新用户来咨询
    const USER_OUT_ROOM = 204;     // 离开房间 用户已离开

    const UPDATE_CUSTOMER_NAME = 205; //编辑客服的名称
    const DELETE_CUSTOMER_SESSION = 206; //删除客服中的会话

    const USER_IN_ROOM_LIST = 207; //批量推送用户列表到

    //一对一聊天
}