<?php
/**
 * Created by PhpStorm.
 * User: ljl
 * Date: 2016/6/15
 * Time: 14:32
 */

/***
 * 将时间戳转换为时间格式
 */
function timestampToTime($timestamp,$dateformat){

    return date($dateformat,$timestamp);
}

/***
 * 将时间转换为时间戳
 */
function timeToTimestamp($time){
    return strtotime($time);
}

/**
 *_runtime()是用来获取执行耗时的
 *@ access public 表示函数对外公开
 *@ return float 表示返回出来的是一个浮点型的值
 */
function _runtime() {
    $_mtime = explode(' ', microtime());
    return $_mtime[1]+$_mtime[0];
}
/**
 * _alert_back()函数弹出提示框
 * @access public 表示函数对外公开
 * @param $_info 弹出的信息
 */
function _alert_back($_info) {
    echo "<script type='text/javascript'>alert('".$_info."');history.back();</script>";
    exit();
}

/***
 *
 *
 */
function _location($_info,$_url) {
    if(!empty($_info)) {
        echo "<script type='text/javascript'>alert('$_info');location.href='$_url';</script>";
        exit();
    }else {
        header('Location:'.$_url);
    }

}

/***
 *  _login_state()   登录状态的判断
 * @return bool     true表示已登录，false表示未登录。
 */
function _login_state() {
    if(isset($_COOKIE['username'])){
        return true;
    } else {
        return false;
    }
}

/**
 *
 * @param $_sql
 * @param $_size
 */

function _page($_connect,$_sql,$_size) {
    //将里面的所有变量取出来，外部可以访问
    global $_page,$_pagesize,$_pagenum,$_pageabsolute,$_num;
    if (isset($_GET['page'])) {
        $_page = $_GET['page'];
        if (empty($_page) || $_page < 0 || !is_numeric($_page)) {
            $_page = 1;
        } else {
            $_page = intval($_page);
        }
    } else {
        $_page = 1;
    }
    $_pagesize = $_size;
    $_num = mysqli_num_rows(_query($_connect,$_sql));
    if ($_num == 0) {
        $_pageabsolute = 1;
    } else {
        $_pageabsolute = ceil($_num / $_pagesize);
    }
    if ($_page > $_pageabsolute) {
        $_page = $_pageabsolute;
    }
    $_pagenum = ($_page - 1) * $_pagesize;
}


/**
 * _paging分页函数
 * @param $_type
 * @return 返回分页
 */

function _paging($_type,$_uniqid)
{
    global $_page, $_pageabsolute, $_num;
    if ($_type == 1) {
        echo '<div id="page_num">';
        echo '<ul>';
        for ($i = 0; $i < $_pageabsolute; $i++) {
            if ($_page == ($i + 1)) {
                echo '<li><a href="' . SCRIPT . '.php?page=' . ($i + 1) . '" class="selected">' . ($i + 1) . '</a></li>';
            } else {
                echo '<li><a href="' . SCRIPT . '.php?page=' . ($i + 1) . '">' . ($i + 1) . '</a></li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    } elseif ($_type == 2) {
        echo '<div id="page_text">';
        echo '<ul>';
        echo '<li>' . $_page . '/' . $_pageabsolute . '页 | </li>';
        echo '<li>共有<strong>' . $_num . '</strong>条数据 | </li>';
        if ($_page == 1) {
            echo '<li>首页 | </li>';
            echo '<li>上一页 | </li>';
        } else {
            echo '<li><a href="' . SCRIPT . '.php?uniqid='.$_uniqid.'">首页</a> | </li>';
            echo '<li><a href="' . SCRIPT . '.php?uniqid='.$_uniqid.'&page=' . ($_page - 1) . '">上一页</a> | </li>';
        }
        if ($_page == $_pageabsolute) {
            echo '<li>下一页 | </li>';
            echo '<li>尾页</li>';
        } else {
            echo '<li><a href="' . SCRIPT . '.php?uniqid='.$_uniqid.'&page=' . ($_page + 1) . '">下一页</a> | </li>';
            echo '<li><a href="' . SCRIPT . '.php?uniqid='.$_uniqid.'&page=' . $_pageabsolute . '">尾页</a></li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}



/***
 * _session_destroy()删除session
 */
function _session_destroy() {
    session_destroy();
}

/***
 * _unsetcookies()删除cookie
 */
function _unsetcookies() {
    setcookie('username','',time()-1);
    setcookie('uniqid','',time()-1);
    _session_destroy();
_location(null,'index.php');
}

/***
 * _sha1_uniqid()生成唯一标识符
 * @return string
 */
function _sha1_uniqid() {
    return _mysql_string(sha1(uniqid(rand(),true)));
}

/***
 * _mysql_string()
 * @param $_string
 * @return string $_string
 */
function _mysql_string($_string) {
    if(!GPC) {
        return addslashes($_string);
    }else {
        return $_string;
    }
}


/***
 * _check_code 验证码比对
 * @param string $_first_code
 * @param string $_end_code
 */
function _check_code($_first_code,$_end_code){
    if($_first_code != $_end_code) {
        _alert_back('验证码不正确！');
    }
}
/**
 * _code()是生成验证码函数
 * @access public
 * @param int $_width 表示验证码的长度
 * @param int $_height 表示验证码的高度
 * @param int $_rnd_code 表示验证码的位数
 * @param bool $_flag 表示验证码是否需要边框
 * @return void 这个函数执行后返回一个验证码
 */
function _code($_width=75,$_height=25,$_rnd_code=4,$_flag=false) {
    //创建随机码，
    $_code='';
    while(strlen($_code)<$_rnd_code) {
        $_code.=dechex(mt_rand(0,15));
    }
//保存在session中持久
    $_SESSION['code']=$_code;

//创建一张图片
    $_width=75;
    $_height=25;
    $_img = imagecreatetruecolor($_width,$_height);
//白色图片
    $_white = imagecolorallocate($_img,255,255,255);
//填充
    imagefill($_img,0,0,$_white);
    if($_flag) {
        //黑色边框
        $_black = imagecolorallocate($_img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
        imagerectangle($_img,0,0,$_width-1,$_height-1,$_black);
    }
//随机创建六条线
    for ($i=0;$i<6;$i++) {
        $_rnd_color = imagecolorallocate($_img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
        imageline($_img,mt_rand(0,$_height),mt_rand(0,$_width),mt_rand(0,$_height),mt_rand(0,$_width),$_rnd_color);
    }
//随机雪花
    for($i=0;$i<100;$i++){
        $_rnd_color = imagecolorallocate($_img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
        imagestring($_img,1,mt_rand(1,$_width),mt_rand(1,$_height),'*',$_rnd_color);
    }
//输出验证码
    for($i=0;$i<strlen($_SESSION['code']);$i++) {
        imagestring($_img,5,$i*$_width/4+mt_rand(1,10),mt_rand(1,$_height/2),$_SESSION['code'][$i],imagecolorallocate($_img,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200)));
    }
//输出图片
    header('Content-Type:image/png');
    imagepng($_img);
//销毁
    imagedestroy($_img);
}