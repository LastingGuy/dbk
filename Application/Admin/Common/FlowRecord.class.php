<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 2016/11/30
 * Time: 下午4:31
 * Description:
 *  用于记录流水
 */

namespace Admin\Common;


class FlowRecord
{
    //操作代码
    const tLogin = 1;    //登录
    const tLogout = 2;   //登出

    const tSetComplete_PickUp = 3;  //设置代取订单为完成
    const tSetComplete_Send =4;     //设置代寄订单为完成
    const tCompleteALL_PickUp = 5;  //批量完成
    const tCompleteALL_Send= 6;    //批量完成

    const tSetUnFinished_PickUP = 7; //设置为未完成
    const tSetUnFinished_Send = 8;   //设置为未完成
    const tUnFinishedALL_PickUp=9;   //批量设置为未完成
    const tUnFinishedALL_Send = 10;  //批量设置为未完成

    const tExportPickUpOrder_Today = 11;  //导出今日代取订单
    const tExportPickUpOrder_UserDefine = 12;     //导出规定时间代取订单
    const tExportSendOrder_Today = 13;    //导出今日代寄订单
    const tExportSendOrder_UserDefine = 14;  //导出规定时间代寄订单


    const tShowPickUpOrders = 15;   //查看代取订单
    const tShowSendOrders = 16;     //查看代寄订单


    //Operation Name
    const OperationName = array(
        self::tLogin=>'登录',
        self::tLogout=>'登出',
        self::tSetComplete_PickUp=>'设置代取订单为完成',
        self::tSetComplete_Send=>'设置代寄订单为完成',
        self::tCompleteALL_PickUp=>'批量完成代取订单',
        self::tCompleteALL_Send=>'批量完成为代寄订单',
        self::tSetUnFinished_PickUP=>'设置为未完成(代取)',
        self::tSetUnFinished_Send=>'设置为未完成(代寄)',
        self::tUnFinishedALL_PickUp=>'批量设置为未完成(代取)',
        self::tUnFinishedALL_Send=>'批量设置为未完成(代寄)',
        self::tExportPickUpOrder_Today=>'导出今日代取订单',
        self::tExportPickUpOrder_UserDefine=>'导出指定时段代取订单',
        self::tExportSendOrder_Today=>'导出今日代寄订单',
        self::tExportSendOrder_UserDefine=>'导出指定时段代寄订单',
        self::tShowPickUpOrders=>'查看代取订单',
        self::tShowSendOrders=>'查看代寄订单'
    );

    /**将操作写入数据库
     * @param $taskid
     * @param string $addition
     */
    private static function record($taskid,$addition="")
    {
        $data = array();
        if(session("admin_id")==null)
            $data['admin_id'] = 0;
        else
            $data['admin_id'] = session("admin_id");
        $data['task_id'] = $taskid;
        $data['addition'] = $addition;
        $data['date'] = date("Y-m-d H:i:s",time());
        $data['ip'] = self::getClientIP();
        $model = M('admin_flowrecord');
        $model->add($data);
    }



    /**获得ip
     * @return mixed
     */
    private static function getClientIP()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * 登录
     */
    public static function Login($addition="")
    {
        self::record(self::tLogin,$addition);
    }

    /**
     * 登出
     */
    public static function Logout($addition="")
    {
        self::record(self::tLogout,$addition);
    }

    /**点击完成代取订单
     * @param $orderid
     */
    public static function setComplete_PickUp($orderid,$addition="NULL")
    {
        $add = "orderID: ".$orderid."  Addition:".$addition;
        self::record(self::tSetComplete_PickUp,$add);
    }


    /**点击完成代寄订单
     * @param $orderid
     */
    public static function setComplete_Send($orderid,$addition="NULL")
    {
        $add = "orderID: ".$orderid."  Addition:".$addition;
        self::record(self::tSetComplete_Send,$add);
    }


    /**一键完成代取订单
     * @param $startStamp   开始时间戳
     * @param $endStamp     结束时间戳
     */
    public static function completeAll_PickUp($startStamp,$endStamp,$addition="NULL")
    {
        $schoolName = session("admin_school_name");
        $add = "School:$schoolName".
            "period: ".date("Y-m-d H:i:s",$startStamp).'-'.date("Y-m-d H:i:s",$endStamp).
            "  Addition:".$addition;
        self::record(self::tCompleteALL_PickUp,$add);
    }

    /**一键完成代寄订单
     * @param $startStamp
     * @param $endStamp
     */
    public static function completeAll_Send($startStamp,$endStamp,$addition="NULL")
    {
        $schoolName = session("admin_school_name");
        $add = "School: $schoolName".
            "period: ".date("Y-m-d H:i:s",$startStamp).'-'.date("Y-m-d H:i:s",$endStamp).
            "  Addition:".$addition;
        self::record(self::tCompleteALL_Send,$add);
    }


    /**设置未完成(代取订单)
     * @param $orderid
     */
    public static function setUnFinished_PickUp($orderid,$addition="NULL")
    {
        $add = "orderID: ".$orderid."  Addition:".$addition;
        self::record(self::tSetUnFinished_PickUP,$add);
    }


    /**设置未完成（代寄订单）
     * @param $orderid
     * @param string $addition
     */
    public static function  setUnFinished_Send($orderid,$addition="NULL")
    {
        $add = "orderID:".$orderid."  Addition:".$addition;
        self::record(self::tSetUnFinished_Send,$add);
    }


    /**一键未完成代取订单
     * @param $startStamp
     * @param $endStamp
     * @param string $addition
     */
    public static function unfinishedAll_PickUp($startStamp,$endStamp,$addition='NULL')
    {
        $schoolName = session("admin_school_name");
        $add = "School: $schoolName".
            "period: ".date("Y-m-d H:i:s",$startStamp)."-".date("Y-m-d H:i:s",$endStamp).
            "Addition: ".$addition;
        self::record(self::tUnFinishedALL_PickUp,$add);
    }


    /**一键未完成代寄订单
     * @param $startStamp
     * @param $endStamp
     * @param string $addition
     */
    public static function unfinishedAll_Send($startStamp,$endStamp,$addition="NULL")
    {
        $schoolName = session("admin_school_name");
        $add = "School: $schoolName".
            "period: ".date("Y-m-d H:i:s",$startStamp)."-".date("Y-m-d H:i:s",$endStamp)."Addition: ".$addition;
        self::record(self::tUnFinishedALL_Send,$add);
    }


    /**导出代取订单
     * @param $addition
     */
    public static function exportPickUpOrders_today($addition="NULL")
    {
        $schoolName = session("admin_school_name");
        $add = "School: $schoolName"."  Addition:$addition";
        self::record(self::tExportPickUpOrder_Today,$add);
    }


    /**导出指定时段代取订单
     * @param $startStamp
     * @param $endStamp
     * @param string $addition
     */
    public static function exportPickOrders_UserDefine($startStamp,$endStamp,$addition="NULL")
    {
        $schoolName = session("admin_school_name");
        $add = "School: $schoolName".
            "period: ".date("Y-m-d H:i:s",$startStamp).'-'.date("Y-m-d H:i:s",$endStamp).
            "Addition: $addition";
        self::record(self::tExportPickUpOrder_UserDefine,$add);
    }


    /**导出今日代寄订单
     * @param $addition
     */
    public static function exportSendOrders_today($addition="NULL")
    {
        $schoolName = session("admin_school_name");
        $add = "School: $schoolName"."  Addition:$addition";
        self::record(self::tExportSendOrder_Today,$add);
    }



    /**导出指定时段代取订单
     * @param $startStamp
     * @param $endStamp
     * @param string $addition
     */
    public static function exportSendOrders_UserDefine($startStamp,$endStamp,$addition="NULL")
    {
        $school = self::getSchool();
        $schoolName = $school['school_name'];
        $add = "School: $schoolName".
            "period: ".date("Y-m-d H:i:s",$startStamp).'-'.date("Y-m-d H:i:s",$endStamp).
            "Addition: $addition";
        self::record(self::tExportPickUpOrder_UserDefine,$add);
    }


    /**查询代取订单
     * @param string $addition
     */
    public static function showPickUpOrders($addition="NULL")
    {
        $school = self::getSchool();
        $schoolName = $school['school_name'];
        $add = "School: $schoolName".
            "Addition: $addition";
        self::record(self::tShowPickUpOrders,$add);
    }


    /**查询代寄订单
     * @param string $addition
     */
    public static function showSendOrders($addition="NULL")
    {
        $school = self::getSchool();
        $schoolName = $school['school_name'];
        $add = "School: $schoolName".
            "Addition: $addition";
        self::record(self::tShowSendOrders,$add);
    }






}