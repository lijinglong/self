<?php

define("TOKEN", "weixinqiang");

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
        if ($this->checkSignature()) {
//            echo $echoStr;
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
    public  function getAccessToken(){
        $res = file_get_contents('access_token.json');
        $results = json_decode($res,true);
        $expires_time = $results["expires_time"];
        $access_token = $results["access_token"];
        if (time() > ($expires_time + 3600)) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
            //使用res获取网址的返回信息
            $res = file_get_contents($url);
            //将接受的json内容转成PHP变量
            $result = json_decode($res,true);
            $access_token = $result['access_token'];
            $expires_time = time();
            file_put_contents('access_token.json','{"access_token":"'.$access_token.'","expires_time":'.$expires_time.'}');
        }
        return $access_token;
    }
    function https_request($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);

        curl_close($curl);
        return $output;
    }
    function getUserData($postObj,$_connect) {
        $access_token = $this->getAccessToken();
        $openid = $postObj->FromUserName;
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $output= $this->https_request($url);
        var_dump($output);
        $output = json_decode($output);
        $this->saveUserInfo($output,$_connect);
        return $output;
    }
    function  saveUserInfo($output,$_connect){
        $this->_query($_connect,"INSERT INTO tg_subscribe (
                                                            tg_openid,
                                                            tg_nickname,
                                                            tg_headimageurl,
                                                            tg_siginflag
                                                            )
                                                            VALUES (
                                                            '{$output['openid']}',
                                                            '{$output['nickname']}',
                                                            '{$output['headimgurl']}',
                                                            FALSE
                                                            )");
        $this->_close($_connect);
    }

    function isSign($_connect,$postObj){
        $sign = $this->_query($_connect,"SELECT tg_signflag from tg_subscribe WHERE tg_openid='{$postObj->FromUserName}'");
        $row = mysql_fetch_assoc($sign);
        return $row['tg_signflag'];
    }
    /***
     * _select_db 选择一个数据库
     */
    function _select_db($_connect) {
        //选择一个数据库
        if(!mysqli_select_db($_connect,DB_NAME)){
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
    public function  saveMessage($postObj) {
        //数据库连接
        define('DB_HOST','159.226.15.68');
        define('DB_USER','root');
        define('DB_PWD','lijinglong');
        define("DB_NAME",'selfdatabase');
        //初始化数据库
        $_connect = new mysqli();
        $_connect = @mysqli_connect(DB_HOST,DB_USER,DB_PWD);
        _select_db($_connect);
        _set_names($_connect);
        if($postObj->MsgType == 'text'){
            _query($_connect,"INSERT INTO weichat (
                                                            tg_tousername,
                                                            tg_fromusername,
                                                            tg_createtime,
                                                            tg_msgtype,
                                                            tg_content,
                                                            tg_msgid,
                                                            tg_uniqid
                                                          )
                                                       VALUES (
                                                            '{$postObj->ToUserName}',
                                                            '{$postObj->FromUserName}',
                                                            '{$postObj->CreateTime}',
                                                            '{$postObj->MsgType}',
                                                            '{$postObj->Content}',
                                                            '{$postObj->MsgId}',
                                                            'weichat'
                                                          )");
        }else {
            _query($_connect,"INSERT INTO weichat (
                                                            tg_tousername,
                                                            tg_fromusername,
                                                            tg_createtime,
                                                            tg_msgtype,
                                                            tg_picurl,
                                                            tg_msgid,
                                                            tg_uniqid
                                                          )
                                                       VALUES (
                                                            '{$postObj->ToUserName}',
                                                            '{$postObj->FromUserName}',
                                                            '{$postObj->CreateTime}',
                                                            '{$postObj->MsgType}',
                                                            '{$postObj->PicUrl}',
                                                            '{$postObj->MsgId}',
                                                            'weichat'
                                                          )");
        }
        _close($_connect);
    }
    function _query($_connect,$_sql) {
        if(!$_result=mysqli_query($_connect,$_sql)) {
            exit ('sql执行失败!'.mysqli_error($_connect));
        }
        return $_result;
    }
    function _close($_connect) {
        if(!mysqli_close($_connect)){
            exit('关闭异常');
        }
    }
    public function responseMsg()
    {

        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch ($RX_TYPE) {
                case "text":
                    $this->saveMessage($postObj);
                    $resultStr = $this->receiveText($postObj);
                    break;
                case "image":
                    $this->saveMessage($postObj);
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
                    $resultStr = $this->receiveEvent($postObj);
                    break;
                default:
                    $resultStr = "unknow msg type: " . $RX_TYPE;
                    break;
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
        $contentStr = "你发送的是文本，内容为：" . $object->Content;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveImage($object)
    {
        $funcFlag = 0;
        $contentStr = "你发送的是图片，地址为：" . $object->PicUrl;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveLocation($object)
    {
        $funcFlag = 0;
        $contentStr = "你发送的是位置，纬度为：" . $object->Location_X . "；经度为：" . $object->Location_Y . "；缩放级别为：" . $object->Scale . "；位置为：" . $object->Label;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveVoice($object)
    {
        $funcFlag = 0;
        $contentStr = "你发送的是语音，媒体ID为：" . $object->MediaId;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveVideo($object)
    {
        $funcFlag = 0;
        $contentStr = "你发送的是视频，媒体ID为：" . $object->MediaId;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveLink($object)
    {
        $funcFlag = 0;
        $contentStr = "你发送的是链接，标题为：" . $object->Title . "；内容为：" . $object->Description . "；链接地址为：" . $object->Url;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }

    private function receiveEvent($object)
    {
        $contentStr = "";
        switch ($object->Event) {
            case "subscribe":
                $contentStr = "欢迎关注方倍工作室";
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