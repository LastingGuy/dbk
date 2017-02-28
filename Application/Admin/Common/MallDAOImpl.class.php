<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2017/2/7
 * Time: 21:34
 */
namespace Admin\Common;

import("Org.Qiniu.vendor.autoload", null, ".php");

// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;


class MallDAOImpl
{
    public function get($param)
    {
        $return_data['draw'] = $param["draw"];
        $model = M("goods_view");
        $return_data['recordsTotal'] = $model->order("goods_time desc")->limit($param['start'], $param['length'])->count();
        $return_data['recordsFiltered'] = $return_data['recordsTotal'];
        $return_data['data'] = $model->order("goods_time desc")->limit($param['start'], $param['length'])->select();

        foreach ($return_data['data'] as $key => $value) {
            $return_data['data'][$key]['edit'] = "";
            $return_data['data'][$key]['look'] = "<img src='" . __ROOT__ . "/Public/assets/advanced-datatable/examples/examples_support/details_open.png'>";
        }
        return $return_data;
    }

    public function update($param)
    {
        $model = M("goods");
        return $model->save($param);
    }

    public function add($param)
    {
        $count = 0;
        $model = M("goods");
        $param['goods_id'] = 1;
        $param['goods_page_view'] = 0;
        $param['goods_online'] = 0;
        $param['goods_time'] = date("Y-m-d H:i:s", time());
        do{
            $result = 0;
            try{
                $result = $model->add($param);
            }
            catch(\Exception $e){
                $param['goods_id'] = $this->buildId();
                $count++;
                if($count>10)
                    return false;
            }
        }while($result==0);
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
    public function updateOnline($goods_id, $goods_online)
    {

        $array['goods_id'] = $goods_id;
        $array['goods_online'] = $goods_online;

        $model = M("goods");
        $model->save($array);
    }

    public function getOneGoods($goods_id)
    {
        $model = M("goodsView");
        return $model->find($goods_id);
    }

    //随机生成6位带字母数字的goods_id
    private function buildId()
    {
        $arr = array_merge(range(0, 9), range('a', 'z'));

        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < 6; $i++) {
            $rand = mt_rand(0, $arr_len - 1);
            $str .= $arr[$rand];
        }
        return $str;
    }

    //七牛上传
    public function qiniuUpload($rootpath,$filename)
    {
        // 需要填写你的 Access Key 和 Secret Key
        $accessKey = 'WNYHEJ0UMEXmA8QGCjZ1IBqKPfh84-9flPX8a_ha';
        $secretKey = 'JjmpZjN9woi4bBU2pfUtiquQ88aeczGNfwai2EJm';

        // 构建鉴权对象
        $auth = new Auth($accessKey,$secretKey);

        // 要上传的空间
        $bucket = 'jingl';
        // 生成上传 Token
        $token = $auth->uploadToken($bucket);

        // 要上传文件的本地路径
        $filePath = $rootpath."\\".$filename;
        $key = $filename;
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        echo "\n====> putFile result: \n";
        if ($err !== null) {
            //var_dump($err)    ;
        } else {
           // var_dump($ret);
        }
    }
}