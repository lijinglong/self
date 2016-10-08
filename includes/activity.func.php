<?php
/**
 * Created by PhpStorm.
 * User: ljl
 * Date: 2016/6/27
 * Time: 10:03
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
 * write_to_file()  创建微信服务器页面文件
 * @param $filename 微信服务器页基础内容
 * @param $addContent  添加的token等内容
 * @param $write_filename   生成的微信服务器文件
 */
function write_to_file($filename,$addContent,$write_filename) {
    // 确定文件存在并且可写。
    $filename = ROOT_PATH.'/self/server/'.$filename;
    $write_filename = ROOT_PATH.'/self/server/'.$write_filename;
    if (is_writable($filename)) {
        // 将文件读入数组,每行是一条记录
        $lines = file ($filename);
        // 使用写入方式打开打开$filename，文件指针将会在文件的开头
        $handle = fopen($write_filename, 'w');
        //在数组中循环，当到达第2行时插入新的内容。
        foreach ($lines as $line_num => $line) {
            if($line_num==1){
                //将$addContent写入到文件中。
                if (!fwrite($handle, $addContent)) {
                    print "不能写入到文件 $filename";
                    exit;
                }
            }
            //写入原来的行内容到文件中
            if (!fwrite($handle, $line)) {
                print "不能写入到文件 $filename";
                exit;
            }
        }
    }
}


/***
 * _check_activityName() 检验活动名称是否输入正确
 * @param $_string 活动名称
 * @param $_min_num 最小长度
 * @param $_max_num 最大长度
 * @return string  返回活动名称
 */
function _check_activityName($_string,$_min_num,$_max_num) {
    $_string = trim($_string);
    if(mb_strlen($_string,'utf-8') < $_min_num || mb_strlen($_string,'utf-8') >$_max_num) {
        _alert_back('活动名称不能少于'.$_min_num.'位,多于'.$_max_num.'位');
    }
    return _mysql_string($_string);
}

/***
 * _check_uniqid()  验证唯一标识是否填写正确
 * @param $string
 */
function _check_uniqids($string){
    $string = trim($string);
    if(strlen($string) < 2 || strlen($string) > 8) {
        _alert_back('唯一标识必须为2到6位英文字母组成');
    }
    if(!preg_match('/^[a-zA-Z]+$/',$string)) {
        _alert_back('唯一标识必须是2到6位英文字母组成');
    }
    return _mysql_string($string);
}

/***
 * _check_screenTitle() 验证大屏幕标题是否正确
 * @param $_string   大屏幕标题
 * @param $_min_num 最小长度
 * @param $_max_num 最大长度
 * @return string   返回大屏幕标题
 */
function _check_screenTitle($_string,$_min_num,$_max_num) {
    $_string = trim($_string);
    if(mb_strlen($_string,'utf-8')< $_min_num || mb_strlen($_string,'utf-8') > $_max_num) {
        _alert_back('大屏幕标题不能少于'.$_min_num.'位，或者大于'.$_max_num.'位');
    }
    return _mysql_string($_string);
}

/***
 * _check_chatID()   验证微信ID不为空
 * @param $_string  微信ID
 * @return string   返回微信ID
 */
function _check_chatID($_string) {
    $_string = trim($_string);
    if(strlen($_string) == 0) {
        _alert_back('微信ID不能为空');
    }
    return _mysql_string($_string);
}

/***
 * _check_chatSecret()   验证微信secret是否为空
 * @param $_string      微信secret
 * @return string       返回微信secret
 */
function _check_chatSecret($_string) {
    $_string = trim($_string);
    if(mb_strlen($_string,'utf-8')==0) {
        _alert_back('微信secret不能为空');
    }
    return _mysql_string($_string);
}

/***
 * _check_method()  验证签到方式
 * @param $_string
 */
function _check_method($_string) {
    if($_string == '') {
        _alert_back('签到方式不能为空');
    }
    return _mysql_string($_string);
}

/***
 * _check_order()   验证签到命令
 * @param $_string
 * @return string
 */
function _check_order($_string) {
    $_string = trim($_string);
    if(mb_strlen($_string,'utf-8')<0) {
        _alert_back('签到命令不能为空');
    }
    return _mysql_string($_string);
}

/***
 * _check_subscribe（）   验证关注提示
 * @param $_string
 */
function _check_subscribe($_string) {
    $_string = trim($_string);
    if(mb_strlen($_string,'utf-8')<0) {
        _alert_back('关注提示不能为空');
    }
    return _mysql_string($_string);
}

/***
 * _check_sign()    验证签到提示
 * @param $_string
 * @return string
 */
function _check_sign($_string) {
    $_string = trim($_string);
    if(mb_strlen($_string,'utf-8')<0) {
        _alert_back('签到提示不能为空');
    }
    return _mysql_string($_string);
}
