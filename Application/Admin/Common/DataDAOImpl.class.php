<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/11/3
 * Time: 21:56
 */
namespace Admin\Common;
class DataDAOImpl{

    //获取所有的收件订单数量
    public function getNrOfPickupOrders(){
        $model = M('pickup');

        $number = $model->where("express_status<100")->count();

        return $number;
    }

    //获取今天的收件订单数量
    public function getTodayNrOfPickupOrders(){
        $model = M('pickup');

        //设置时间
        $tomorrow = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
        $today_begin = date("Y-m-d", $tomorrow)." 16:00;00";

        $date = date('Y-m-d');
        $today_end = $date." 16:00:00";

        $number = $model->where("express_status<100 and time<='$today_end' and time>'$today_begin'")->count();

        return $number;
    }

    //获取所有的寄件订单数量
    public function getNrOfSendOrders(){
        $model = M('send');

        $number = $model->count();

        return $number;
    }

    //获取今天的寄件订单数
    public function getTodayNrOfSendOrders(){
        $model = M('send');

        //设置时间
        $tomorrow = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
        $today_begin = date("Y-m-d", $tomorrow)." 16:00;00";

        $date = date('Y-m-d');
        $today_end = $date." 16:00:00";

        $number = $model->where("time<='$today_end' and time>'$today_begin'")->count();

        return $number;
    }
}
