<?php
/**
 * Created by PhpStorm.
 * User: ljl
 * Date: 2016/6/16
 * Time: 14:02
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
 * @param $_first_uniqid
 * @param $_end_uniqid
 * @return string
 */
function _check_uniqid($_first_uniqid,$_end_uniqid) {
    if(($_first_uniqid != $_end_uniqid)||(strlen($_first_uniqid) != 40)) {
        _alert_back('唯一标识符提交不合法！');
    }else {
        return _mysql_string($_first_uniqid);
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
    //限制敏感用户名
    $_mg[0]='ljl';
    $_mg[1]='lijinglong';
    $_mg[2]='wq';
    //告诉用户那些用户不嗯呢该注册
    foreach($_mg as $value) {
        @$_mg_string.=$value.'\n';
    }
    //这里采用绝对匹配
    if(in_array($_string,$_mg)) {
        _alert_back($_mg_string.'以上三个用户为敏感用户名，不能注册！');
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
function _check_password($_first_pass,$_end_pass,$_min_num) {
    //判断密码
    if(strlen($_first_pass)<$_min_num) {
        _alert_back('密码不得小于'.$_min_num.'位！');
    }
    //密码和确认密码必须一致
    if($_first_pass != $_end_pass) {
        _alert_back('密码和确认密码不一致');
    }
    //密码加密
    //返回密码
    return _mysql_string(sha1($_first_pass));
}

/***
 * _check_question() 返回密码提示
 * @param $_string
 * @param $_min_num
 * @param $_max_num
 * @return string  返回过滤后的密码提示
 */
function _check_question($_string,$_min_num,$_max_num) {
    //头尾的空格必须去掉
    $_string = trim($_string);
    //长度大于4位或小于20位
    if(mb_strlen($_string,'utf-8')<$_min_num||mb_strlen($_string,'utf-8')>$_max_num){
        _alert_back('长度小于'.$_min_num.'位或不能大于'.$_max_num.'位！');
    }
    //返回密码提示
    return _mysql_string($_string);
}

/***
 * @param $_ques
 * @param $_answ
 * @param $_min_num
 * @param $_max_num
 * @return string
 */
function _check_answer($_ques,$_answ,$_min_num,$_max_num) {
    //长度大于4位或小于20位
    if(mb_strlen($_answ,'utf-8')<$_min_num||mb_strlen($_answ,'utf-8')>$_max_num){
        _alert_back('用户名长度小于'.$_min_num.'位或不能大于'.$_max_num.'位！');
    }
    //密码提示与回答不能一致
    if($_ques == $_answ) {
        _alert_back('密码提示与回答不得一致');
    }
    return _mysql_string(sha1($_answ));
}

/***
 * _check_email()函数检查邮箱是否合法
 * @access public
 * @param $_string 提交的邮箱
 * @return string 返回验证后的邮箱
 */
function _check_email($_string,$_min_num,$_max_num) {
        //参考bnbbs@163.com
        //[a-zA-Z0-9_]=>\w
        if (!preg_match('/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/',$_string)) {
            _alert_back('邮箱格式不正确');
        }
        if(strlen($_string )<$_min_num || strlen($_string)>$_max_num){
            _alert_back('邮箱长度不合法');
        }

    return _mysql_string($_string);
}

/***
 * _check_qq
 * @param $_string
 * @return int $_string 验证后的qq号码
 */
function _check_qq($_string) {
    if(empty($_string)) {
        return null;
    } else {
        if(!preg_match('/^[1-9]{1}[0-9]{4,9}$/',$_string)) {
            _alert_back('QQ号码格式不正确');
        }
    }
    return _mysql_string($_string);
}

