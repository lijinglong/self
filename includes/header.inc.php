<?php
/**
 * Created by PhpStorm.
 * User: ljl
 * Date: 2016/6/15
 * Time: 13:18
 */
//防止恶意调用
if(!defined('IN_TG')){
    exit('Access Defined！');
}
?>
    <div id="header">
        <h1><a href="index.php">多用户留言系统</a></h1>
        <ul>
            <li><a href="index.php">首页</a></li>
            <?php
                if(isset($_COOKIE['username'])) {
                    if(@$_COOKIE['flag']==1){
                        echo '<li><a href="member.php?username='.$_COOKIE['username'].'">管理员·个人中心</a></li>';
                    }else{
                        echo '<li><a href="member.php?username='.$_COOKIE['username'].'">'.$_COOKIE['username'].'·个人中心</a></li>';
                    }
                    echo "\n";
                }else {
                    echo '<li><a href="register.php">注册</a></li>';
                    echo "\n";
                    echo "\t\t";
                    echo '<li><a href="login.php">登录</a></li>';
                    echo "\n";
                }
            ?>
            <li>风格</li>
            <li><a href="manager.php">管理</a></li>
            <?php
                if(isset($_COOKIE['username'])) {
                    echo '<li><a href="logout.php">退出</a></li >';
                }
            ?>
        </ul>
    </div>
