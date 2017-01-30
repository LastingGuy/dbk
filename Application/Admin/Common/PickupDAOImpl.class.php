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
            $return_data['recordsTotal'] = $model->where("school_id='$school' and (express_status=2 or express_status=3 or express_status=8 or express_status=9) and (receiver_name like '$search%' or receiver_phone like '$search%' or pickup_no like '$search%')")->count();
            $return_data['recordsFiltered'] = $return_data['recordsTotal'];
            //获取订单
            $return_data['data'] = $model->where("school_id='$school' and (express_status=2 or express_status=3 or express_status=8 or express_status=9) and (receiver_name like '$search%' or receiver_phone like '$search%' or pickup_no like '$search%')")->order("pickup_id desc")->limit($param['start'],$param['length'])->select();

        }else{
            $return_data['recordsTotal'] = $model->where("school_id='$school' and (express_status=2 or express_status=3 or express_status=8 or express_status=9)")->count();
            $return_data['recordsFiltered'] = $return_data['recordsTotal'];
            //获取订单
            $return_data['data'] = $model->where("school_id='$school' and (express_status=2 or express_status=3 or express_status=8 or express_status=9)")->order("pickup_id desc")->limit($param['start'],$param['length'])->select();

        }



        foreach($return_data['data'] as $key=>$value){
            $return_data['data'][$key]['edit'] = "";

            if($return_data['data'][$key]['express_status'] == 2){
                $return_data['data'][$key]['express_status'] = "<div style='color:red'>代 拿</div>";
            }
            else if($return_data['data'][$key]['express_status'] == 8){
                $return_data['data'][$key]['express_status'] = "<div style='color:red'>遗留中</div>";
            }
            else if($return_data['data'][$key]['express_status'] == 9){
                $return_data['data'][$key]['express_status'] = "<div style='color:red'>失败中</div>";
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
        //记录流水
        FlowRecord::exportPickUpOrders_today();

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

        $data = $model->where("school_id='$school' and time_end<='$today_end' and time_end>'$today_begin' and express_status=2 ")->getField("pickup_no,receiver_name,receiver_phone,express_company,express_type,
            pay_fee,dormitory_address,express_sms,express_code,remarks,time_end,express_status",true);

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
        //记录流水
        FlowRecord::exportPickOrders_UserDefine($begin,$end);

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

        $data = $model->where("school_id='$school' and time_end<='$end' and time_end>'$begin' and express_status=2")->getField("pickup_id,receiver_name,receiver_phone,express_company,express_type,
            pay_fee,dormitory_address,express_sms,express_code,remarks,time_end,express_status",true);

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
    public function updateStatus($pickup_no, $status){

        $model = M("pickup");
        $data['express_status'] = $status;
        $model->where("pickup_no=$pickup_no")->save($data);
    }

    //完成指定时间内的订单
    public function completeDuringTheTime($begin_time, $end_time){
        $object = M();
        $school_id = session("admin_school");
        $object->execute("update dbk_pickup_view set express_status=3 where time_end>='$begin_time' and time_end<='$end_time' and school_id=$school_id");
        //记录流水
        FlowRecord::completeAll_PickUp($begin_time,$end_time);
        return 1;
    }

    //未完成指定时间内的订单
    public function uncompleteDuringTheTime($begin_time, $end_time){
        $object = M();
        $school_id = session("admin_school");
        $object->execute("update dbk_pickup_view set express_status=2 where time_end>='$begin_time' and time_end<='$end_time' and school_id=$school_id");
        FlowRecord::unfinishedAll_PickUp($begin_time, $end_time);
        return 1;
    }
}