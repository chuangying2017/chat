<!doctype html>
<html lang="zh-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.staticfile.org/amazeui/2.7.2/css/amazeui.min.css">
    <link rel="stylesheet" href="/css/login.css?v=190527">
    <script src="https://cdn.staticfile.org/jquery/3.4.1/jquery.min.js"></script>
    <title>客户登录</title>
</head>
<body>

<!-- 登录框体 -->
<div class="block-box">
    <div class="main-title">客户通讯</div>
    <div class="sub-title">communication</div>
    <form class="am-form" style="margin-top: 80px;">
        <div class="am-form-group am-input-group">
            <span class="am-input-group-label"><i class="am-icon-at am-icon-fw"></i></span>
            <input type="text" name="username" class="am-form-field" placeholder="输入账号">
        </div>
        <div class="am-form-group am-input-group">
            <span class="am-input-group-label"><i class="am-icon-lock am-icon-fw"></i></span>
            <input type="password" name="password" class="am-form-field" placeholder="输入登录密码">
        </div>
        <div class="am-form-group">
            <button type="button" class="am-btn am-btn-primary am-btn-block" style="margin-top: 0px;">开始畅聊</button>
        </div>
        <div class="am-form-group" style="text-align: center;margin-top: 130px;">
            <!--<a href="/register">没有微聊账号 马上注册一个</a>-->
        </div>
    </form>
</div>
<script>

    $("button[type='button']").click(function()
    {
            var username = $('input[name="username"]').val();

            var password = $('input[name="password"]').val();

            if (!username || !password)
            {
                alert('账号密码必填');
                return false;
            }

            $.ajax({
                data:{username:username,password:password},
                type:'post',
                dataType:'json',
                url:'/login/verifyLogin',
                success:function(data){
                    console.log(data)
                    if (data.msg == 'success')
                    {
                        window.location.href=data.result.src;
                    }else{
                        alert('账号或密码错误');
                    }
                },
                erroe:function(err,type){
                    console.log(err,type)
                }
            });
    });

</script>
</body>
</html>