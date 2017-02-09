<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/2/7
 * Time: 21:34
 */
namespace Admin\Common;

class MallDAOImpl
{
    public function get($param)
    {
        $return_data['draw'] = $param["draw"];
        $model = M("goods_view");
        $return_data['recordsTotal'] = $model->order("goods_time desc")->limit($param['start'],$param['length'])->count();
        $return_data['recordsFiltered'] = $return_data['recordsTotal'];
        $return_data['data'] = $model->order("goods_time desc")->limit($param['start'],$param['length'])->select();

        foreach($return_data['data'] as $key=>$value){
            $return_data['data'][$key]['edit'] = "";
            $return_data['data'][$key]['look'] = "<img src='".__ROOT__."/Public/assets/advanced-datatable/examples/examples_support/details_open.png'>";
        }
        return $return_data;
    }

    public function update($param)
    {
        $model = M("goods");
        $model->create();
        $model->save();
    }

    public function add($param)
    {
        $model = M("goods");
        $model->add($param);
    }

    //获得一级目录
    public function getClassify1()
    {
        $model = M("goods_classify1");
        return $model->getField("classify1_id, classify1_description");
    }

    //根据一级目录获取二级目录
    public function getClassify2($classify)
    {
        $model = M("goods_classify2");
        return $model->where("classify1_id=$classify")->getField("classify2_id, classify2_description");
    }

    //
    public function updateOnline($goods_id, $goods_online){

        $array['goods_id'] = $goods_id;
        $array['goods_online'] = $goods_online;

        $model = M("goods");
        $model->save($array);
    }
}