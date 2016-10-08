/**
 * Created by ljl on 2016/6/28.
 */
//等待网页加载完毕再执行
window.onload = function(){
    var fm = document.getElementsByTagName('form')[0];
    function checkEmpty(point,info){
        var str = point.value;
        if(str.replace(/(^\s*)|(\s*$)/g,'').length == 0){
            alert(info);
            point.value = '';
            point.focus();
            return false;
        }
    }
    fm.onsubmit = function (){
        return checkEmpty(fm.activityname,'活动名称不能为空');
        if(fm.activityname.value.length < 2 || fm.activityname.value.length > 20) {
            alert('活动名称不能小于2位或者大于20位');
            fm.activityname.value = '';
            fm.activityname.focus();
            return false;
        }
        return checkEmpty(fm.uniqid,'唯一标识不能为空');
        if(fm.uniqid.value.length < 2 || fm.uniqid.value.length > 8){
            alert('唯一标识不能小于2位或者大于6位');
            fm.uniqid.value = '';
            fm.uniqid.focus();
            return false;
        }
        return checkEmpty(fm.screentitle,'大屏幕标题不能为空');
        if(fm.screentitle.value.length <1 || fm.screentitle.value.length > 15){
            alert('大屏幕标题不能小于1位或者大于15位');
            fm.screentitle.value='';
            fm.screentitle.focus();
            return false;
        }
        return checkEmpty(fm.chatid,'微信ID不能为空!');
        return checkEmpty(fm.chatsecret,'微信secret不能为空！');
        return checkEmpty(fm.starttime,'开始时间不能为空');
        return checkEmpty(fm.endtime,'结束时间不能为空');
        var s = new Date(fm.starttime.value);
        var e = new Date(fm.endtime.value);
        if(s>e|| s==e){
            alert('结束时间不能早于开始时间');
            fm.endtime.value='';
            fm.endtime.focus();
            return false;
        }
        return checkEmpty(fm.methodes,'签到方式不能为空');
        return checkEmpty(fm.orders,'签到命令不能为空');
        return checkEmpty(fm.subscribe,'关注提示不能为空');
        return checkEmpty(fm.sign,'签到提示不能为空');

        return true;
    };
};
