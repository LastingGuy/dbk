<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 03/02/2017
 * Time: 13:53
 */

namespace Home\Common\Objects;


use Home\Common\DAO\DormitoryDAO;
use Home\Common\DAO\SchoolDAO;

class SendOrder
{
    private $sendID;
    private $sendNo;
    private $userID;
    private $sender_name;
    private $sender_phone;
    private $dormitory_id;
    private $dormitory_address;
    private $recv_name;
    private $recv_phone;
    private $goods;
    private $destination;
    private $remarks;
    private $time;
    private $status;
    private $school_name;
    private $school_id;

    /**
     * @return mixed
     */
    public function getSendID()
    {
        return $this->sendID;
    }

    /**
     * @param $sendID
     * @return $this
     */
    public function setSendID($sendID)
    {
        $this->sendID = $sendID;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSendNo()
    {
        return $this->sendNo;
    }

    /**
     * @param $sendNo
     * @return $this
     */
    public function setSendNo($sendNo)
    {
        $this->sendNo = $sendNo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param $userID
     * @return $this
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSenderName()
    {
        return $this->sender_name;
    }

    /**
     * @param $sender_name
     * @return $this
     */
    public function setSenderName($sender_name)
    {
        $this->sender_name = $sender_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSenderPhone()
    {
        return $this->sender_phone;
    }

    /**
     * @param $sender_phone
     * @return $this
     */
    public function setSenderPhone($sender_phone)
    {
        $this->sender_phone = $sender_phone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDormitoryId()
    {
        return $this->dormitory_id;
    }

    /**
     * @param $dormitory_id
     * @return $this
     */
    public function setDormitoryId($dormitory_id)
    {
        $this->dormitory_id = $dormitory_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecvName()
    {
        return $this->recv_name;
    }

    /**
     * @param $recv_name
     * @return $this
     */
    public function setRecvName($recv_name)
    {
        $this->recv_name = $recv_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecvPhone()
    {
        return $this->recv_phone;
    }

    /**
     * @param $recv_phone
     * @return $this
     */
    public function setRecvPhone($recv_phone)
    {
        $this->recv_phone = $recv_phone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoods()
    {
        return $this->goods;
    }

    /**
     * @param $goods
     * @return $this
     */
    public function setGoods($goods)
    {
        $this->goods = $goods;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param $destination
     * @return $this
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param $remarks
     * @return $this
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param $time
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    public function setCurrentTime()
    {
        $this->time = date("Y-m-d H:i:s");
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getDormitoryAddress()
    {
        return $this->dormitory_address;
    }

    /**
     * @param mixed $dormitory_address
     * @return SendOrder
     */
    public function setDormitoryAddress($dormitory_address)
    {
        $this->dormitory_address = $dormitory_address;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSchoolName()
    {
        return $this->school_name;
    }

    /**
     * @param mixed $school_name
     * @return SendOrder
     */
    public function setSchoolName($school_name)
    {
        $this->school_name = $school_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSchoolId()
    {
        return $this->school_id;
    }

    /**
     * @param mixed $school_id
     * @return SendOrder
     */
    public function setSchoolId($school_id)
    {
        $this->school_id = $school_id;
        return $this;
    }

    public function checkUser()
    {
        return $this->userID!="";
    }

    public function checkSenderName()
    {
        return $this->sender_name!="";
    }

    public function checkSenderPhone()
    {
        return isMobile($this->sender_phone);
    }

    public function checkRecvName()
    {
        return $this->recv_name!="";
    }

    public function checkRecvPhone()
    {
        return $this->recv_phone!="";
    }

    public function checkGoods()
    {
        return $this->goods!="";
    }

    /**检测学校是否存在
     * @return bool
     */
    public function checkSchoolName()
    {
        if(!$this->school_name)
            return false;

        $schooldao = new SchoolDAO();
        $data = $schooldao->getSchoolInfoByName($this->school_name);

        if($data)
        {
            $this->school_id = $data['school_id'];
            return true;
        }
        else
            return false;
    }

    public function checkDormitory()
    {
        if(!$this->dormitory_address)
            return false;
        $dao = new DormitoryDAO();
        $data = $dao->getDormitoryInfoByAddress($this->school_id,$this->dormitory_address);

        if($data)
        {
            $this->dormitory_id = $data['dormitory_id'];
            return true;
        }
        else
            return false;
    }

    public function checkDestination()
    {
        return $this->destination!="";
    }



}


























