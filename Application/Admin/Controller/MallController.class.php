<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/2/7
 * Time: 21:25
 */
namespace Admin\Controller;
use Think\Controller;
use Admin\Common;
class MallController extends Controller
{
    //文件上传路径
    static $rootPath = "C:\\Users\\lenovo\\desktop\\file1\\";
    public function index()
    {
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }


        //准备权限
        $this->assign("admin_school",session("admin_school"));
        $this->assign("admin_type",session("admin_type"));
        $this->display();
    }

    //获取商品
    public function get()
    {
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }
        $param_array['draw'] = I("get.draw");
        $param_array['start'] = I("get.start");
        $param_array['length'] = I("get.length");
        $param_array['search'] = I("get.search");
        
        $object = new Common\MallDAOImpl();
        $return_data = $object->get($param_array);
        $this->ajaxReturn($return_data);

    }

    //得到指定的信息
    public function getOneGoods()
    {
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }
        $goods_id = I("get.goods_id");
        $object = new Common\MallDAOImpl();
        $return_data = $object->getOneGoods($goods_id);
        $this->ajaxReturn($return_data);
    }

    //编辑商品
    public function update()
    {
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }

        $param['goods_id'] = I("post.goods_id");
        $param['goods_name'] = I("post.goods_name");
        $param['goods_price'] = I("post.goods_price");
        $param['goods_link'] = I("post.goods_link");
        $param['goods_description'] = I("post.goods_description");
        if($param['classify2_id']=="")
            $param['classify2_id'] = null;


        //文件上传
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->etxs = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = self::$rootPath;
        $upload->savePath = '';
        $upload->autoSub = false;
        $info = null;

        $info = $upload->upload();

        if(!$info) {// 上传错误提示错误信息
            /*var_dump($upload->getError());*/
        }
        else{
            /* // 上传成功 获取上传文件信息
             echo 'ok';*/
        }

        $object = new Common\MallDAOImpl();
        $picture = "";
        foreach ($info as $file) {
            $object->qiniuUpload(self::$rootPath,$file['savename']);
            $picture = $picture.'http://ok9ryp7cb.bkt.clouddn.com/'.$file['savename'].",";
        }
        $param['pictures'] = $picture;
        $result = "";
        $object = new Common\MallDAOImpl();
        if($object->update($param)==true)
            $result = "添加成功！";
        else
            $result = "添加失败！";
        echo $result."</br><a href='./index'> 返 回 </a>";
    }

    //增加商品
    public function add()
    {
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }
        $param['goods_name'] = I("post.goods_name");
        $param['goods_price'] = I("post.goods_price");
        $param['goods_link'] = I("post.goods_link");
        $param['goods_description'] = I("post.goods_description");
        $param['classify2_id'] = I("post.classify2_id");
        if($param['classify2_id']=="")
            $param['classify2_id'] = null;


        //文件上传
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->etxs = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = self::$rootPath;
        $upload->savePath = '';
        $upload->autoSub = false;
        $info = null;

        $info = $upload->upload();

        if(!$info) {// 上传错误提示错误信息
            /*var_dump($upload->getError());*/
        }
        else{
           /* // 上传成功 获取上传文件信息
            echo 'ok';*/
        }

        $object = new Common\MallDAOImpl();
        $picture = "";
        foreach ($info as $file) {
            $object->qiniuUpload(self::$rootPath,$file['savename']);
            $picture = $picture.'http://ok9ryp7cb.bkt.clouddn.com/'.$file['savename'].",";
        }
        $param['pictures'] = $picture;
        $result = "";
        if($object->add($param)==true)
            $result = "添加成功！";
        else
            $result = "添加失败！";
        echo $result."</br><a href='./index'> 返 回 </a>";
    }

    //更新商品在线情况
    public function updateOnline()
    {
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }

        $goods_id = I("goods_id");
        $goods_online = I("goods_online");
        $object = new Common\MallDAOImpl();
        $object->updateOnline($goods_id, $goods_online);
    }

    public function getClassify1()
    {
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }
        $object = new Common\MallDAOImpl();
        $result = $object->getClassify1();
        $this->ajaxReturn($result);
    }

    public function getClassify2()
    {
        if(!session("?admin_id")) {
            header('Location:'.U("Admin/Index/index"));
        }
        $object = new Common\MallDAOImpl();
        $classify1 = I("classify1");
        $result = $object->getClassify2($classify1);
        $this->ajaxReturn($result);
    }
}