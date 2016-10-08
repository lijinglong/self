<?php
/**
 * Created by PhpStorm.
 * User: ljl
 * Date: 2016/6/15
 * Time: 16:55
 */
//防止恶意调用
if(!defined('IN_TG')){
    exit('Access Defined！');
}
//防止非html页面调用
if(!defined('SCRIPT')){
    exit('Access Defined!');
}
?>
<link rel="shortcut icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="styles/1/basic.css" />
<link rel="stylesheet" type="text/css" href="styles/1/<?php echo SCRIPT ?>.css" />
