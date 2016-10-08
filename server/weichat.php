<?php
//define your token
define("TOKEN", "weixinqiang");
define("SUBSCRIBE","lijinglong");
require '../common.inc.php';
$wechatObj = new wechatCallbackapiTest();
if(!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
} else {
    $wechatObj->valid();
}
class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $from_msgType = $postObj->MsgType;
            switch($from_msgType) {
                case "event":
                    $this->reviceEvent($postObj);
                    break;
                case "text":
                    $content = "你已经关注了";
                    $resultStr = $this->revicesmitText($postObj,$content);
                    return $resultStr;
                    break;
            }
        }else {
            echo "";
            exit;
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
    function getUserData($userName) {
        $access_token = getAccessToken();
        $openid = $userName;
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
        $output= https_request($url);
        var_dump($output);
        $output = json_decode($output);
        return $output;
    }

    public function reviceEvent($postObj) {
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $event = $postObj->Event;
        switch ($event){
            case "subscribe":
                $contentStr = SUBSCRIBE;
//                $info = getUserData($fromUsername);
                $resultStr = $this->revicesmitText($postObj,'谢谢关注'.SUBSCRIBE);
                return $resultStr;
                break;
            case "SCAN":
                //用户已经关注后二维码扫描事
//                $resultStr = getUserData($fromUsername);
                $resultStr = $this->revicesmitText($postObj,'你已经关注了');
                return $resultStr;
                break;
        }
    }
    public function reviceNormal($postObj) {
        $from_msgType = $postObj->MsgType;
        switch($from_msgType) {
            case "text":
                $resultStr = $this->revicesmitText($postObj,'你已经关注了');
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
                                                            '{$_postObj->ToUserName}',
                                                            '{$_postObj->FromUserName}',
                                                            '{$_postObj->CreateTime}',
                                                            '{$_postObj->MsgType}',
                                                            '{$_postObj->Content}',
                                                            '{$_postObj->MsgId}',
                                                            'weichat'
                                                          )");
                return $resultStr;
                break;
            case "image":

                break;
            case "voice":

                break;
            case "video":

                break;
            case "shortvideo":

                break;
            case "location":

                break;
            case "link":

                break;
        }
    }

    public function revicesmitText($postObj,$content) {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
        $resultStr = sprintf($textTpl,$postObj->FromUserName,$postObj->ToUserName,time(),$content);
        return $resultStr;
    }
    public  function revicesmitMusic($postObject,$content,$funFlag = 0) {
        $musicTpl = "<xml>
             <ToUserName><![CDATA[%s]]></ToUserName>
             <FromUserName><![CDATA[%s]]></FromUserName>
             <CreateTime>%s</CreateTime>
             <MsgType><![CDATA[%s]]></MsgType>
             <Music>
             <Title><![CDATA[%s]]></Title>
             <Description><![CDATA[%s]]></Description>
             <MusicUrl><![CDATA[%s]]></MusicUrl>
             <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
             </Music>
             <FuncFlag>%d</FuncFlag>
             </xml>";
        $resultStr = sprintf($musicTpl,$postObject->FromUserName,$postObject->ToUserName,time(),"music",$content['Title'],
            $content['Description'],$content['MusicUrl'],$content['HQMusicUrl'],$funFlag);
        return $resultStr;
    }
    public function revicesmitNews($postObj,$arr_item,$num = 1){
        if(!is_array($arr_item)){
            return;
        }else {
            $itemTpl = "<item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>";
            $item_str = "";
            foreach ($arr_item as $item) {
                $item_str .=sprintf($itemTpl,$item['Title'],$item['Description'],$item['PicUrl'],$item['Url']);
            }
            $newsTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <Content><![CDATA[]]></Content>
                    <ArticleCount>%d</ArticleCount>
                    <Articles>
                    $item_str</Articles>
                    </xml>";
            $resultStr = sprintf($newsTpl,$postObj->FromUserName,$postObj->ToUserName,time(),count($arr_item));
            return $resultStr;
        }
    }
}

?>