<?php
/**
 * Created by PhpStorm.
 * User: ljl
 * Date: 2016/6/20
 * Time: 14:55
 */
//防止恶意调用
if(!defined('IN_TG')){
    exit('Access Defined！');
}
if(!function_exists('_alert_back')) {
    exit('_alert_back()函数不存在，请检查');
}
if(!function_exists('_mysql_string')) {
    exit('_mysql_string()函数不存在，请检查');
}
/***
 * 生成登陆cookie
 * @param $_username
 * @param $_uniqid
 */
function _setcookies($_username,$_uniqid,$_time,$tg_flag) {
    switch($_time) {
        case '0':
            setcookie('username',$_username);
            setcookie('uniqid',$_uniqid);
            setcookie('flag',$tg_flag);
            break;
        case'1':
            setcookie('username',$_username,time()+86400);
            setcookie('uniqid',$_uniqid,time()+86400);
            setcookie('flag',$tg_flag,time()+86400);
            break;
        case'2':
            setcookie('username',$_username,time()+604800);
            setcookie('uniqid',$_uniqid,time()+604800);
            setcookie('flag',$tg_flag,time()+604800);
            break;
        case'3':
            setcookie('username',$_username,time()+2592000);
            setcookie('uniqid',$_uniqid,time()+2592000);
            setcookie('flag',$tg_flag,time()+2592000);
            break;
    }
}

/**
 *_check_username() 检查用户名是否符合要求
 * @access public 表示函数对外公开
 * @param $_string 待检查的字符串
 * @param $_min_num 表示最小长度
 * @param $_max_num 表示最大长度
 * @return 返回过滤后的用户名字符串
 */
function _check_username($_string,$_min_num,$_max_num) {
    //头尾的空格必须去掉
    $_string = trim($_string);
    //长度大于2位或小于20位
    if(mb_strlen($_string,'utf-8')<$_min_num||mb_strlen($_string,'utf-8')>$_max_num){
        _alert_back('长度小于'.$_min_num.'位或不能大于'.$_max_num.'位！');
    }
    //限制敏感字符
    $_char_parrern = '/[<>\'\" \  ]/';
    if(preg_match($_char_parrern,$_string)) {
        _alert_back('用户名不得包含敏感字符');
    }
    //将用户名转义输入
    return _mysql_string($_string);
}

/***
 * _check_password 函数验证密码
 * @param $_first_pass 密码
 * @param $_end_pass   确认密码
 * @param $_min_num   密码最少位数
 * @return string 返回加密后的密码
 */
function _check_password($_string,$_min_num) {
    //判断密码
    if(strlen($_string)<$_min_num) {
        _alert_back('密码不得小于'.$_min_num.'位！');
    }
    //密码加密
    //返回密码
    return sha1($_string);
}

function _check_time($_string) {
    $_time = array(0,1,2,3,4);
    if(!in_array($_string,$_time)) {
        _alert_back('保留方式出错');
    }
    return _mysql_string($_string);
}