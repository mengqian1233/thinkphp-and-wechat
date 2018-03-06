<?php
/**
 * Created by PhpStorm.
 * User: xiaoqian
 * Date: 2017/12/5
 * Time: 14:29
 */

namespace app\index\controller;


use app\common\model\Customer;
use think\Controller;
use think\Session;
use think\Config;

/*
 * 第二步作用：由yonghuController带有操作方法到OauthCode方法进行构造code授权路径*/
class OauthController extends Controller
{
    private $wxcfg;

    private $redirect_map = [
        'user-help'      => '/index/yonghu/help',
        'user-index'     => '/index/yonghu/index',
    ];

    public function __construct()
    {
        //将继承父类的构造方法在子类中能够使用
        parent::__construct();
        //将appid,secret,token进行赋值
        $this->wxcfg = \weixin\wxBasic::getConfig();
    }

    public function OauthCode()
    {
        //授权回调路径
        $return_uri = Config::get('host') . '/index/oauth/OauthReturn';
        //获取code授权
        $get_code_url = 'https://open.weixin.qq.com/connect/oauth2/authorize';
        //构造参数
        $get_code_url .= '?appid='.$this->wxcfg['appid'] .
            '&redirect_uri='. urlencode($return_uri) .   //urlencode: 进行转码
            '&response_type=code' .
            '&scope=snsapi_userinfo' .//属于微信开发者文档第二种类型
            '&state=' . input('get.redirect') . //此处获取到YonghuController传递的redirect
            '#wechat_redirect';

        //header('location:'.$get_code_url);
        return $this->redirect($get_code_url);
    }

    public function OauthReturn(){
        $code = isset($_GET['code'])?$_GET['code']:'';
        if (empty($code)) {
            exit('Error: code is empty!');
        }

        $oauth_token_api = 'https://api.weixin.qq.com/sns/oauth2/access_token'.
            '?appid=' . $this->wxcfg['appid'] .
            '&secret=' . $this->wxcfg['secret'] .
            '&code=' . $code .
            '&grant_type=authorization_code';

        $wxcurl = new \weixin\wxCURL;
        $response = $wxcurl->get($oauth_token_api);

        $oauth_token = json_decode($response,true);

        if (!isset($oauth_token['access_token'])) {
            exit('Error:failed to get access_token.');
        }

        $oauth_info_api = 'https://api.weixin.qq.com/sns/userinfo' .
            '?access_token=' . $oauth_token['access_token'] .
            '&openid='.$oauth_token['openid'] .
            '&lang=zh_CN';

        $response = $wxcurl->get($oauth_info_api);
        $info = json_decode($response,true);
        if (isset($info['errcode'])) {
            exit($response);
        }
        //数据库上传
        /*
         * 获取到用户的openid之后，和数据库对比，数据库有此用户更新用户的姓名、头像等信息
         * 数据库没有此用户直接将用户的信息保存到数据库*/
        //1、获取用户的openid
        $openid = $info['openid'];
        //2、判断数据库有没有此用户，判断条件为openid
        $m = new Customer();
        $result = $m -> where('openid',$openid)->find();
        if (empty($result)) {
            $m ->save($info);
        } else {
            $m->where('openid',$openid)->update([
                'nickname' => $info['nickname'],
                'sex' => $info['sex'],
                'headimgurl' => $info['headimgurl']
            ]);
        }
        //set login
        Session::set('openid',$info['openid']);
        $ri = input('?get.state')?input('get.state'):'';

        if (isset($this->redirect_map[$ri])) {
            return $this->redirect(Config::get('host') . $this->redirect_map[$ri]);
        } else {
            exit("Unknow redirect");
        }

    }


}
