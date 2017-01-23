<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 2017/1/22
 * Time: 18:57
 */

namespace Home\Common\Objects;


use Home\Common\DAO\DormitoryDAO;
use Home\Common\DAO\SchoolDAO;
use Home\Common\Service\ChargeService;
use Home\Common\Service\ExpressService;

class PickupOrder
{

    private $_pickup_id;
    private $_pickup_no;
    private $_user_id;


    ///////////School///////////
    private $_school_name;
    private $_school_id;

    /**检测学校是否存在
     * @return bool
     */
    public function checkSchoolName()
    {
        if(!$this->_school_name)
            return false;

        $schooldao = new SchoolDAO();
        $data = $schooldao->getSchoolInfoByName($this->_school_name);

        if($data)
        {
            $this->_school_id = $data['school_id'];
            return true;
        }
        else
            return false;
    }

    /**检测学校是否存在
     * @return bool
     */
    public function checkSchoolId()
    {
        if(!$this->_school_id)
            return false;

        $schooldao = new SchoolDAO();
        $data =$schooldao->getSchoolInfoById($this->_school_id);

        if($data)
        {
            $this->_school_name = $data['school_id'];
            return true;
        }
        else
            return false;
    }



    ////////////////dormitory///////
    private $_dormitory_id = -1;
    private $_dormitory_address;

    /**检测寝室是否存在
     * @return bool
     */
    public function checkDorID()
    {
        if(!$this->_dormitory_id)
            return false;
        $dordao = new DormitoryDAO();
        $data = $dordao->getDormitoryInfoById($this->_dormitory_id);

        if($data)
        {
            $this->_dormitory_address = $data['dormitory_address'];
            return true;
        }
        else
            return false;
    }

    /**检测寝室是否存在
     * @return bool
     */
    public function checkDorAddress()
    {
        if(!$this->_dormitory_address)
            return false;
        $dao = new DormitoryDAO();
        $data = $dao->getDormitoryInfoByAddress($this->_school_id,$this->_dormitory_address);

        if($data)
        {
            $this->_dormitory_id = $data['dormitory_id'];
            return true;
        }
        else
            return false;
    }


    /////////////////////receiver///////////
    private $_receiver_name;
    private $_receiver_phone;

    /**检查姓名是否为空
     * @return bool
     */
    public function checkReceiverName()
    {
        if($this->_receiver_name)
            return true;
        else
            return false;
    }

    /**检查联系电话是否合法
     * @return bool
     */
    public function  checkReceiverPhone()
    {
        return isMobile($this->_receiver_phone);
    }


    ///////////////////express/////////
    private $_express_type_size;
    private $_express_type;
    private $_express_company;
    private $_express_sms;
    private $_express_code;
    private $_price;

    /**检测快递类型合法
     * @return bool
     */
    public function checkExpressSize()
    {
        if(!$this->_express_type_size)
            return false;

        $express = new ExpressService($this->_school_id);
        $this->_express_type = $express->getDescription($this->_express_type_size);
        $this->_price = $express->charge($this->_express_type_size);
        if($this->_price===false)
            return false;
        else
            return true;
    }

    /**判断快递公司是否合法
     * @return bool
     */
    public function checkExpressCompany()
    {
        if(!$this->_express_company)
            return false;

        $express = new ExpressService($this->checkSchoolId());
        return $express->isCompanyExist($this->_express_company);
    }

    /**判断快递短信是否合法
     * @return bool
     */
    public function checkSMS()
    {
        if($this->_express_sms)
            return true;
        else
            return false;
    }

    /**判断提取码是否合法
     * @return bool
     */
    public function checkExpressCode()
    {
        if($this->_express_code)
            return true;
        else
            return false;
    }


    private $_remarks;
    private $_orderTime;
    private $_status;




    public function __construct($order)
    {
        $this->_school_name = false;
        $this->_school_id = false;
        $this->_pickup_id = false;
        $this->_pickup_no = false;
        $this->_user_id = false;
        $this->_dormitory_id = false;
        $this->_dormitory_address = false;
        $this->_receiver_name = false;
        $this->_receiver_phone = false;
        $this->_express_type = false;
        $this->_express_type_size = false;
        $this->_express_code = false;
        $this->_express_company = false;
        $this->_express_sms = false;
        $this->_price = false;



        if(isset($order['pickup_id']))
            $this->_pickup_id = $order['pickup_id'];

        if(isset($order['pickup_no']))
            $this->_pickup_no = $order['pickup_no'];

        if(isset($order['user_id']))
            $this->_user_id = $order['user_id'];

        if(isset($order['school_name']))
            $this->_school_name = $order['school_name'];

        if(isset($order['school_id']))
            $this->_school_id = $order['school_id'];

        if(isset($order['dormitory_id']))
            $this->_dormitory_id = $order['dormitory_id'];

        if(isset($order['dormitory_address']))
            $this->_dormitory_address = $order['dormitory_address'];

        if(isset($order['receiver_name']))
            $this->_receiver_name = $order['receiver_name'];

        if(isset($order['receiver_phone']))
            $this->_receiver_phone = $order['receiver_phone'];

        if(isset($order['express_type_size']))
            $this->_express_type_size = $order['express_type_size'];

        if(isset($order['express_type']))
            $this->_express_type = $order['express_type'];

        if(isset($order['express_company']))
            $this->_express_company = $order['express_company'];

        if(isset($order['express_sms']))
            $this->_express_sms = $order['express_sms'];

        if(isset($order['express_code']))
            $this->_express_code = $order['express_code'];

        if(isset($order['remarks']))
            $this->_remarks = $order['remarks'];

        if(isset($order['order_time']))
            $this->_orderTime = $order['order_time'];

        if(isset($order['status']))
            $this->_status = $order['status'];

    }

}