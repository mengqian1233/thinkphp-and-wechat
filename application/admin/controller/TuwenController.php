<?php
/**
 * Created by PhpStorm.
 * User: xiaoqian
 * Date: 2017/12/13
 * Time: 10:40
 */

namespace app\admin\controller;


use app\common\model\Sucai;
use app\common\model\Tuwen;
use think\Request;
use weixin\wxMaterial;

class TuwenController extends BaseController
{
    protected $fields = ['title', 'content','thumb_media_id','show_cover_pic','content_source_url','author','digest'];

    public function index()
    {
        //获取分页数据
        $rows = Tuwen::paginate();
        //显示视图
        $this->assign('rows', $rows);
        return $this->fetch();
    }
    public function create()
    {
        $s = new Sucai();
        $result  = $s->where('type','image')->select();
        $this->assign('sucai',$result);
        return $this->fetch();
    }
    public function edit($id)
    {
        $s = new Sucai();
        $result  = $s->where('type','image')->select();
        $this->assign('sucai',$result);
        $this->assign('row',Tuwen::get($id));
        return $this->fetch();
    }
    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $tuwen = new Tuwen();
        //获取用户添加的内容
        foreach ($this->fields as $f) {
            $tuwen->$f = input($f);
        }
        //设置图文数据
        $news_article=[
            'articles'=>[
                [
                    'title'=>input('title'),
                    'thumb_media_id'=>input('thumb_media_id'),
                    'author'=>input('author'),
                    'digest'=>input('digest'),
                    'show_cover_pic'=>input('show_cover_pic'),
                    'content'=>input('content'),
                    'content_source_url'=>input('content_source_url')
                ],//支持多个图文
            ]
        ];
        //插入到数据库中
        if ($tuwen->save()) {
            //return $this->success('数据插入成功','/admin/menu');
            $wxm = new wxMaterial();
            $json = $wxm->createNews($news_article);
            $result2 = json_decode($json, true);
            if ($result2['media_id'] != 'NULL') {
                $tuwen['media_id'] = $result2['media_id'];
                if ($tuwen->save()) {
                    return $this->success('图文上传成功', 'http://mqian.wywwwxm.com/tp5/public/index.php/admin/tuwen/index');
                } else {
                    // 上传失败获取错误信息
                    echo $this->getError();
                }
            }

        }

    }
    public function update()
    {
        $tuwen = new Tuwen();
        //获取用户添加的内容
        foreach ($this->fields as $f) {
            $tuwen->$f = input($f);
        }
        $id=input('id');
        $result = $tuwen->where('id',$id)->find();
        if($result){
            $news_article=[
                'media_id'=>$result['media_id'],
                'index'=>0,
                'articles'=>[
                    'title'=>input('title'),
                    'thumb_media_id'=>input('thumb_media_id'),
                    'author'=>input('author'),
                    'digest'=>input('digest'),
                    'show_cover_pic'=>input('show_cover_pic'),
                    'content'=>input('content'),
                    'content_source_url'=>''
                ]
            ];
            $wxm = new wxMaterial();
            $json = $wxm->setNews($news_article);
            $result2 = json_decode($json, true);
            $matter_errcode=$result2['errcode'];
            if($matter_errcode!='0'){
                return $this->error('素材修改上传微信服务器失败','http://mqian.wywwwxm.com/tp5/public/index.php/admin/tuwen/index');
            }
            //插入到数据库中
            if ($tuwen->isUpdate(true)->save(['id'=>$id])) {
                return $this->success('图文素材修改成功','http://mqian.wywwwxm.com/tp5/public/index.php/admin/tuwen/index');
                //return $this->success('数据插入成功','/admin/menu');
                            }
            else{
                return $this->error('素材修改保存数据库失败');
            }
        }
        }
    public function delete($id)
    {
        /* echo 'delete'.$id;*/
        $new = Tuwen::get($id);
        if ($new->delete()){
            return "ok";
        }else{
            return "error";
        }
    }

}
