<?php
/**
 * Created by PhpStorm.
 * User: ljl
 * Date: 2016/8/16
 * Time: 14:48
 */
if(!defined('IN_TG')){
    exit('Access Defined！');
}
if(isset($_COOKIE['username'])){
    $_username = $_COOKIE['username'];
}else{
    $_username = '';
}
?>

<div id="user">
    <h2>菜单选项</h2>
    <dl>
        <dd><a href="activity.php" >发布活动</a></dd>
        <dd><a href="activityList.php" >活动列表</a></dd>
        <dd><a href="member.php?username=<?php echo $_username;?>">个人中心</a></dd>
        <dd><a href="reward.php?username=<?php echo $_username;?>">上墙规则</a> </dd>
    </dl>
</div>