<?php
/**
 * Created by PhpStorm.
 * User: ben
 * Date: 20/01/2017
 * Time: 18:20
 * Description:
 *  寝室管理
 * Remark：
 *  设置显示顺序功能还未完成
 */
namespace Admin\Common\Management;

use Admin\Common\DAO;

class DormitoryManagement
{
    private $DorDAO;

    public function __construct($schoolid = false)
    {
        if(!$schoolid)
            $schoolid = session('admin_school');

        $this->DorDAO = new DAO\DormitoryDAO($schoolid);

    }

    /**设置寝室上线
     * @param $dorid
     * @return bool
     */
    public function Dor_Online($dorid)
    {
        return $this->SET(self::_Dor_Online,$dorid);
    }

    /**设置寝室下线
     * @param $dorid
     * @return bool
     */
    public function Dor_Offline($dorid)
    {
        return $this->SET(self::_Dor_Offline,$dorid);
    }

    /**设置下线通知
     * @param $dorid
     * @param $msg
     * @return bool
     */
    public function Dor_setOfflineMsg($dorid,$msg)
    {
        return $this->SET(self::_Dor_SetOfflineMSG,$dorid,$msg);
    }

    /**地址设置
     * @param $dorid
     * @param $address
     * @return bool
     */
    public function setAddress($dorid,$address)
    {
        return $this->SET(self::_Dor_SetAddress,$dorid,$address);
    }

    /**增加寝室
     * @param $address
     * @return bool
     */
    public function addDor($address)
    {
        $r = $this->DorDAO->addDormitory($address);
        if($r == DAO\DormitoryDAO::E_OK)
            return true;
        else
            return false;

    }


    const _Dor_Online = 1;
    const _Dor_Offline = 2;
    const _Dor_SetOfflineMSG =3;
    const _Dor_SetAddress = 4;
    private function SET($type,$dorid,$v = false)
    {
        switch ($type)
        {
            case self::_Dor_Online:
                $this->DorDAO->Online($dorid);
                break;
            case self::_Dor_Offline:
                $this->DorDAO->Offline($dorid);
                break;
            case self::_Dor_SetOfflineMSG:
                $this->DorDAO->setOfflineMsg($dorid,$v);
                break;
            case self::_Dor_SetAddress:
                $this->DorDAO->setAddress($dorid,$v);
                break;

        }

        $r = $this->DorDAO->update();
        if($r == DAO\DormitoryDAO::E_OK)
            return ture;
        else
            return false;
    }
}