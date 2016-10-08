<?php
/**
 * Created by PhpStorm.
 * User: ljl
 * Date: 2016/6/17
 * Time: 15:33
 */
//防止恶意调用
if(!defined('IN_TG')){
    exit('Access Defined！');
}


/***
 * _connect() 创建数据库连接
 * @access public
 * @return void
 */
//function _connectdb($host,$user,$pass){
//    //创建数据库连接
//    $_conn = mysqli_connect($host,$user,$pass);
//    if(!$_conn) {
//        exit('数据库连接失败!');
//    }else {
//        return $_conn;
//    }
//}
/***
 * _select_db 选择一个数据库
 */
function _select_db($_connect,$db_name) {
    //选择一个数据库
    if(!mysqli_select_db($_connect,$db_name)){
        exit('找不到数据库!');
    }
}

/***
 * _set_names() 设置字符集
 */
function _set_names($_connect) {
    if(!mysqli_set_charset($_connect,'UTF8')){
       exit('字符集错误!');
    }
}

/***
 * _query() 执行sql语句返回结果集
 * @param $_connect
 * @param $_sql
 * @return 查询结果集
 */
function _query($_connect,$_sql) {
    if(!$_result=mysqli_query($_connect,$_sql)) {
        exit ('sql执行失败!'.mysqli_error($_connect));
    }
    return $_result;
}

/***
 * _fetch_array() 将结果集返回一个数组
 * @param $_connect
 * @param $_sql
 * @return array|null
 */
function _fetch_array($_connect,$_sql) {
    return mysqli_fetch_array(_query($_connect,$_sql),MYSQL_ASSOC);
}

/***
 * _affected_rows() 表示影响到的记录条数
 * @param $_connect
 * @return int
 */
function _affected_rows($_connect) {
    return mysqli_affected_rows($_connect);
}

/***
 * _is_repeat() 判断新增用户是否与已有数据重复
 * @param $_connect
 * @param $_sql
 * @param $_info
 */
function _is_repeat($_connect,$_sql,$_info) {
    if(_fetch_array($_connect,$_sql)) {
        _alert_back($_info);
    }
}

/***
 * _close() 关闭数据库连接
 * @param $_connect
 */
function _close($_connect) {
    if(!mysqli_close($_connect)){
        exit('关闭异常');
    }
}
