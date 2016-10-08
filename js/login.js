/**
 * Created by ljl on 2016/6/20.
 */
window.onload = function() {
    code();
    //登录验证
    var fm = document.getElementsByTagName('form')[0];
    fm.onsubmit = function() {
        //能用客户端验证的计量用客户端
        //验证用户名
        if (fm.username.value.length < 2 || fm.username.value.length > 20) {
            alert('用户名不得小于两位或者大于20位');
            fm.username.value = '';
            fm.username.focus();//将焦点移至表单字段
            return false;
        }
        if (/[<>\'\"\ \  ]/.test(fm.username.value)) {
            alert('用户名不能包含敏感字符');
            fm.username.value = '';
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
        //验证验证码
        if(fm.code.value.length != 4){
            alert('验证码必须四位');
            fm.code.value='';
            fm.code.focus();
            return false;
        }
    }
};
