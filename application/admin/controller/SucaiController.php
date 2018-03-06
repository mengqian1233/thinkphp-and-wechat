<?php
/**
 * Created by PhpStorm.
 * User: xiaoqian
 * Date: 2017/11/27
 * Time: 8:24
 */

namespace app\admin\controller;

use app\common\model\Sucai;
use think\Controller;
use think\Request;
use EasyWeChat\Foundation\Application;
use weixin\wxMaterial;

class SucaiController extends BaseController
{
    //数据添加或修改时所使用的字段名称
    protected $fields = ['title', 'type', 'new_title', 'instruction', 'content', 'media_id', 'weixin_url', 'url'];

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //获取分页数据
        $rows = Sucai::paginate();
        //显示视图
        $this->assign('rows', $rows);
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function image()
    {
        return $this->fetch();
    }

    public function saveImage(Request $request)
    {
        $image[] = '';
        $news = new Sucai();
        foreach ($this->fields as $f) {
            $news->$f = input($f);
        }

        $file = request()->file('image');
//          移动到框架应用根目录/public/uploads/ 目录下,对上传图片进行验证
        if ($file) {
            $info = $file->validate(['size' => 3145728, 'ext' => 'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads', '');
            if ($info) {
                // 成功上传后 获取上传信息
                $image['url'] = ROOT_PATH . 'public' . DS . 'uploads' . DS . $info->getSaveName();
                $news['url'] = $image['url'];
                if ($news->save()) {
                    $token = \weixin\wxToken::getToken();
                    $options = [
                        /* 账号基本信息，请从微信公众平台/开放平台获取*/
                        'app_id' => 'wxf494c33cf49eeedb',         // AppID
                        'secret' => '9a830553b63877824a984af5d6a4a750',     // AppSecret
                        'token' => $token,          // Token
                        'aes_key' => '',                    // EncodingAESKey，安全模式下请一定要填写！！！
                    ];
                    $app = new Application($options);
                    $sucai = $app->material;
                    $result = $sucai->uploadImage($news['url']);
                    $media = json_decode($result, true);
                    $news['media_id'] = $media['media_id'];
                    $news['weixin_url'] = $media['url'];
                    if ($news->save()) {
                        return $this->success('素材上传成功', 'http://mqian.wywwwxm.com/tp5/public/index.php/admin/sucai/index');

                    } else {
                        return $this->error('素材上传失败');
                    }
                }
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

    /*$news['url'] = input('url');
    $news['title'] = input('title');
    $news['content'] = input('content');*/
    public function thumb()
    {
        return $this->fetch();
    }

    public function saveThumb(Request $request)
    {
        $thumb[] = '';
        $news = new Sucai();
        foreach ($this->fields as $f) {
            $news->$f = input($f);
        }

        $file = request()->file('thumb');
//          移动到框架应用根目录/public/uploads/ 目录下,对上传图片进行验证
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads', '');
            if ($info) {
                // 成功上传后 获取上传信息
                $thumb['url'] = ROOT_PATH . 'public' . DS . 'uploads' . DS . $info->getSaveName();
                $news['url'] = $thumb['url'];
                if ($news->save()) {
                    $token = \weixin\wxToken::getToken();
                    $options = [
                        /* 账号基本信息，请从微信公众平台/开放平台获取*/
                        'app_id' => 'wxf494c33cf49eeedb',         // AppID
                        'secret' => '9a830553b63877824a984af5d6a4a750',     // AppSecret
                        'token' => $token,          // Token
                        'aes_key' => '',                    // EncodingAESKey，安全模式下请一定要填写！！！
                    ];
                    $app = new Application($options);
                    $sucai = $app->material;
                    $result = $sucai->uploadThumb($news['url']);
                    $media = json_decode($result, true);
                    $news['media_id'] = $media['media_id'];
                    $news['weixin_url'] = $media['url'];
                    if ($news->save()) {
                        return $this->success('素材上传成功', 'http://mqian.wywwwxm.com/tp5/public/index.php/admin/sucai/index');

                    } else {
                        return $this->error('素材上传失败');
                    }
                }
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
    }

    public function video()
    {
        return $this->fetch();
    }

    public function saveVideo()
    {
        $menu = new Sucai();
        //获取用户添加的内容
        foreach ($this->fields as $f) {
            $menu->$f = input($f);
        }
        $file = request()->file('video');
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads', '');
            if ($info) {
//                dump($info->getSaveName());exit;
                $menu['url'] = $info->getSaveName();
                $video['url'] = ROOT_PATH . 'public' . DS . 'uploads' . DS . $menu['url'];
//                dump($menu['src_path']);exit;
                if ($menu->save()) {
                    $wxm = new wxMaterial();
                    $result = $wxm->uploadVideo($video['url'], $menu['new_title'], $menu['instruction']);
                    $result2 = json_decode($result, true);
                    if ($result2) {
                        $menu['media_id'] = $result2['media_id'];
                        if ($menu->save()) {
                            return $this->success('视频上传成功', 'http://mqian.wywwwxm.com/tp5/public/index.php/admin/sucai/index');
                        } else {
                            // 上传失败获取错误信息
                            echo $file->getError();
                        }
                    }
                }
            }else {
                exit("test error");
            }
        }
    }

    /*public function voice()
    {
        return $this->fetch();
    }
    public function saveVoice(Request $request)
    {
        $menu = new Sucai();
        //获取用户添加的内容
        foreach ($this->fields as $f) {
            $menu->$f = input($f);
        }
        $file = request()->file('voice');
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads', '');
            if ($info) {
//                dump($info->getSaveName());exit;
                $menu['url'] = $info->getSaveName();
                $voice['url'] = ROOT_PATH . 'public' . DS . 'uploads' . DS . $menu['url'];
//                dump($menu['src_path']);exit;
                if ($menu->save()) {
                    $wxm = new wxMaterial();
                    $result = $wxm->uploadVideo($voice['url'], $menu['new_title'], $menu['instruction']);
                    $result2 = json_decode($result, true);
                    if ($result2['media_id'] != 'NULL') {
                        $menu['media_id'] = $result2['media_id'];
                        if ($menu->save()) {
                            return $this->success('声音上传成功', '/admin/sucai/index');
                        } else {
                            // 上传失败获取错误信息
                            echo $file->getError();
                        }
                    }
                }
            }else {
                exit("test error");
            }
        }
    }*/

           /* //插入到数据库中
            if ($news->save()) {
                //return $this->success('数据插入成功','/admin/menu');
                //插入成功则创建菜单api
                switch ($news['type']) {
                    case "image":
                        $result = $sucai->uploadImage($image['url']);
                        break;
                    case "voice":
                        echo "i is bar";
                        break;
                    case "video":
                        echo "i is cake";
                        break;
                    case "thumb":
                        echo "i is cake";
                        break;
                        dump($image['url']); //echo $info->getFilename();
                }
            } else {
                return $this->error('数据插入失败');
            }*/
    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {

}

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $news = Sucai::get($id);
        foreach ($this->fields as $f)
        {
            $news->$f = input($f);
        }
        $news['url'] = input('url');
        $news['title'] = input('title');
        $news['content'] = input('content');
        //更新到数据库中
        if ($news -> save()){
            //return $this->success('数据更新成功','/admin/news');
            $m= new \weixin\wxMenu();
            return $m->createMenu($news['value']);
        }else{
            return $this->error('数据更新失败');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        /* echo 'delete'.$id;*/
        $new = Sucai::get($id);
        if ($new->delete()){
            return "ok";
        }else{
            return "error";
        }
    }
    /*显示要上传素材的各个视图方法*/

}