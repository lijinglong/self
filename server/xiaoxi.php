<?php

define("TOKEN","weixinqiang");
define("APPID","wxca5cce539a328074");
define("APPSECRET","5a564fac80df3bf9baa69eda01b9fa48");
//wx9a67825a81c893c0
//1f534d83b91712342cc3d0f59ecda4ee
$wechatObj = new wechatCallbackapiTest();
if (isset($_GET['echostr'])) {
    $wechatObj->valid();
}else{

    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
    //获取access_token
    public  function getAccessToken($postObj){
//        $res = file_get_contents('access_token.json');
//        $results = json_decode($res,true);
//        $expires_time = $results["expires_time"];
//        $access_token = $results["access_token"];
//        if (time() > ($expires_time + 3600)) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
            //使用res获取网址的返回信息
            $res = file_get_contents($url);
            //将接受的json内容转成PHP变量
            $result = json_decode($res,true);
            $access_token = $result['access_token'];
//            $expires_time = time();
//            file_put_contents('access_token.json','{"access_token":"'.$access_token.'","expires_time":'.$expires_time.'}');
//        }

        return $access_token;
    }
    //替换指定的敏感词
    public function  replace_sensitive_word($word){
        $arr = file("sensitive_word.txt");//敏感词典
        $arr1 = array();
        foreach($arr as $k=>$v){
            $arr1["num".$k] = trim($v);
        }
        return  $content = str_replace($arr1,"*",$word);
    }
    function getUserData($postObj,$_connect,$uniqid,$activityname) {
        $access_token = $this->getAccessToken($postObj);
        $openid = (string)$postObj->FromUserName;
        $subtime = $postObj->CreateTime;
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $output = file_get_contents($url);
        $output = json_decode($output,true);
        $this->saveUserInfo($output,$_connect,$uniqid,$subtime,$activityname);
        //return $output;
//        return $resultStr;
    }
    function saveUserInfo($output,$_connect,$uniqid,$subtime,$activityname){
        $openid = $output['openid'];
        $nickname = $output['nickname'];
        $headimgurl = $output['headimgurl'];
        $signflag = 1;
        $this->_query($_connect,"INSERT INTO tg_subcribe (
                                                            tg_openid,
                                                            tg_nickname,
                                                            tg_headimgurl,
                                                            tg_signflag,
                                                            tg_activityname,
                                                            tg_uniqid,
                                                            tg_subtime
                                                            )
                                                            VALUES (
                                                            '{$openid}',
                                                            '{$nickname}',
                                                            '{$headimgurl}',
                                                            '{$signflag}',
                                                            '{$activityname}',
                                                            '{$uniqid}',
                                                            '{$subtime}'
                                                            )");
        $this->_close($_connect);
    }
    /***
     * _select_db 选择一个数据库
     */
    function _select_db($_connect,$dbname) {
        //选择一个数据库
        if(!mysqli_select_db($_connect,$dbname)){
            exit('找不到数据库!');
        }
    }

    /***
     * _set_names() 设置字符集
     */
    function _set_names($_connect) {
        if(!mysqli_set_charset($_connect,'UTF8')){

            exit('字符集错误!');
        }
    }
    function _query($_connect,$_sql) {
        if(!$_result=mysqli_query($_connect,$_sql)) {
            exit ('sql执行失败!'.mysql_error($_connect));
        }
        return $_result;
    }
    function _close($_connect) {
        if(!mysqli_close($_connect)){

            exit('关闭异常');
        }
    }
    function userTextEncode($str){
        if(!is_string($str))return $str;
        if(!$str || $str=='undefined')return '';

        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i",function($str){
            return addslashes($str[0]);
        },$text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
        return json_decode($text);
    }
    public function  saveMessage($postObj,$_connect) {

        include('../emoji.php');
        $username = $postObj->FromUserName;
        $result = $this->_query($_connect,"SELECT * from tg_subcribe WHERE tg_openid='{$username}'order by tg_subtime DESC LIMIT 1");
        $row = mysqli_fetch_assoc($result);
        $nickname =$row['tg_nickname'];
        $headimgurl = $row['tg_headimgurl'];
        $uniqid = $row['tg_uniqid'];
        $createtime = $postObj->CreateTime;
        $msgtype = $postObj->MsgType;
        $content =$this->replace_sensitive_word($postObj->Content);
        $content = $this->userTextEncode($content);
        $msgid = $postObj->MsgId;
        if($postObj->MsgType == 'text'){
            $this->_query($_connect,"INSERT INTO weichat (
                                                           tg_headimgurl,
                                                           tg_fromusername,
                                                           tg_createtime,
                                                           tg_msgtype,
                                                           tg_content,
                                                           tg_msgid,
                                                           tg_uniqid
                                                         )
                                                     VALUES (
                                                            '{$headimgurl}',
                                                            '{$nickname}',
                                                            '{$createtime}',
                                                            '{$msgtype}',
                                                            '{$content}',
                                                            '{$msgid}',
                                                            '{$uniqid}'
                                                          )");
        }else if($postObj->MsgType =='image') {
            $picUrl = $postObj->PicUrl;
            $this->_query($_connect,"INSERT INTO weichat (
                                                            tg_headimgurl,
                                                            tg_fromusername,
                                                            tg_createtime,
                                                            tg_msgtype,
                                                            tg_picurl,
                                                            tg_msgid,
                                                            tg_uniqid
                                                          )
                                                       VALUES (
                                                            '{$headimgurl}',
                                                            '{$nickname}',
                                                            '{$createtime}',
                                                            '{$msgtype}',
                                                            '{$picUrl}',
                                                            '{$msgid}',
                                                            '{$uniqid}'
                                                          )");
        }
        $this->_close($_connect);
    }

    public function responseMsg()
    {

        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            define('DB_HOST','localhost');
            define('DB_USER','ljl');
            define('DB_PWD','ljlself5881');
            define("DB_NAME",'self');
            //初始化数据库
            $_connect = @mysqli_connect(DB_HOST,DB_USER,DB_PWD);
            $this->_select_db($_connect,DB_NAME);
            $this->_set_names($_connect);
            $openid =(string)$postObj->FromUserName;
            $uniqid =(string)$postObj->Content;
            $resultStr = '';
            //判断关注用户数据表是否存在此用户
            $resultSubcribe = $this->_query($_connect,"SELECT * from tg_subcribe where tg_openid='{$openid}' ORDER by tg_subtime DESC LIMIT 1");
            if(mysqli_num_rows($resultSubcribe)!=0){
                switch ($RX_TYPE) {
                    case "text":
                        $content = $postObj->Content;
                        if($content=='qd'){
                            $res = mysqli_fetch_assoc($resultSubcribe);
                            $tg_id = $res['tg_id'];
                            $this->_query($_connect,"DELETE from tg_subcribe where tg_id='{$tg_id}'");
                        }else{
                            //判断用户发送的内容是否是签到命令
                            $resultr = $this->_query($_connect,"SELECT * from tg_activity where tg_uniqid='{$content}' LIMIT 1");
                            if(mysqli_num_rows($resultr)!=0){
                                //判断用户表中是否有此签到命令，防止重复签到
                                $resultSubcribe = $this->_query($_connect,"SELECT * from tg_subcribe where tg_uniqid='{$content}' LIMIT 1");
                                if(mysqli_fetch_assoc($resultSubcribe)!=0){
                                    //若已签到，则将信息存到消息表中显示
                                    $this->saveMessage($postObj,$_connect);
                                    $resultStr = $this->receiveText($postObj);
                                }else{
                                    $resulstr = mysqli_fetch_assoc($resultr);
                                    $activityname = $resulstr['tg_activityname'];
                                    $this->getUserData($postObj,$_connect,$uniqid,$activityname);
                                    $funcFlag = 0;
                                    $contentStr = '您已签到成功，现在可以发现消息进行微信墙互动。您参加的活动为:《'.$resulstr['tg_activityname'].'》。';
                                    $resultStr = $this->transmitText($postObj, $contentStr, $funcFlag);
                                }
                            }else{
                                //若已签到，则将信息存到消息表中显示
                                $this->saveMessage($postObj,$_connect);
                                $resultStr = $this->receiveText($postObj);
                            }
                        }
                        break;
                    case "image":
                        $this->saveMessage($postObj,$_connect);
                        $resultStr = $this->receiveImage($postObj);
                        break;
                    case "location":
                        $resultStr = $this->receiveLocation($postObj);
                        break;
                    case "voice":
                        $resultStr = $this->receiveVoice($postObj);
                        break;
                    case "video":
                        $resultStr = $this->receiveVideo($postObj);
                        break;
                    case "link":
                        $resultStr = $this->receiveLink($postObj);
                        break;
                    case "event":
                        $resultStr = $this->receiveEvent($postObj,$_connect);
                        break;
                    default:
                        $resultStr = "unknow msg type: " . $RX_TYPE;
                        break;
                }
            }else {
                //判断是否是正确的活动唯一标识（签到命令）
                $resultr = $this->_query($_connect,"SELECT * from tg_activity where tg_uniqid='{$uniqid}' LIMIT 1");
                if(mysqli_num_rows($resultr)!=0){
                    $resulstr = mysqli_fetch_assoc($resultr);
                    $activityname = $resulstr['tg_activityname'];
                    $this->getUserData($postObj,$_connect,$uniqid,$activityname);
                    $funcFlag = 0;
                    $contentStr = '您已签到成功，现在可以发现消息进行微信墙互动。您参加的活动为:《'.$resulstr['tg_activityname'].'》。';
                    $resultStr = $this->transmitText($postObj, $contentStr, $funcFlag);
                }else{
                    $funcFlag = 0;
                    $contentStr = "对不起，您输入的签到命令错误，请核对后重新输入，谢谢！";
                    $resultStr = $this->transmitText($postObj, $contentStr, $funcFlag);
                }
              // $resultStr = $this->saveUserInfoAndActivity($postObj,$_connect,$uniqid);
            }
            echo $resultStr;
        } else {
            echo "";
            exit;
        }
    }

    private function receiveText($object)
    {

        $funcFlag = 0;
        $contentStr = '发送成功，可以在大屏幕上查看您的消息了';
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveImage($object)
    {
        $funcFlag = 0;
        $contentStr = '发送成功，可以在大屏幕上显示您的消息了';
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveLocation($object)
    {
        $funcFlag = 0;
        $contentStr = '对不起暂时不接受此类型的消息';
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveVoice($object)
    {
        $funcFlag = 0;
        $contentStr = '对不起暂时不接受此类型的消息';
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveVideo($object)
    {
        $funcFlag = 0;
        $contentStr = '对不起暂时不接受此类型的消息';
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveLink($object)
    {
        $funcFlag = 0;
        $contentStr = '对不起暂时不接受此类型的消息';
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveEvent($object,$connect)
    {
        $contentStr = "";
        switch ($object->Event) {
            case "subscribe":
                $contentStr = "欢迎关注中国科普博览，请回复“self”进行签到，签到成功即可参与大屏幕讨论。";
                break;
            case "unsubscribe":
                $contentStr = "";
                break;
            case "CLICK":
                switch ($object->EventKey) {
                    default:
                        $contentStr = "你点击了: " . $object->EventKey;
                        break;
                }
                break;
            default:
                $contentStr = "receive a new event: " . $object->Event;
                break;
        }
        $resultStr = $this->transmitText($object, $contentStr);
        return $resultStr;
    }

    private function transmitText($object, $content, $flag = 0)
    {
        $textTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[%s]]></Content>
    <FuncFlag>%d</FuncFlag>
</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
        return $resultStr;
    }
}
?>