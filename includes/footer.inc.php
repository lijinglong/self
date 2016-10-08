<?php
/**
 * Created by PhpStorm.
 * User: ljl
 * Date: 2016/6/15
 * Time: 13:23
 */
//防止恶意调用
if(!defined('IN_TG')){
    exit('Access Defined！');
}
_close($_connect);
?>
<div id="footer">
    <p>本程序执行时间为：<?php echo round(_runtime()-START_TIME,4)?>秒</p>
    <p>版权所有 翻版必究</p>
</div>