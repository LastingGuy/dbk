<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extends Controller{
    public function index(){
        $this->display();
    }
    public function add(){
        //判断是否位post提交
        if(IS_POST){
            $pickup = M("Pickup");  //实例化表
            if($pickup->create()){ //创建数据成功
                if($pickup->add()){ //提交成功
                    echo "提交成功";
                }else{ //提交失败
                    echo "提交失败";
                }
            }else{ //创建数据失败
                $msg = $pickup->getError();  //获取数据创建失败原因
                $this->error($msg);

            }
        }
    }
}
?>