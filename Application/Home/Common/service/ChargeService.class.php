<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 2017/1/22
 * Time: 20:37
 */

namespace Home\Common\Service;

use Home\Common\DAO;


class ChargeService
{
    private $schoolid;
    private $items;

    public function __construct($schoolid)
    {
        $this->setSchoolID($schoolid);
    }

    public function setSchoolID($id)
    {
        $this->schoolid = $id;
        $this->items = array();
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
        if(isset($this->items[$size]))
            return  $this->items[$size]['price'];
        else
            return -1;
    }
    public function chargeTOString($size)
    {
        if(isset($this->items[$size]))
        {
            $str = $this->items[$size]['price']."元";
            $add = $this->items[$size]['addition'];
            if(""!= $add)
                $str.="($add)";
            return  $str;
        }
        else
            return "计价错误，请尝试重新下单";

    }

    /**获得快递大小描述
     * @return array
     */
    public  function getDescriptions()
    {
        $r = array_column($this->items,'description');
        return $r;
    }


    private function select()
    {
        $model = DAO\Models::M_fee();
        $data = $model->where("school_id = '?' and online = 1",$this->schoolid)->select();

        if($data)
        {
            foreach ($data as $item)
            {
                $this->items[$item['size']] = $item;
            }
        }
    }
}