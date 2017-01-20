<?php
/**
 * Author: ben
 * Date: 19/01/2017
 * Time: 14:18
 * Descrition:
 *  学校管理
 */

namespace Admin\Common\Management;

use Admin\Common;
class SchoolManagement
{
    /**学校上线
     * @param $schoolid
     * @return bool
     */
    public static function School_online($schoolid)
    {
        return self::School_SET(self::_School_Online,$schoolid);

    }

    /**学校下线
     * @param $schoolid
     * @return bool
     */
    public static function School_offline($schoolid)
    {
        return self::School_SET(self::_School_Offline,$schoolid);
    }

    /**设置下线通知
     * @param $schoolid
     * @param $msg
     * @return bool
     */
    public static function School_setOfflineMsg($schoolid,$msg)
    {
        return self::School_SET(self::_School_SetOfflineMSG,$schoolid,$msg);
    }

    /**设置学校公告
     * @param $schoolid
     * @param $msg
     * @return bool
     */
    public static function School_setSchoolMsg($schoolid,$msg)
    {
        return self::School_SET(self::_School_SetSchoolMsg,$schoolid,$msg);
    }

    /**设置学校名称
     * @param $schooid
     * @param $name
     * @return bool
     */
    public static function School_setName($schooid,$name)
    {
        return self::School_SET(self::_School_SetName,$schooid,$name);
    }

    /**设置学校所在城市
     * @param $schoolid
     * @param $city
     * @return bool
     */
    public static function School_setCity($schoolid,$city)
    {
        return self::School_SET(self::_School_SetCity,$schoolid,$city);
    }

    /**添加学校
     * @param $name
     * @param $city
     * @return bool
     */
    public static function School_add($name,$city)
    {
        $school = new Common\DAO\SchoolDAO();
        $school->setName($name)->setCity($city)->Offline()->hide();
        if($school->insert()==$school::E_OK)
            return true;
        else
            return false;
    }

    const _School_Online = 1;
    const _School_Offline = 2;
    const _School_SetOfflineMSG =3;
    const _School_SetSchoolMsg = 4;
    const _School_SetName = 5;
    const _School_SetCity = 6;
    private static function School_SET($kind,$schoolid,$value=false)
    {
        $school = new Common\DAO\SchoolDAO();
        if($school->findByID($schoolid)==$school::E_NotFound)
        {
            return false;
        }

        switch ($kind)
        {
            case self::_School_Offline:
                $school->Offline();
                break;
            case self::_School_Online:
                $school->Online();
                break;
            case self::_School_SetOfflineMSG:
                $school->setOfflineMsg($value);
                break;
            case self::_School_SetSchoolMsg:
                $school->setSchoolMsg($value);
                break;
            case self::_School_SetName:
                $school->setName($value);
                break;
            case self::_School_SetCity:
                $school->setCity($value);
                break;
        }

        if($school->update()==$school::E_OK)
        {
            return true;
        }
        else
            return false;
    }

}