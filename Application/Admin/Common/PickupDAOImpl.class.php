<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/10/23
 * Time: 13:55
 */

namespace Admin\Common;
import("Vendor.PHPExcel.PHPExcel",null,".php");

class PickupDAOImpl implements IPickupDAO{
    //获得代收件
    public function get($param){
        $school = session("admin_school");
        $model = M('pickup_view');

        $return_data['draw'] = $param["draw"];
        $search = $param['search']['value'];
        if($search!=null){
            $return_data['recordsTotal'] = $model->where("school_id='$school' and express_status>=2 and express_status<=3 and (receiver_name like '$search%' or receiver_phone like '$search%')")->count();
            $return_data['recordsFiltered'] = $return_data['recordsTotal'];
            //获取订单
            $return_data['data'] = $model->where("school_id='$school' and express_status>=2 and express_status<=3 and (receiver_name like '$search%' or receiver_phone like '$search%')")->order("pickup_id desc")->limit($param['start'],$param['length'])->select();

        }else{
            $return_data['recordsTotal'] = $model->where("school_id='$school' and express_status>=2 and express_status<=3")->count();
            $return_data['recordsFiltered'] = $return_data['recordsTotal'];
            //获取订单
            $return_data['data'] = $model->where("school_id='$school' and express_status>=2 and express_status<=3")->order("pickup_id desc")->limit($param['start'],$param['length'])->select();

        }



        foreach($return_data['data'] as $key=>$value){
            if($return_data['data'][$key]['express_status'] == 2)
            {
                $return_data['data'][$key]['edit'] = "<a class=\" complete \" href=\"javascript:;\"><span class=\"label label-success\">完 成</span></a>";
            }
            elseif ($return_data['data'][$key]['express_status'] == 3)
            {
                $return_data['data'][$key]['edit'] = "<a class=\" complete \" href=\"javascript:;\"><span class=\"label label-danger\">未完成</span></a>";
            }

            if($return_data['data'][$key]['express_status'] == 2){
                $return_data['data'][$key]['express_status'] = "<div style='color:red'>进行中</div>";
            }
            else if($return_data['data'][$key]['express_status'] == 3){
                $return_data['data'][$key]['express_status'] = "<div style='color:lightseagreen'>已完成</div>";
            }
            $return_data['data'][$key]['look'] = "<img src='".__ROOT__."/Public/assets/advanced-datatable/examples/examples_support/details_open.png'>";
        }
        return $return_data;
    }

    //下载今日4点半前的数据和昨天4点半以后的数据，导出excel
    public function export(){
        $school = session("admin_school");

        $excel = new \PHPExcel();
        //Excel表格式,这里简略写了8列
        $letter = array('A','B','C','D','E','F','G','F','I','H','J','K');
        //表头数组
        $tableheader = array('订单号','姓名','手机号码','快递公司','快递类型','快递价格','寝 室','快递短信','取件码/货架号/手机号','备注','订单时间','状态');
        //填充表头信息
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }

        //表格数组
        $model = D('pickup_view');

        $tomorrow = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
        $today_begin = date("Y-m-d", $tomorrow)." 16:00;00";

        $date = date('Y-m-d');
        $today_end = $date." 16:00:00";

        $data = $model->where("school_id='$school' and pay_time<='$today_end' and pay_time>'$today_begin' and express_status=2 ")->getField("pickup_id,receiver_name,receiver_phone,express_company,express_type,
            price,dormitory_address,express_sms,express_code,remarks,pay_time,express_status",true);

        //填充表格信息
        $i = 2;
        foreach($data as $key => $value){
            $j = 0;

            foreach ($value as $key2=>$value2){
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value2");
                $j++;
            }
            $i++;
        }

        $write = new \PHPExcel_Writer_Excel5($excel);
        header("Content-Type:text/xls");
        header("Content-Disposition:attachment;filename=data.xls");
        $write->save('php://output');
    }

    //根据自定义时间下载
    public function exportUserDefined($begin, $end){

        $school = session("admin_school");

        $excel = new \PHPExcel();
        //Excel表格式,这里简略写了8列
        $letter = array('A','B','C','D','E','F','G','F','I','H','J','K');
        //表头数组
        $tableheader = array('订单号','姓名','手机号码','快递公司','快递类型','快递价格','寝 室','快递短信','取件码/货架号/手机号','备注','订单时间','状态');
        //填充表头信息
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }

        //表格数组
        $model = D('pickup_view');

        $data = $model->where("school_id='$school' and pay_time<='$end' and pay_time>'$begin' and express_status=2")->getField("pickup_id,receiver_name,receiver_phone,express_company,express_type,
            price,dormitory_address,express_sms,express_code,remarks,pay_time,express_status",true);

        //填充表格信息
        $i = 2;
        foreach($data as $key => $value){
            $j = 0;

            foreach ($value as $key2=>$value2){
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value2");
                $j++;
            }
            $i++;
        }

        $write = new \PHPExcel_Writer_Excel5($excel);
        header("Content-Type:text/xml");
        header("Content-Disposition:attachment;filename='data.xls'");
        $write->save('php://output');
    }

    //更新订单状态
    public function updateStatus($pickup_id){
        $model = M("pickup");
        $data['pickup_id'] = $pickup_id;
        $express_status = $model->where("pickup_id=$pickup_id")->getField('express_status');
        if($express_status==2){
            $data['express_status'] = 3;
            $model->save($data);
            return 1;
        }
        else if($express_status==3){
            $data['express_status'] = 2;
            $model->save($data);
            return 2;
        }

    }

    //完成指定时间内的订单
    public function completeDuringTheTime($begin_time, $end_time){
        $object = M();
        $school_id = session("admin_school");
        $object->execute("update dbk_pickup_view set express_status=3 where pay_time>='$begin_time' and pay_time<='$end_time' and school_id=$school_id");
        return 1;
    }

    //未完成指定时间内的订单
    public function uncompleteDuringTheTime($begin_time, $end_time){
        $object = M();
        $school_id = session("admin_school");
        $object->execute("update dbk_pickup_view set express_status=2 where pay_time>='$begin_time' and pay_time<='$end_time' and school_id=$school_id");
        return 1;
    }
}