/**
 * Created by ljl on 2016/8/12.
 */
function delmessage(tg_id,uniqid){
    if(confirm("确定删除此条消息？")){
        window.location="message.php?del=ok&uniqid="+uniqid+"&tg_id="+tg_id;
        return true;
    }else{
        return false;
    }
}