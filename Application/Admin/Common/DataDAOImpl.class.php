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
    public function getNrOfPickupOrders($school){
        $model = M('pickup_view');
        $number = null;
        if($school!=null) {
            $number = $model->where("express_status=2 or express_status=3 and school_id='$school'")->count();
        }
        else{
            $number = $model->where("express_status=2 or express_status=3")->count();
        }
        return $number;
    }

    //获取今天的收件订单数量
    public function getTodayNrOfPickupOrders($school){
        $model = M('pickup_view');

        //设置时间
        $tomorrow = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
        $today_begin = date("Y-m-d", $tomorrow)." 16:00;00";

        $date = date('Y-m-d');
        $today_end = $date." 16:00:00";

        if($school!=null){
            $number = $model->where("school_id='$school' and express_status=2 or express_status=3 and time<='$today_end' and time>'$today_begin'")->count();
        }
        else{
            $number = $model->where("express_status=2 or express_status=3 and time<='$today_end' and time>'$today_begin'")->count();
        }


        return $number;
    }

    //获取所有的寄件订单数量
    public function getNrOfSendOrders($school){
        $model = M('send_view');
        if($school!=null){
            $number = $model->where("school_id='$school'")->count();
        }
        else{
            $number = $model->count();
        }

        return $number;
    }

    //获取今天的寄件订单数
    public function getTodayNrOfSendOrders($school){
        $model = M('send_view');

        //设置时间
        $tomorrow = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
        $today_begin = date("Y-m-d", $tomorrow)." 16:00;00";

        $date = date('Y-m-d');
        $today_end = $date." 16:00:00";

        if($school!=null){
            $number = $model->where("school_id='$school'and time<='$today_end' and time>'$today_begin'")->count();
        }
        else{
            $number = $model->where("time<='$today_end' and time>'$today_begin'")->count();
        }

        return $number;
    }

    //获取所有城市
    public function getCity(){
        $model = D('school');
        $city = $model->field('school_city')->group('school_city')->select();
        return $city;
    }

    //根据城市获取学校
    public function getSchoolByCity($city){
        $model = D('school');
        $school = $model->where("school_city='$city'")->select();
        return $school;
    }
}
