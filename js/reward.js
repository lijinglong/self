/**
 * Created by ljl on 2016/8/26.
 */

window.onload = function(){
    var screen = document.getElementById('screen');
    screen.onclick = function(){
        window.location.href = 'screen.php?uniqid=self';
    };

};
