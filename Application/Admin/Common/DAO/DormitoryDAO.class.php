<?php
/**
 * Created by PhpStorm.
 * Author: ben
 * Date: 20/01/2017
 * Time: 11:26
 * Descroption:
 *  Dormitory DAO
 */

namespace Admin\Common\DAO;

class DormitoryDAO
{
    private $M_Dormitory;
    private $dormitories;
    private $schoolid;
    private $noErr;

    //errors
    const E_FAIL = 0;
    const E_OK = 1;
    const E_NotFound = 2;
    const E_NoChange = 3;

    public function __construct($schooid)
    {
        $this->clear();
        $this->setSchoolID($schooid);
    }

    /**设置学校id，获得寝室信息
     * @param $schoolid
     * @return int
     */
    public function setSchoolID($schoolid)
    {
        $this->schoolid = $schoolid;

        $model = $this->M_Dormitory;
        $data = $model->where("school_id = '%s'",$schoolid)->order(array('order'))->select();
        if($data)
        {
            $this->dormitories = array();
            foreach($data as $v)
            {
                $index = $v['dormitory_id'];
                $this->dormitories[$index] = $v;
            }

//            print_r($this->dormitories);
            return self::E_OK;
        }
        else
        {
            return self::E_NotFound;
        }
    }

    public function setAddress($dorID,$address)
    {
        $this->dormitories[$dorID]['dormitory_address'] = $address;
        return $this;
    }

    public function Online($dorID)
    {
        $this->dormitories[$dorID]['online'] = 1;
        return $this;
    }

    public function Offline($dorID)
    {
        $this->dormitories[$dorID]['online'] = 0;
        return $this;
    }

    public function setOfflineMsg($dorID,$msg)
    {
        $this->dormitories[$dorID]['offline_msg'] = $msg;
        return $this;
    }

    public function addDormitory($address)
    {
        if(!isset($this->schoolid))
            return self::E_FAIL;

        $model = $this->M_Dormitory;
        $dor = array(
            'dormitory_address'=>$address,
            'school_id'=>$this->schoolid,
            'online'=>0
        );

        $result = $model->data($dor)->add();

        if($result)
        {
            $dor['dormitory_id'] = $result;
            $this->dormitories[$result] = $dor;
            print_r($this->dormitories);
            return self::E_OK;
        }
        else
        {
            return self::E_FAIL;
        }
    }

    /**更新数据库
     * @return bool
     */
    public function update()
    {
        foreach($this->dormitories as $dor)
        {
            $r = $this->M_Dormitory->save($dor);
            if($r===false)
                return false;
        }
        return true;
    }

    public function get()
    {
        return $this->dormitories;
    }

    public function clear()
    {
        $this->M_Dormitory = M('dormitory');
        $this->noErr = false;
    }
}























