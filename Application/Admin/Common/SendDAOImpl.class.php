<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/10/23
 * Time: 16:48
 */
namespace Admin\Common;
import("Vendor.PHPExcel.PHPExcel",null,".php");
class SendDAOImpl implements ISendDAO{

    //获得代寄件
    public function get($param){
        $school = session("admin_school");
        $model = M('send_view');

        $return_data['draw'] = $param["draw"];
        $return_data['recordsTotal'] = $model->count();
        $return_data['recordsFiltered'] = $model->count();

        //获取订单
        $return_data['data'] = $model->where("school_id='$school'")->select();
        foreach($return_data['data'] as $key=>$value){
            $return_data['data'][$key]['look'] = "<img src='".__ROOT__."/Public/assets/advanced-datatable/examples/examples_support/details_open.png'>";
        }
        return $return_data;
    }

    //下载今日4点半前的数据，导出excel
    public function export(){

        $school = session("admin_school");

        $excel = new \PHPExcel();
        //Excel表格式,这里简略写了8列
        $letter = array('A','B','C','D','E','F','G','H');
        //表头数组
        $tableheader = array('订单号','姓名','手机号码','寝室','寄件物品','备注','下单时间','状态');
        //填充表头信息
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }

        //表格数组
        $model = D('send_view');

        $tomorrow = mktime(0,0,0,date("m"),date("d")-1,date("Y"));
        $today_begin = date("Y-m-d", $tomorrow)." 17:00;00";

        $date = date('Y-m-d');
        $today_end = $date." 17:00:00";
        
        $data = $model->where("school_id='$school' and time<='$today_end' and time>='$today_begin'")->getField("send_id,sender_name,sender_phone,dormitory_address,sender_goods,
            remarks,time,sender_status",true);

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
        /* for ($i = 2;$i <= count($data) + 1;$i++) {
              $j = 0;
              foreach ($data[$i - 2] as $key=>$value) {
                  $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                  $j++;
              }
          }*/

        $write = new \PHPExcel_Writer_Excel5($excel);
        header("Content-Type:text/xml");
        header("Content-Disposition:attachment;filename='data.xls'");
        $write->save('php://output');
    }
}