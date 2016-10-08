/**
 * Created by ljl on 2016/6/15.
 */
//等待网页加载完毕再执行
window.onload = function(){
    code();
    var faceimg = document.getElementById('faceimg');
    faceimg.onclick = function() {
        window.open('face.php','face','width=400,height=400,top=0,left=0,scrollbars=1');
    };
    //表单验证
    var fm = document.getElementsByTagName('form')[0];
    fm.onsubmit = function(){
        //能用客户端验证的计量用客户端
        //验证用户名
        if(fm.username.value.length<2||fm.username.value.length>20) {
            alert('用户名不得小于两位或者大于20位');
            fm.username.value='';
            fm.username.focus();//将焦点移至表单字段
            return false;
        }
        if(/[<>\'\"\ \  ]/.test(fm.username.value)){
            alert('用户名不能包含敏感字符');
            fm.username.value='';
            fm.username.focus();//将焦点移至表单字段
            return false;
        }
        //验证密码
        if(fm.password.value.length<6) {
            alert('密码不得小于6位');
            fm.password.value='';
            fm.password.focus();//将焦点移至表单字段
            return false;
        }
        if(fm.password.value !=fm.notpassword.value){
            alert('密码必须一致');
            fm.password.value='';
            fm.password.focus();//将焦点移至表单字段
            return false;
        }
        //密码提示与回答
        if(fm.question.value.length<2 || fm.question.value.length>20) {
            alert('密码提示不得小于2位或者不嗯给你');
            fm.question.value='';
            fm.question.focus();//将焦点移至表单字段
            return false;
        }
        if(fm.answer.value.length<2 || fm.answer.value.length>20) {
            alert('密码提示不得小于2位或者不嗯给你');
            fm.answer.value='';
            fm.answer.focus();//将焦点移至表单字段
            return false;
        }
        if(fm.answer.value == fm.question.value0) {
            alert('密码提示与回答不能一致');
            fm.answer.value='';
            fm.answer.focus();//将焦点移至表单字段
            return false;
        }
        //验证邮箱
        if(!/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/.test(fm.email.value)) {
            alert('邮件格式不正确');
            fm.email.value = '';
            fm.email.focus();
            return false;
        }
        //验证qq
        if(fm.qq.value !=''){
            if(!/^[1-9]{1}[\d]{4,9}$/.test(fm.qq.value)){
                alert('qq号码不正确');
                fm.qq.value = '';
                fm.qq.focus();
                return false;
            }
        }
        //验证验证码
        if(fm.code.value.length != 4){
            alert('验证码必须四位');
            fm.code.value='';
            fm.code.focus();
            return false;
        }
        return true;
    };
};

