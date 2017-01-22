<?php
/**
 * Author: ben
 * Date: 19/01/2017
 * Time: 14:19
 * Description:
 *  dbk_school 操作
 */

namespace Admin\Common\DAO;

class SchoolDAO
{
    private $M_School;
    private $school;
    private $noErr;

    //errors
    const E_FAIL = 0;
    const E_OK = 1;
    const E_NotFound = 2;
    const E_NoChange = 3;

    public function __construct()
    {
        $this->M_School = M('school');
//        $this->school = array();
        $this->noErr = false;
    }

    /**设置school_id
     * @param $id
     * @return $this
     */
    public function setID($id)
    {
        $this->school['school_id'] = $id;

        return $this;
    }

    /**设置学校名
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->school['school_name'] = $name;

        return $this;
    }

    /**设置城市
     * @param $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->school['school_city'] = $city;

        return $this;
    }

    /**上线
     * @return $this
     */
    public function Online()
    {
        $this->school['online'] = 1;

        return $this;
    }

    /**下线
     * @return $this
     */
    public function Offline()
    {
        $this->school['online'] = 0;

        return $this;
    }

    /**设置下线公告
     * @param $msg
     * @return $this
     */
    public function setOfflineMSG($msg)
    {
        $this->school['offline_msg'] = $msg;

        return $this;
    }

    /**该学校可见
     * @return $this
     */
    public function display()
    {
        $this->school['display'] = 1;
        return $this;
    }

    /**隐藏此学校
     * @return $this
     */
    public function hide()
    {
        $this->school['display'] = 0;
        return $this;
    }

    /**设置学校公告
     * @param $msg
     * @return $this
     */
    public function setSchoolMsg($msg)
    {
        $this->school['msg'] = $msg;
        return $this;
    }

    /**通过名称寻找学校
     * @param bool $name
     * @return E_NotFound|array|mixed
     */
    public function findByName($name=false)
    {
        if(!$name)
        {
            $name = $this->school['school_name'];

            if(!isset($this->school))
            {
                $this->noErr = false;
                return self::E_NotFound;
            }
        }


        $school = $this->M_School->where("school_name = '%s'",$name)->find();
        if($school)
        {
            $this->noErr = true;
            $this->school = $school;
            return $school;
        }
        else
        {
            $this->noErr = false;
            return self::E_NotFound;
        }
    }

    /**通过ID寻找学校
     * @param bool $schoolID
     * @return E_NotFound|array|mixed
     */
    public function findByID($schoolID=false)
    {
        if(!$schoolID)
        {
            $schoolID = $this->school['school_id'];
            if(!isset($this->school))
            {
                $this->noErr = false;
                return self::E_NotFound;
            }
        }

        $school = $this->M_School->where("school_id = '%s'",$schoolID)->find();

        if($school)
        {
            $this->noErr = true;
            $this->school = $school;
            return $school;
        }
        else
        {
            $this->noErr = false;
            return self::E_NotFound;
        }
    }

    /**通过数组查询学校
     * @return E_NotFound|array|mixed
     */
    public function find()
    {

        $school = $this->school;

        if(isset($school))
        {
            $school = $this->M_School->where($school)->find();
        }

        if($school)
        {
            $this->noErr = true;
            $this->school = $school;
            return $school;
        }
        else
        {
            $this->noErr = false;
            return self::E_NotFound;
        }
    }

    public function clear()
    {
        unset($this->school);
        $this->noErr = false;
        return $this;
    }


    public function update()
    {
        if($this->noErr)
        {
            $r = $this->M_School->save($this->school);
            if($r>0)
                return self::E_OK;
            else
                return self::E_NoChange;
        }
        else
        {
            return self::E_FAIL;
        }
    }

    public function insert()
    {
        $model = $this->M_School;
        if(isset($this->school)) {
            $result = $model->field("school_name,school_city,online,offline_msg,display,msg")
                ->data($this->school)->add();
        }
        else
            return self::E_FAIL;

        if($result)
        {
            $this->findByID($result);
            return self::E_OK;
        }
        else
        {
            return self::E_FAIL;
        }

    }

}































