<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 2017/1/22
 * Time: 20:37
 */

namespace Home\Common\Service;

use Home\Common\DAO;


class ExpressService
{
    private static $schoolid=-1;
    private static $items;
    private static $companies;

    public function __construct($schoolid)
    {
        $this->setSchoolID($schoolid);
    }

    public function setSchoolID($id)
    {
        if(self::$schoolid===$id)
            return;

        self::$schoolid = $id;
        self::$items = array();
        self::$companies = array();
        $this->select();
        return $this;
    }



    /**计费
     * @param $size
     * @return int
     *
     */
    public function charge($size)
    {
        if(isset(self::$items[$size]))
            return  self::$items[$size]['price'];
        else
            return false;
    }

    public function chargeTOString($size)
    {
        if(isset(self::$items[$size]))
        {
            $str = self::$items[$size]['price']."元";
            $add = self::$items[$size]['addition'];
            if(""!= $add)
                $str.="($add)";
            return  $str;
        }
        else
            return "计价错误，请尝试重新下单";

    }

    /**获得快递大小描述集合
     * @return array
     */
    public function getDescriptions()
    {
        $r = array_column(self::$items,'description');
        return $r;
    }
    public function getDescription($size)
    {
        if(isset(self::$items[$size]))
            return self::$items[$size];
        else
            return false;
    }

    /**获得快递公司集合
     * @return array
     */
    public function getCompanys()
    {
        $r = array_column(self::$companies,'express_company_name');
        return $r;
    }

    /**判断快递公司是否存在
     * @param $name
     * @return bool
     */
    public function isCompanyExist($name)
    {
        return isset(self::$companies[$name]);
    }

    private function select()
    {
        $model = DAO\Models::M_fee();
        $data = $model->where("school_id = '%s' and online = 1",self::$schoolid)->select();

        if($data)
        {
            foreach ($data as $item)
            {
                self::$items[$item['size']] = $item;
            }
        }

        $data = array();
        $model = DAO\Models::M_express_company();
        $data = $model->where("school_id = '%s' and online = 1",self::$schoolid)->order(array('order'))->select();
        if($data)
        {
            foreach ($data as $item)
            {
                self::$companies[$item['express_company_name']] = $item;
            }
        }


    }
}