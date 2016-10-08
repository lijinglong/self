/**
 * Created by ljl on 2016/6/16.
 */
window.onload = function() {
    var img = document.getElementsByTagName('img');
    for (i= 0 ;i < img.length;i ++) {
        img[i].onclick = function() {
            _opener(this.alt);
        };
    }
};
function _opener(imgsrc) {
    //opener表示父窗口


        opener.document.getElementById('faceimg').src = imgsrc;
        opener.document.register.face.value = imgsrc;
        opener.document.member.face.value=imgsrc;

}
