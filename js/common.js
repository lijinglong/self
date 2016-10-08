/**
 * Created by ljl on 2016/8/15.
 */
function deleteInfo(tg_id,uniqid,info,page){
    if(confirm(info)){
        window.location=page+".php?del=ok&uniqid="+uniqid+"&tg_id="+tg_id;
        return true;
    }else{
        return false;
    }
}