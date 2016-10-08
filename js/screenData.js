/**
 * Created by ljl on 2016/9/28.
 */
function startmarquee(lh, speed, delay) {
    var t;
    var oHeight = 600;
    var p = false;
    var o = document.getElementById("content");
    var preTop = 0;
    o.scrollTop = 0;
    function start() {
        t = setInterval(scrolling, speed);
        o.scrollTop += 1;
    }
    function scrolling() {
        if (o.scrollTop % lh != 0 && o.scrollTop % (o.scrollHeight - oHeight - 1) != 0) {
            preTop = o.scrollTop;
            o.scrollTop += 1;
            if (preTop >= o.scrollHeight || preTop == o.scrollTop) {
                location.replace(location);
                //content.innerHTML = "<p>ceshi js</p>";

                o.scrollTop = 0;

            }
        } else {
            clearInterval(t);
            setTimeout(start, delay);
        }
    }
    setTimeout(start, delay);
}
//window.onload=function(){
//    startmarquee(10, 30, 15);
//};