<?php

namespace app\index\controller;

class WxhtalkController
{
    //填写微信公众号设置好的token
    private $token = 'mytest';
    private $msgTpl='';
    private $msgType='text';

    private $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
    private $imageTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Image>
            <MediaId><![CDATA[%s]]></MediaId>
            </Image>
            </xml>";
    private $voiceTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Voice>
            <MediaId><![CDATA[%s]]></MediaId>
            </Voice>
            </xml>";


    //验证流程开始
    private function checkSignature()
    {
        $signature = request_data('get','signature');
        $timestamp = request_data('get','timestamp');
        $nonce = request_data('get','nonce');
        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function valid()
    {
        $echoStr = request_data('get','echostr');
        if($this->checkSignature()){
            exit($echoStr);
        }
    }
    //end

    public function responseMsg()
    {
        //$this->valid();exit;
        /*
        HTTP_RAW_POST_DATA这种获取POST数据流的方式只能在低于5.6版本
        的 PHP上运行，这里使用的PHP版本是7.0
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        */
        //获取 POST数据流
        $postStr = file_get_contents('php://input', 'r');

        //如果数据不为空则进行处理
        if (!empty($postStr)){

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //把 PHP对象的变量转换成关联数组
            $wxmsg = get_object_vars($postObj);
            //解析后的数据交给预处理方法去处理
            $ret =$this->preMsgHandle($wxmsg);
            //设置模板变量
            $mtpl = $this->msgTpl;
            //消息创建时间
            /*$tm=time();
            $type = $wxmsg['MsgType'];
            $mtpl='';
            if ('text'== $type ){
                $mtpl='textTpl';
                $ret=$wxmsg['Content'];
            }elseif('image' == $type){
                $mtpl='imageTpl';
                $ret=$wxmsg['MediaId'];
            }elseif ('voice'  == $type){
                $mtpl = 'voiceTpl';
                $ret = $wxmsg['MediaId'];
            }*/
            $resultStr = '';
            $resultStr = sprintf(
                $this->$mtpl,
                $wxmsg['FromUserName'],
                $wxmsg['ToUserName'],
                time(),
                $this->msgType,
                $ret);
//exit('success'); 处理用户的请求，但是不回复任何消息
            exit($resultStr);
        }
        else {
            exit('');
        }
    }
        //消息预处理方法
    private function preMsgHandle($wxmsg)
    {
        //动态设置消息模板变量
        $this->msgTpl = $wxmsg['MsgType'].'Tpl';
        $this->msgType = $wxmsg['MsgType'];

        switch ($wxmsg['MsgType'])
        {
            case  'text':
                //文本类型直接返回消息内容
                //文本消息处理方法
                return $this->textHandle($wxmsg);
                break;
            case  'image':
                return $wxmsg['MediaId'];
                break;
            case  'voice':
                return $wxmsg['MediaId'];
                break;
            case  'video':
                //如果是视频消息，返回文本消息错误提示
                $this->msgTpl = 'textTpl';
                $this->msgType='msgType';
                return '该类型不被支持';
                break;
            case  'event':
                //如果是VIEW类型则记录时间消息的url
                /*if($wxmsg['Event'] == 'VIEW') {
                    file_put_contents('wx_event.log',
                        $wxmsg['EventKey'] . "\n",
                        FILE_APPEND);//不要重置的文件内容，直接在后边追加内容
                }*/
                return $this->eventHandle($wxmsg);

                break;
            default:return 'null';
        }
    }
    //文本消息处理方法，实现关键词自动回复
    private function textHandle($wxmsg)
    {
        switch ($wxmsg['Content']){
            case '?':
            case 'help':
                return '请点击：我的戴尔-》常见问题，查看帮助';
                break;
            case 'info':
                return 'programmer';
                break;
            default:
                return $wxmsg['Content'];
        }


    }
    private function eventHandle($wxmsg)
    {
        //保存事件信息
        $event_log = time()."|".$wxmsg['Event'];
        switch ($wxmsg['Event']){
            //页面跳转事件
            case 'VIEW':
                $event_log.="|". $wxmsg['EventKey'];
                break;
                //位置信息事件
            case 'LOCATION':
                $event_log.= "| lat<".
                    $wxmsg['Latitude'].
                    ">lng<".
                    $wxmsg['Longitude'].
                    ">";
                break;
                //关注公众号
            case 'subscrible':
                $event_log.="|".$wxmsg['FromUserName'];
                break;
            case 'unsubcrible':
                break;
                //点击菜单返回消息事件
            case 'CLICK':
                $event_log.=$wxmsg['EventKey'];
                break;
            case 'SCAN':
                break;
            default: ;
        }
        $event_log .= "\n";
        file_put_contents('wx_event.log',$event_log,FILE_APPEND);
        /*消息事件返回测试消息
        实际测试发现只有关注公众号时的事件通知支持返回消息
        以下几行代码在生产环境的事件处理中可以去掉*/
        $this->msgTpl='textTpl';
        $this->msgType='textType';
        return 'this is test info';
    }

}
