/**
 * Created by ljl on 2016/6/20.
 */
function code() {
    var code = document.getElementById('code');
    code.onclick = function() {
        this.src='code.php?tm='+Math.random();
    };
}
