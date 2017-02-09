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
        $param['classify2_id'] = I("post.classify2_id");

        $object = new Common\MallDAOImpl();
        $this->ajaxReturn($object->update($param));

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

        $object = new Common\MallDAOImpl();
        $this->ajaxReturn($object->add($param));
    }

    //增加商品
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